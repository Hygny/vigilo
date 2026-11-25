#!/usr/bin/env bash
#
# Backup diário do MySQL do Vigilo, chamado pelo cron do VPS:
#   1) mysqldump de todas as bases do container data-mysql-1 (hoje só `vigilo`)
#      com --single-transaction: snapshot consistente, sem travar as tabelas
#      enquanto fila/scheduler escrevem;
#   2) valida (gzip íntegro + última linha "-- Dump completed") e só então troca
#      atômica para ~/backups/db-AAAA-MM-DD.sql.gz (permissão 600);
#   3) cópia FORA do VPS: manda para a VM da base CNPJ (verbo `guardar-backup`
#      do gateway.sh, chave dedicada ~/.ssh/cnpj_db — a mesma do reimport) e
#      confere o sha256 que a VM devolve; até 3 tentativas (recusa definitiva
#      da VM — ex.: já guarda o do dia com outro conteúdo — não re-tenta);
#   4) rotação local: 30 mais recentes + o 1º de cada um dos 12 últimos meses
#      (a VM guarda 90 + 24 — ver cnpj-db/bin/guardar-backup.sh). Só arquivos
#      db-AAAA-MM-DD.sql.gz: a subpasta ~/backups/solradar (backup de OUTRO app)
#      nunca é tocada.
#
# Sai ≠ 0 se o dump falhar (nada é trocado nem apagado) ou se a cópia fora não
# se confirmar (o backup local fica). Sem CNPJ_REMOTE_SSH no ~/apps/vigilo/.env,
# só faz o backup local.
#
#   backup-db.sh                       rotina diária
#   backup-db.sh --enviar-existentes   só manda à VM os backups locais que já
#                                      existem (semente da cópia fora; reenviar
#                                      é idempotente)
#
# Um backup por dia na VM: rodar à mão de novo no mesmo dia troca o local, mas a
# VM mantém o 1º do dia (nunca sobrescreve) e o envio sai como recusado.
#
# Cron (VPS, todo dia 03:00):
#   0 3 * * * /usr/bin/bash ~/apps/vigilo/src/deploy/backup-db.sh >> ~/apps/vigilo/backup-db.log 2>&1
set -euo pipefail
export LC_ALL=C

APP_DIR="${VIGILO_APP_DIR:-$HOME/apps/vigilo}"
DIR="${BACKUP_DIR:-$HOME/backups}"
CONTAINER="${MYSQL_CONTAINER:-data-mysql-1}"
ROTACIONAR="$(dirname "$(readlink -f "$0")")/cnpj-db/bin/rotacionar-backups.sh"
DIARIOS="${BACKUP_DIARIOS:-30}"
MENSAIS="${BACKUP_MENSAIS:-12}"
TENTATIVAS="${BACKUP_ENVIO_TENTATIVAS:-3}"
ESPERA="${BACKUP_ENVIO_ESPERA:-60}" # segundos entre tentativas de envio

log() { echo "==> [$(date -Is)] $*"; }
erro() { echo "!! [$(date -Is)] $*" >&2; }

# Resultado da rodada diária no app (card "Rotinas" do /admin). Melhor esforço:
# o backup não depende do app estar de pé.
registrar() {
    docker compose -f "$APP_DIR/docker-compose.yml" exec -T app php artisan vigilo:rotina backup "$1" --message="$2" >/dev/null 2>&1 || erro "registro da rodada no app falhou (card Rotinas do /admin) — o backup em si seguiu"
}

# Só a variável necessária do .env (não carrega os segredos do arquivo).
env_var() {
    [ -f "$APP_DIR/.env" ] || return 0
    grep -E "^$1=" "$APP_DIR/.env" | tail -n 1 | cut -d= -f2- | tr -d "\"'" || true
}
REMOTE_SSH="$(env_var CNPJ_REMOTE_SSH)"
REMOTE_KEY="$(env_var CNPJ_REMOTE_SSH_KEY)"
REMOTE_KEY="${REMOTE_KEY:-$HOME/.ssh/cnpj_db}"

exec 9>"/tmp/vigilo-backup-db.lock"
if ! flock -n 9; then
    log "outro backup já em andamento — saindo"
    exit 0
fi

