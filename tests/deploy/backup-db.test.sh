#!/usr/bin/env bash
#
# Testes do backup do MySQL do Vigilo: deploy/backup-db.sh (VPS) e, na VM,
# cnpj-db/bin/{gateway,guardar-backup,rotacionar-backups}.sh — com `docker` e
# `ssh` falsos no PATH (o ssh falso roda o gateway.sh local), nada toca servidor.
# Roda em Linux e no Git Bash:
#   bash tests/deploy/backup-db.test.sh
set -uo pipefail
export LC_ALL=C

REPO="$(cd "$(dirname "$0")/../.." && pwd)"
BACKUP="$REPO/deploy/backup-db.sh"
BIN="$REPO/deploy/cnpj-db/bin"
FAILS=0

ok() { echo "  ok   $1"; }
ko() {
    echo "  FAIL $1"
    [ -f "$T/out" ] && sed 's/^/       | /' "$T/out"
    FAILS=$((FAILS + 1))
}

dump_ok() { printf -- '-- MySQL dump\nCREATE TABLE t (id int);\n-- Dump completed on 2026-11-24  3:00:01\n'; }

# Cria backups diários falsos (válidos) de <início> por <n> dias em <dir>.
seed() {
    local dir="$1" inicio="$2" n="$3" i d
    mkdir -p "$dir"
    for ((i = 0; i < n; i++)); do
        d="$(date -d "$inicio + $i day" +%F)"
        dump_ok | gzip >"$dir/db-$d.sql.gz"
    done
}
conta() { find "$1" -maxdepth 1 -name 'db-*.sql.gz' | wc -l | tr -d ' '; }

setup() {
    T="$(mktemp -d)"
    export T
    mkdir -p "$T/home/apps/vigilo" "$T/home/backups" "$T/vm" "$T/bin"
    printf 'APP_KEY=segredo\nCNPJ_REMOTE_SSH=ubuntu@vm\n' >"$T/home/apps/vigilo/.env"
    echo ok >"$T/docker_mode"
    echo 0 >"$T/ssh_fail"
    : >"$T/calls"

    cat >"$T/bin/docker" <<'EOF'
#!/usr/bin/env bash
echo "docker $*" >>"$T/calls"
case "$(cat "$T/docker_mode")" in
    ok) printf -- '-- MySQL dump\nCREATE TABLE t (id int);\n-- Dump completed on 2026-11-24  3:00:01\n' ;;
    incompleto) printf -- '-- MySQL dump\nCREATE TABLE t (id int);\n' ;;
    falha) echo "mysqldump: Got error" >&2; exit 2 ;;
esac
EOF
    # ssh falso: faz o papel da VM rodando o gateway.sh local (stdin repassado).
    # ssh_fail: 1 = conexão cai (255) · 2 = "VM" devolve um sha errado.
    cat >"$T/bin/ssh" <<EOF
#!/usr/bin/env bash
echo "ssh \${*: -1}" >>"\$T/calls"
[ "\$(cat "\$T/ssh_fail")" = 1 ] && exit 255
if [ "\$(cat "\$T/ssh_fail")" = 2 ]; then cat >/dev/null; echo 0000; exit 0; fi
SSH_ORIGINAL_COMMAND="\${*: -1}" VIGILO_BACKUP_DIR="\$T/vm" VIGILO_BACKUP_MIN_LIVRE_MB=0 \
    exec bash "$BIN/gateway.sh"
EOF
    chmod +x "$T/bin/docker" "$T/bin/ssh"
    if ! command -v flock >/dev/null; then
        printf '#!/usr/bin/env bash\nexit 0\n' >"$T/bin/flock"
        chmod +x "$T/bin/flock"
    fi
}

run_backup() {
    HOME="$T/home" PATH="$T/bin:$PATH" BACKUP_ENVIO_ESPERA=0 bash "$BACKUP" "$@" >"$T/out" 2>&1
    echo $? >"$T/rc"
}

