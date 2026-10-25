#!/usr/bin/env bash
#
# Manutenção da base CNPJ, chamada pelo cron do host. Roda SEMANALMENTE
# (a Receita publica ~1x/mês, sem dia fixo — rodar toda semana garante pegar o
# dump novo em até 7 dias, sem depender de acertar o dia da publicação).
#
# Fluxo:
#   1) reimport do dump da Receita (upsert — não dropa as tabelas);
#   2) SÓ se o reimport trouxe dados novos (processed_files cresceu):
#        2a) re-coleta da carteira (gera os alertas do que mudou entre dumps);
#        2b) normalização de sócios (refresca socios.nome_norm da busca OSINT).
#   Nas semanas sem dump novo, (2) é pulado — a rodada é um no-op de segundos.
#   3) PGFN (dívida ativa), base remota: confere/carrega o trimestre mais novo
#      da PGFN INTEIRA na VM (verbo `pgfn`). Roda toda semana, independente do
#      dump da Receita; é no-op enquanto o trimestre já estiver carregado.
#
# Ao final (e também quando o reimport aborta) grava uma linha em
# `maintenance_runs` via `vigilo:maintenance-record`, que o super-admin vê em
# /admin. Como grava TODA semana, o card prova que o cron está vivo.
#
# Regras de falha:
#   - o REIMPORT é tentado até REIMPORT_ATTEMPTS vezes (padrão 3), com
#     REIMPORT_RETRY_WAIT segundos entre tentativas, para absorver hiccups
#     transitórios do servidor da Receita; só aborta se TODAS falharem (não mexe
#     em dado parcial);
#   - (2a) e (2b) são independentes: uma falhar não impede a outra;
#   - a contagem de processed_files de ANTES fica salva em
#     ~/apps/vigilo/.cnpj-pf-baseline até os passos (2) terminarem bem: se a
#     rodada morrer depois de o dump ser carregado, a seguinte ainda vê o delta;
#   - base remota: VM sem espaço em /data (saída 3) aborta na 1ª tentativa.
#
# Base remota (VM Oracle — docs/vigilo-cnpj-oracle-migracao.md): com
# CNPJ_REMOTE_SSH (ex.: ubuntu@159.112.186.240) no ~/apps/vigilo/.env, o
# pipeline e a contagem de processed_files rodam NA VM, via SSH com a chave
# dedicada ~/.ssh/cnpj_db (comando forçado gateway.sh: verbos `reimport`,
# `pf-count` e `pgfn`). Re-coleta, normalização e o registro continuam aqui. Sem a
# variável, tudo roda neste host como antes.
#
# Cron sugerido (toda segunda, 03:00):
#   0 3 * * 1 /usr/bin/bash ~/apps/vigilo/src/deploy/reimport-mensal.sh >> ~/apps/vigilo/cnpj-mensal.log 2>&1
#
# Ver docs/vigilo-v2-f1-runbook.md.
set -uo pipefail

APP_DIR="$HOME/apps/vigilo"
COMPOSE="docker compose -f $APP_DIR/docker-compose.yml"

cd "$APP_DIR" || exit 1

# Lock: evita duas execuções simultâneas (ex.: uma rodada estourou a janela e a
# próxima semana disparou por cima). flock -n sai se já houver uma rodando.
exec 9>"/tmp/vigilo-reimport-mensal.lock" || exit 1
if ! flock -n 9; then
    echo "==> [$(date -Is)] outra execução já em andamento — saindo"
    exit 0
fi

# Credenciais do Postgres (as mesmas do compose). PG_PASS SEM `:?` — faltando,
# ainda queremos registrar a falha (contrato "grava maintenance_run sempre").
set -a
# shellcheck disable=SC1091
. "$APP_DIR/.env"
set +a
PG_USER="${CNPJ_DB_USERNAME:-cnpj}"
PG_PASS="${CNPJ_DB_PASSWORD:-}"
REMOTE_SSH="${CNPJ_REMOTE_SSH:-}"
REMOTE_KEY="${CNPJ_REMOTE_SSH_KEY:-$HOME/.ssh/cnpj_db}"

STARTED_AT="$(date -Is)"
REIMPORT=skip
RECOLETA=skip
NORMALIZAR=skip
PGFN="" # vazio = o passo não rodou (abortos antes dele não o registram)
MSG=""

is_num() { case "${1:-}" in '' | *[!0-9]*) return 1 ;; *) return 0 ;; esac }