# Manda um backup à VM e confere o sha256 devolvido. Devolve o código do
# guardar-backup.sh da VM (2 nome, 3 sem espaço, 4 inválido, 6 já guardado
# diferente), 255 se o SSH caiu, 1 se o sha não confere.
enviar_um() {
    local arquivo="$1" nome esperado recebido rc=0
    nome="${arquivo##*/}"
    esperado="$(sha256sum "$arquivo" | cut -d' ' -f1)"
    recebido="$(ssh -i "$REMOTE_KEY" -o IdentitiesOnly=yes -o BatchMode=yes -o ConnectTimeout=20 \
        -o ServerAliveInterval=30 -o ServerAliveCountMax=4 \
        "$REMOTE_SSH" "guardar-backup $nome" <"$arquivo" | tail -n 1)" || rc=$?
    [ "$rc" = 0 ] || return "$rc"
    if [ "$recebido" != "$esperado" ]; then
        erro "sha256 de ${nome} não confere (local ${esperado}, VM ${recebido:-?})"
        return 1
    fi
}

enviar() {
    local arquivo="$1" tentativa=1 rc
    while :; do
        rc=0
        enviar_um "$arquivo" || rc=$?
        if [ "$rc" = 0 ]; then
            log "cópia fora do VPS ok: ${arquivo##*/} na VM (${REMOTE_SSH})"
            return 0
        fi
        # Recusa definitiva da VM: re-tentar não muda nada.
        if [ "$rc" = 2 ] || [ "$rc" = 3 ] || [ "$rc" = 6 ]; then
            erro "a VM recusou ${arquivo##*/} (código ${rc}) — backup local mantido"
            return 1
        fi
        if [ "$tentativa" -ge "$TENTATIVAS" ]; then
            erro "cópia fora do VPS FALHOU para ${arquivo##*/} após ${TENTATIVAS} tentativas — backup local mantido"
            return 1
        fi
        tentativa=$((tentativa + 1))
        sleep "$ESPERA"
    done
}

shopt -s nullglob

if [ "${1:-}" = "--enviar-existentes" ]; then
    [ -n "$REMOTE_SSH" ] || { erro "CNPJ_REMOTE_SSH ausente em ${APP_DIR}/.env — sem destino"; exit 1; }
    falhas=0
    for f in "$DIR"/db-[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9].sql.gz; do
        enviar "$f" || falhas=$((falhas + 1))
    done
    [ "$falhas" = 0 ] || { erro "${falhas} backup(s) não chegaram à VM"; exit 1; }
    exit 0
fi

umask 077
mkdir -p "$DIR"
nome="db-$(date +%F).sql.gz"
tmp="$DIR/.${nome}.$$.tmp"
trap 'rm -f "$tmp"' EXIT

log "dump do MySQL (${CONTAINER}) → ${DIR}/${nome}"
if ! docker exec "$CONTAINER" sh -c \
    'exec mysqldump -uroot -p"$MYSQL_ROOT_PASSWORD" --all-databases --single-transaction --routines --events' \
    | gzip >"$tmp"; then
    erro "mysqldump falhou — nenhum backup trocado nem apagado"
    registrar fail "mysqldump falhou"
    exit 1
fi
if ! gzip -t "$tmp" 2>/dev/null || ! zcat "$tmp" | tail -n 1 | grep -q '^-- Dump completed'; then
    erro "dump inválido ou incompleto — descartado (nenhum backup trocado nem apagado)"
    registrar fail "dump inválido ou incompleto"
    exit 1
fi
mv -f "$tmp" "$DIR/$nome"
trap - EXIT
log "backup local ok: ${nome} ($(du -h "$DIR/$nome" | cut -f1))"

rc=0
if [ -n "$REMOTE_SSH" ]; then
    enviar "$DIR/$nome" || rc=1
else
    erro "CNPJ_REMOTE_SSH ausente em ${APP_DIR}/.env — backup SÓ local"
fi

"$ROTACIONAR" "$DIR" "$DIARIOS" "$MENSAIS"

tamanho="$(du -h "$DIR/$nome" | cut -f1)"
if [ "$rc" != 0 ]; then
    registrar fail "${nome} (${tamanho}) local ok, mas a cópia na VM falhou"
elif [ -z "$REMOTE_SSH" ]; then
    registrar ok "${nome} (${tamanho}) — só local (sem CNPJ_REMOTE_SSH)"
else
    registrar ok "${nome} (${tamanho}) + cópia na VM"
fi

exit "$rc"