# guardar-backup direto pelo gateway, como o VPS chama.
guardar() {
    SSH_ORIGINAL_COMMAND="guardar-backup $1" VIGILO_BACKUP_DIR="$T/vm" \
        VIGILO_BACKUP_MIN_LIVRE_MB="${MIN_MB:-0}" bash "$BIN/gateway.sh" >"$T/out" 2>&1
    echo $? >"$T/rc"
}

HOJE="$(date +%F)"

echo "rotacionar-backups.sh"

t="mantém os N mais recentes + o 1º de cada mês; não toca subpasta, temporário nem outro nome"
setup
seed "$T/r" 2026-01-01 100 # 01/01 a 10/04
mkdir -p "$T/r/solradar"
dump_ok | gzip >"$T/r/solradar/db-2026-01-02.sql.gz"
echo x >"$T/r/.db-2026-01-05.sql.gz.123.tmp"
echo x >"$T/r/outro.sql.gz"
bash "$BIN/rotacionar-backups.sh" "$T/r" 30 12 >"$T/out" 2>&1
# 30 mais recentes (12/03..10/04) + 01/01, 01/02, 01/03 (01/04 já está nos 30)
if [ "$(conta "$T/r")" = 33 ] && [ -f "$T/r/db-2026-01-01.sql.gz" ] && [ -f "$T/r/db-2026-02-01.sql.gz" ] \
    && [ -f "$T/r/db-2026-03-01.sql.gz" ] && [ -f "$T/r/db-2026-03-12.sql.gz" ] && [ ! -f "$T/r/db-2026-03-11.sql.gz" ] \
    && [ -f "$T/r/solradar/db-2026-01-02.sql.gz" ] && [ -f "$T/r/.db-2026-01-05.sql.gz.123.tmp" ] \
    && [ -f "$T/r/outro.sql.gz" ]; then ok "$t"; else ko "$t"; fi

t="limita os mensais aos M meses mais recentes"
setup
seed "$T/r" 2026-01-01 100
bash "$BIN/rotacionar-backups.sh" "$T/r" 30 2 >"$T/out" 2>&1
if [ "$(conta "$T/r")" = 31 ] && [ -f "$T/r/db-2026-03-01.sql.gz" ] && [ ! -f "$T/r/db-2026-02-01.sql.gz" ]; then
    ok "$t"
else ko "$t"; fi

t="com até N arquivos não apaga nada (buraco no calendário não zera os backups)"
setup
seed "$T/r" 2025-01-01 5
bash "$BIN/rotacionar-backups.sh" "$T/r" 30 0 >"$T/out" 2>&1
if [ "$(conta "$T/r")" = 5 ]; then ok "$t"; else ko "$t"; fi

t="argumento inválido: sai 2 sem apagar"
setup
seed "$T/r" 2026-01-01 40
bash "$BIN/rotacionar-backups.sh" "$T/r" abc 12 >"$T/out" 2>&1
rc1=$?
bash "$BIN/rotacionar-backups.sh" "$T/r" 0 12 >>"$T/out" 2>&1
rc2=$?
bash "$BIN/rotacionar-backups.sh" "$T/nao-existe" 30 12 >>"$T/out" 2>&1
rc3=$?
if [ "$rc1" = 2 ] && [ "$rc2" = 2 ] && [ "$rc3" = 2 ] && [ "$(conta "$T/r")" = 40 ]; then ok "$t"; else ko "$t"; fi

echo "guardar-backup.sh (via gateway.sh)"

t="backup válido: guarda e devolve o sha256 do arquivo"
setup
dump_ok | gzip >"$T/x.gz"
guardar db-2026-11-24.sql.gz <"$T/x.gz"
if [ "$(cat "$T/rc")" = 0 ] && cmp -s "$T/x.gz" "$T/vm/db-2026-11-24.sql.gz" \
    && [ "$(tail -n 1 "$T/out")" = "$(sha256sum "$T/x.gz" | cut -d' ' -f1)" ]; then ok "$t"; else ko "$t"; fi