# Verbo do gateway.sh na VM da base remota. Keepalive: o reimport fica horas
# sem saída e a conexão não pode morrer por ociosidade. -n: não consome o stdin
# deste script. A chave da VM tem de estar no known_hosts (BatchMode não
# pergunta) — conferida pela fingerprint na migração.
remote() {
    ssh -n -i "$REMOTE_KEY" -o IdentitiesOnly=yes -o BatchMode=yes -o ConnectTimeout=20 \
        -o ServerAliveInterval=60 -o ServerAliveCountMax=5 "$REMOTE_SSH" "$1"
}

# Quantos arquivos o pipeline já processou (idempotência da Receita vive aqui).
# Vazio se a tabela não existir / não der pra consultar.
pf_count() {
    if [ -n "$REMOTE_SSH" ]; then
        remote pf-count 2>/dev/null | tr -d '[:space:]'
        return
    fi
    $COMPOSE exec -T -e PGPASSWORD="$PG_PASS" postgres \
        psql -U "$PG_USER" -d cnpj -tAc "SELECT count(*) FROM processed_files" 2>/dev/null | tr -d '[:space:]'
}

# Uma tentativa de reimport (upsert): na VM (base remota) ou aqui.
run_pipeline() {
    if [ -n "$REMOTE_SSH" ]; then
        remote reimport
        return
    fi
    docker run --rm --network data \
        -e DATABASE_URL="$PG_DSN" \
        -e LOADING_STRATEGY=upsert \
        ghcr.io/caiopizzol/cnpj-data-pipeline
}

record_run() {
    $COMPOSE exec -T app php artisan vigilo:maintenance-record \
        --kind=cnpj_mensal --started="$STARTED_AT" \
        --reimport="$REIMPORT" --recoleta="$RECOLETA" --normalizar="$NORMALIZAR" \
        ${PGFN:+--pgfn="$PGFN"} \
        --message="$MSG" \
        || echo "!! [$(date -Is)] não consegui registrar o maintenance-run"
}

# Passo (3): PGFN inteira na VM. Falha aqui não desfaz o resto da rodada — só
# marca o passo (e a rodada) como falha no card do /admin; a carga anterior
# segue em uso na VM.
run_pgfn() {
    if [ -z "$REMOTE_SSH" ]; then
        PGFN=skip # a PGFN inteira só existe na base remota
        return
    fi
    echo "==> [$(date -Is)] PGFN (dívida ativa): confere/carrega o trimestre mais novo na VM"
    remote pgfn
    local rc=$?
    if [ "$rc" = 0 ]; then
        PGFN=ok
        return
    fi
    PGFN=fail
    if [ "$rc" = 3 ]; then
        MSG="${MSG:+$MSG; }carga da PGFN não executada: VM sem espaço livre em /data"
    else
        MSG="${MSG:+$MSG; }carga da PGFN falhou (código ${rc}) — a anterior segue em uso"
    fi
    echo "!! [$(date -Is)] carga da PGFN falhou (código ${rc})"
}

# Base local: sem a senha do Postgres não dá para consultar a base nem
# reimportar — registra a falha (o card do super-admin precisa saber) e sai, em
# vez de morrer calado. (Na base remota a senha mora na VM.)
if [ -z "$REMOTE_SSH" ] && [ -z "$PG_PASS" ]; then
    REIMPORT=fail
    MSG="CNPJ_DB_PASSWORD ausente em ~/apps/vigilo/.env — manutenção abortada"
    echo "!! [$(date -Is)] $MSG"
    record_run
    exit 1
fi
PG_DSN="postgres://${PG_USER}:${PG_PASS}@data-postgres-1:5432/cnpj"
[ -n "$REMOTE_SSH" ] && echo "==> [$(date -Is)] base remota: pipeline roda em ${REMOTE_SSH}"

BEFORE="$(pf_count)"

# Baseline persistido: se uma rodada anterior morreu depois de o pipeline
# carregar um dump (na base remota o import segue rodando na VM mesmo com este
# script caído — reboot do VPS, SSH que cai 3×), a rodada seguinte ainda
# precisa ver o delta e fazer a re-coleta/normalização. Vale o MENOR entre o
# salvo e o atual; o arquivo só some quando não há o que fazer ou quando os
# passos (2) terminam bem (se um falhar, a próxima rodada tenta de novo).
BASELINE_FILE="$APP_DIR/.cnpj-pf-baseline"
SAVED="$(cat "$BASELINE_FILE" 2>/dev/null || true)"
if is_num "$SAVED" && { ! is_num "$BEFORE" || [ "$SAVED" -lt "$BEFORE" ]; }; then
    echo "==> [$(date -Is)] baseline salvo de uma rodada anterior: ${SAVED} (atual: ${BEFORE:-?})"
    BEFORE="$SAVED"
fi
if is_num "$BEFORE"; then
    echo "$BEFORE" >"$BASELINE_FILE"
fi

# O servidor de dados abertos da Receita é instável: às vezes devolve resposta
# vazia e o pipeline quebra no parse ("no element found") logo na descoberta —
# falha transitória, não erro de dado. Por isso tentamos algumas vezes com
# intervalo antes de desistir. (A recarga é upsert/idempotente, então re-tentar
# é seguro.) Configurável por env.
REIMPORT_ATTEMPTS="${REIMPORT_ATTEMPTS:-3}"
REIMPORT_RETRY_WAIT="${REIMPORT_RETRY_WAIT:-180}" # segundos entre tentativas

attempt=1
while :; do
    echo "==> [$(date -Is)] reimport (upsert) — tentativa ${attempt}/${REIMPORT_ATTEMPTS}"
    run_pipeline
    rc=$?
    if [ "$rc" = 0 ]; then
        REIMPORT=ok
        break
    fi

    echo "!! [$(date -Is)] tentativa ${attempt} falhou (código ${rc})"

    # Saída 3 do reimport.sh da VM = sem espaço em /data: re-tentar não adianta.
    if [ -n "$REMOTE_SSH" ] && [ "$rc" = 3 ]; then
        REIMPORT=fail
        MSG="VM da base CNPJ sem espaço livre em /data — reimport não executado (ver cnpj-mensal.log)"
        echo "!! [$(date -Is)] $MSG"
        record_run
        exit 1
    fi

    if [ "$attempt" -ge "$REIMPORT_ATTEMPTS" ]; then
        REIMPORT=fail
        MSG="reimport falhou após ${REIMPORT_ATTEMPTS} tentativa(s) (ver cnpj-mensal.log)"
        if [ -n "$REMOTE_SSH" ]; then
            echo "!! [$(date -Is)] reimport FALHOU após ${REIMPORT_ATTEMPTS} tentativas — abortando (o import pode seguir rodando na VM; a próxima rodada detecta o dump pelo baseline salvo)"
        else
            echo "!! [$(date -Is)] reimport FALHOU após ${REIMPORT_ATTEMPTS} tentativas — abortando (dado pode estar parcial)"
        fi
        record_run
        exit 1
    fi

    echo "==> [$(date -Is)] aguardando ${REIMPORT_RETRY_WAIT}s antes de retentar..."
    sleep "$REIMPORT_RETRY_WAIT"
    attempt=$((attempt + 1))
done

AFTER="$(pf_count)"

# Houve dump novo? Sim se a contagem de arquivos processados subiu. Se não deu
# pra contar (tabela ausente etc.), roda os passos por segurança.
if is_num "$BEFORE" && is_num "$AFTER"; then
    [ "$AFTER" -gt "$BEFORE" ] && NEW_DATA=1 || NEW_DATA=0
else
    NEW_DATA=1
    MSG="${MSG:+$MSG; }contagem de processed_files indisponível; rodei os passos por segurança"
fi

if [ "$NEW_DATA" = 0 ]; then
    echo "==> [$(date -Is)] sem dump novo (processed_files: ${BEFORE} → ${AFTER}) — pulando recoleta/normalizar"
    MSG="${MSG:+$MSG; }sem dump novo (base já atualizada)"
    rm -f "$BASELINE_FILE"
    run_pgfn
    record_run
    echo "==> [$(date -Is)] manutenção concluída (sem dump novo da Receita)"
    exit 0
fi

echo "==> [$(date -Is)] dump novo detectado (processed_files: ${BEFORE} → ${AFTER})"

echo "==> [$(date -Is)] re-coleta da carteira (alertas)"
if $COMPOSE exec -T app php artisan vigilo:recoletar-carteira; then
    RECOLETA=ok
else
    RECOLETA=fail
    MSG="${MSG:+$MSG; }recoletar-carteira falhou"
    echo "!! [$(date -Is)] recoletar-carteira falhou (segue para normalizar)"
fi

echo "==> [$(date -Is)] normalizar sócios (nome_norm da busca por sócio)"
if $COMPOSE exec -T app php artisan vigilo:normalizar-socios; then
    NORMALIZAR=ok
else
    NORMALIZAR=fail
    MSG="${MSG:+$MSG; }normalizar-socios falhou"
    echo "!! [$(date -Is)] normalizar-socios falhou"
fi

if [ "$RECOLETA" = ok ] && [ "$NORMALIZAR" = ok ]; then
    rm -f "$BASELINE_FILE"
else
    MSG="${MSG:+$MSG; }a próxima rodada repete a re-coleta/normalização"
fi

run_pgfn
record_run
echo "==> [$(date -Is)] manutenção concluída"