t="gzip corrompido: sai 4, descarta e não deixa temporário"
setup
echo "não é gzip" >"$T/x.gz"
guardar db-2026-11-24.sql.gz <"$T/x.gz"
if [ "$(cat "$T/rc")" = 4 ] && [ -z "$(ls -A "$T/vm")" ]; then ok "$t"; else ko "$t"; fi

t="dump incompleto (sem '-- Dump completed'): sai 4 e descarta"
setup
printf -- '-- MySQL dump\nCREATE TABLE t (id int);\n' | gzip >"$T/x.gz"
guardar db-2026-11-24.sql.gz <"$T/x.gz"
if [ "$(cat "$T/rc")" = 4 ] && [ -z "$(ls -A "$T/vm")" ]; then ok "$t"; else ko "$t"; fi

t="envio que cai no meio não substitui o backup bom do mesmo dia"
setup
dump_ok | gzip >"$T/x.gz"
guardar db-2026-11-24.sql.gz <"$T/x.gz"
head -c 20 "$T/x.gz" >"$T/meio.gz"
guardar db-2026-11-24.sql.gz <"$T/meio.gz"
if [ "$(cat "$T/rc")" = 4 ] && cmp -s "$T/x.gz" "$T/vm/db-2026-11-24.sql.gz"; then ok "$t"; else ko "$t"; fi

t="nome fora do padrão (caminho, comando): sai 2 e não grava"
setup
dump_ok | gzip >"$T/x.gz"
guardar "../../etc/cron.d/x" <"$T/x.gz"
rc1="$(cat "$T/rc")"
guardar "db-2026-11-24.sql.gz; rm -rf /" <"$T/x.gz"
rc2="$(cat "$T/rc")"
if [ "$rc1" = 2 ] && [ "$rc2" = 2 ] && [ -z "$(ls -A "$T/vm" 2>/dev/null)" ]; then ok "$t"; else ko "$t"; fi

t="data impossível ou futura no nome: sai 2 e não grava (não empurra os reais p/ fora da rotação)"
setup
dump_ok | gzip >"$T/x.gz"
rcs=""
for n in db-2026-13-45.sql.gz db-2026-11-00.sql.gz db-2026-02-30.sql.gz "db-$(date -d '+5 day' +%F).sql.gz" db-9999-12-31.sql.gz; do
    guardar "$n" <"$T/x.gz"
    rcs="$rcs$(cat "$T/rc")"
done
if [ "$rcs" = 22222 ] && [ -z "$(ls -A "$T/vm" 2>/dev/null)" ]; then ok "$t"; else ko "$t"; fi

t="nunca sobrescreve: mesmo nome com outro conteúdo sai 6 e o guardado fica"
setup
dump_ok | gzip >"$T/x.gz"
guardar db-2026-11-24.sql.gz <"$T/x.gz"
printf -- '-- MySQL dump\nDROP TABLE t;\n-- Dump completed on 2026-11-24\n' | gzip >"$T/outro.gz"
guardar db-2026-11-24.sql.gz <"$T/outro.gz"
if [ "$(cat "$T/rc")" = 6 ] && cmp -s "$T/x.gz" "$T/vm/db-2026-11-24.sql.gz"; then ok "$t"; else ko "$t"; fi

t="reenvio idêntico é idempotente: sai 0 e devolve o sha"
setup
dump_ok | gzip >"$T/x.gz"
guardar db-2026-11-24.sql.gz <"$T/x.gz"
guardar db-2026-11-24.sql.gz <"$T/x.gz"
if [ "$(cat "$T/rc")" = 0 ] && [ "$(tail -n 1 "$T/out")" = "$(sha256sum "$T/x.gz" | cut -d' ' -f1)" ] \
    && [ "$(conta "$T/vm")" = 1 ]; then ok "$t"; else ko "$t"; fi

t="maior que o teto de tamanho: sai 4 e descarta"
setup
dump_ok | gzip >"$T/x.gz"
SSH_ORIGINAL_COMMAND="guardar-backup db-2026-11-24.sql.gz" VIGILO_BACKUP_DIR="$T/vm" VIGILO_BACKUP_MIN_LIVRE_MB=0 \
    VIGILO_BACKUP_MAX_BYTES=10 bash "$BIN/gateway.sh" <"$T/x.gz" >"$T/out" 2>&1
if [ $? = 4 ] && [ -z "$(ls -A "$T/vm")" ]; then ok "$t"; else ko "$t"; fi

t="sem espaço livre: sai 3 e não grava"
setup
dump_ok | gzip >"$T/x.gz"
MIN_MB=999999999 guardar db-2026-11-24.sql.gz <"$T/x.gz"
if [ "$(cat "$T/rc")" = 3 ] && [ ! -f "$T/vm/db-2026-11-24.sql.gz" ]; then ok "$t"; else ko "$t"; fi

t="roda a rotação da VM (90 + 24) a cada backup guardado"
setup
seed "$T/vm" 2026-01-01 100
dump_ok | gzip >"$T/x.gz"
guardar db-2026-04-11.sql.gz <"$T/x.gz"
# 90 mais recentes (12/01..11/04) + 01/01
if [ "$(cat "$T/rc")" = 0 ] && [ "$(conta "$T/vm")" = 91 ] && [ -f "$T/vm/db-2026-01-01.sql.gz" ] \
    && [ ! -f "$T/vm/db-2026-01-02.sql.gz" ]; then ok "$t"; else ko "$t"; fi

t="gateway continua recusando verbo desconhecido"
setup
SSH_ORIGINAL_COMMAND="bash -i" bash "$BIN/gateway.sh" >"$T/out" 2>&1
rc=$?
SSH_ORIGINAL_COMMAND="guardar-backup" bash "$BIN/gateway.sh" >>"$T/out" 2>&1
rc2=$?
if [ "$rc" = 2 ] && [ "$rc2" = 2 ]; then ok "$t"; else ko "$t"; fi

echo "backup-db.sh (VPS)"

t="rotina: dump local validado + cópia na VM com sha256 conferido + rotação local"
setup
seed "$T/home/backups" 2020-01-01 40 # 01/01 a 09/02/2020
mkdir -p "$T/home/backups/solradar"
echo intocado >"$T/home/backups/solradar/dump.sql.gz"
run_backup
# 30 mais recentes (12/01..09/02 + o de hoje) + 01/01/2020 (1º do mês) = 31
if [ "$(cat "$T/rc")" = 0 ] && [ -f "$T/home/backups/db-$HOJE.sql.gz" ] \
    && cmp -s "$T/home/backups/db-$HOJE.sql.gz" "$T/vm/db-$HOJE.sql.gz" \
    && grep -q "cópia fora do VPS ok" "$T/out" && grep -q -- "--single-transaction" "$T/calls" \
    && [ "$(conta "$T/home/backups")" = 31 ] && [ -f "$T/home/backups/db-2020-01-01.sql.gz" ] \
    && [ ! -f "$T/home/backups/db-2020-01-11.sql.gz" ] && [ -f "$T/home/backups/solradar/dump.sql.gz" ] \
    && ! grep -q "segredo" "$T/out"; then ok "$t"; else ko "$t"; fi

t="sha256 devolvido pela VM não confere: tenta 3x, sai 1 e mantém o local"
setup
echo 2 >"$T/ssh_fail"
run_backup
if [ "$(cat "$T/rc")" = 1 ] && [ -f "$T/home/backups/db-$HOJE.sql.gz" ] \
    && [ "$(grep -c "ssh guardar-backup" "$T/calls")" = 3 ] && grep -q "não confere" "$T/out"; then
    ok "$t"
else ko "$t"; fi

t="VM já guarda o do dia com outro conteúdo: recusa definitiva, não re-tenta, sai 1"
setup
printf -- '-- MySQL dump\nDROP TABLE t;\n-- Dump completed on 2026-11-24\n' | gzip >"$T/vm/db-$HOJE.sql.gz"
cp "$T/vm/db-$HOJE.sql.gz" "$T/vm-original.gz"
run_backup
if [ "$(cat "$T/rc")" = 1 ] && [ "$(grep -c "ssh guardar-backup" "$T/calls")" = 1 ] \
    && grep -q "recusou" "$T/out" && cmp -s "$T/vm-original.gz" "$T/vm/db-$HOJE.sql.gz" \
    && [ -f "$T/home/backups/db-$HOJE.sql.gz" ]; then ok "$t"; else ko "$t"; fi

t="mysqldump falhou: sai 1, não cria o do dia e não apaga nenhum antigo"
setup
seed "$T/home/backups" 2026-01-01 40
echo falha >"$T/docker_mode"
run_backup
if [ "$(cat "$T/rc")" = 1 ] && [ ! -f "$T/home/backups/db-$HOJE.sql.gz" ] \
    && [ "$(conta "$T/home/backups")" = 40 ] && ! grep -q "ssh " "$T/calls" \
    && [ -z "$(find "$T/home/backups" -name '.*tmp')" ]; then ok "$t"; else ko "$t"; fi

t="dump incompleto: sai 1 e não vira backup"
setup
echo incompleto >"$T/docker_mode"
run_backup
if [ "$(cat "$T/rc")" = 1 ] && [ ! -f "$T/home/backups/db-$HOJE.sql.gz" ] && ! grep -q "ssh " "$T/calls"; then
    ok "$t"
else ko "$t"; fi

t="VM fora do ar: tenta 3x, sai 1 e mantém o backup local"
setup
echo 1 >"$T/ssh_fail"
run_backup
if [ "$(cat "$T/rc")" = 1 ] && [ -f "$T/home/backups/db-$HOJE.sql.gz" ] \
    && [ "$(grep -c "ssh guardar-backup" "$T/calls")" = 3 ] && grep -q "FALHOU" "$T/out"; then
    ok "$t"
else ko "$t"; fi

t="sem CNPJ_REMOTE_SSH: só backup local, sem SSH"
setup
printf 'APP_KEY=segredo\n' >"$T/home/apps/vigilo/.env"
run_backup
if [ "$(cat "$T/rc")" = 0 ] && [ -f "$T/home/backups/db-$HOJE.sql.gz" ] && ! grep -q "ssh " "$T/calls"; then
    ok "$t"
else ko "$t"; fi

t="--enviar-existentes: semeia a VM com os backups locais, sem dump novo"
setup
seed "$T/home/backups" 2026-11-01 10
run_backup --enviar-existentes
if [ "$(cat "$T/rc")" = 0 ] && [ "$(conta "$T/vm")" = 10 ] && ! grep -q "docker " "$T/calls" \
    && cmp -s "$T/home/backups/db-2026-11-05.sql.gz" "$T/vm/db-2026-11-05.sql.gz"; then ok "$t"; else ko "$t"; fi

t="registra o resultado no app (card Rotinas): ok, cópia falha e dump falho"
setup
run_backup
ok1=$(grep -c "docker compose -f .*docker-compose.yml exec -T app php artisan vigilo:rotina backup ok" "$T/calls")
setup
echo 1 >"$T/ssh_fail"
run_backup
fail1=$(grep -c "vigilo:rotina backup fail" "$T/calls")
setup
echo falha >"$T/docker_mode"
run_backup
fail2=$(grep -c "vigilo:rotina backup fail" "$T/calls")
if [ "$ok1" = 1 ] && [ "$fail1" = 1 ] && [ "$fail2" = 1 ]; then ok "$t"; else ko "$t (ok=$ok1 fail=$fail1/$fail2)"; fi

if [ "$FAILS" -gt 0 ]; then
    echo "$FAILS teste(s) falharam"
    exit 1
fi
echo "todos os testes passaram"
