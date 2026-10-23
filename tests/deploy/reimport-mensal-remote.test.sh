#!/usr/bin/env bash
#
# Testes do deploy/reimport-mensal.sh no modo base REMOTA (CNPJ_REMOTE_SSH),
# com `ssh` e `docker` falsos no PATH — nada toca servidor de verdade.
# Precisa de Linux (flock, GNU date). Rodar:
#   bash tests/deploy/reimport-mensal-remote.test.sh
set -uo pipefail

SCRIPT="$(cd "$(dirname "$0")/../.." && pwd)/deploy/reimport-mensal.sh"
FAILS=0

# setup <pf antes> <pf depois do reimport> <código do reimport> [recoleta falha: 0|1]
setup() {
    T="$(mktemp -d)"
    export T
    mkdir -p "$T/home/apps/vigilo" "$T/bin"
    printf 'CNPJ_REMOTE_SSH=ubuntu@vm\nREIMPORT_RETRY_WAIT=0\n' >"$T/home/apps/vigilo/.env"
    : >"$T/home/apps/vigilo/docker-compose.yml"
    echo "$1" >"$T/pf"
    echo "$2" >"$T/pf_after"
    echo "$3" >"$T/reimport_rc"
    echo "${4:-0}" >"$T/recoleta_fails"
    : >"$T/calls"

    # ssh falso: só os verbos do gateway.sh da VM.
    cat >"$T/bin/ssh" <<'EOF'
#!/usr/bin/env bash
verb="${*: -1}"
echo "ssh $verb" >>"$T/calls"
case "$verb" in
    pf-count) cat "$T/pf" ;;
    reimport)
        rc="$(cat "$T/reimport_rc")"
        [ "$rc" = 0 ] && cp "$T/pf_after" "$T/pf"
        exit "$rc"
        ;;
esac
EOF
    # docker falso: registra os artisan (recoleta/normalizar/maintenance-record)
    # e, no modo local, faz o papel do psql (contagem) e do pipeline.
    cat >"$T/bin/docker" <<'EOF'
#!/usr/bin/env bash
echo "docker $*" >>"$T/calls"
case "$*" in
    *recoletar-carteira*) [ "$(cat "$T/recoleta_fails")" = 1 ] && exit 1 ;;
    *processed_files*) cat "$T/pf" ;;
    *cnpj-data-pipeline*)
        rc="$(cat "$T/reimport_rc")"
        [ "$rc" = 0 ] && cp "$T/pf_after" "$T/pf"
        exit "$rc"
        ;;
esac
exit 0
EOF
    chmod +x "$T/bin/ssh" "$T/bin/docker"

    # Sem flock (ex.: Git Bash), um no-op basta: o teste não roda em paralelo.
    if ! command -v flock >/dev/null; then
        printf '#!/usr/bin/env bash\nexit 0\n' >"$T/bin/flock"
        chmod +x "$T/bin/flock"
    fi
}

run() {
    HOME="$T/home" PATH="$T/bin:$PATH" bash "$SCRIPT" >"$T/out" 2>&1
    echo $? >"$T/rc"
}

ok() { echo "  ok   $1"; }
ko() {
    echo "  FAIL $1"
    sed 's/^/       | /' "$T/out"
    FAILS=$((FAILS + 1))
}
has() { grep -q -- "$1" "$T/calls"; }
count() { grep -c -- "$1" "$T/calls"; }
baseline() { cat "$T/home/apps/vigilo/.cnpj-pf-baseline" 2>/dev/null; }

echo "reimport-mensal.sh (modo remoto)"

t="sem dump novo: roda o pipeline na VM, pula os passos e limpa o baseline"
setup 74 74 0
run
if [ "$(cat "$T/rc")" = 0 ] && has "ssh reimport" && ! has "recoletar-carteira" \
    && has -- "--reimport=ok" && [ -z "$(baseline)" ]; then ok "$t"; else ko "$t"; fi

t="dump novo: re-coleta + normalização e limpa o baseline"
setup 74 111 0
run
if [ "$(cat "$T/rc")" = 0 ] && has "recoletar-carteira" && has "normalizar-socios" \
    && [ -z "$(baseline)" ]; then ok "$t"; else ko "$t"; fi

t="rodada anterior caiu e a VM terminou o import: baseline salvo garante a re-coleta"
setup 111 111 0
echo 74 >"$T/home/apps/vigilo/.cnpj-pf-baseline"
run
if [ "$(cat "$T/rc")" = 0 ] && has "recoletar-carteira" && [ -z "$(baseline)" ]; then
    ok "$t"
else ko "$t"; fi

t="VM sem espaço (saída 3): não re-tenta, registra o motivo e guarda o baseline"
setup 74 74 3
run
if [ "$(cat "$T/rc")" = 1 ] && [ "$(count "ssh reimport")" = 1 ] && has "sem espaço livre" \
    && has -- "--reimport=fail" && [ "$(baseline)" = 74 ]; then ok "$t"; else ko "$t"; fi

t="falha transitória: re-tenta 3x, registra a falha e guarda o baseline"
setup 74 74 1
run
if [ "$(cat "$T/rc")" = 1 ] && [ "$(count "ssh reimport")" = 3 ] \
    && has -- "--reimport=fail" && [ "$(baseline)" = 74 ]; then ok "$t"; else ko "$t"; fi

t="passo (2) falhou: baseline fica para a próxima rodada repetir"
setup 74 111 0 1
run
if has -- "--recoleta=fail" && has "normalizar-socios" && [ "$(baseline)" = 74 ]; then
    ok "$t"
else ko "$t"; fi

t="modo local (sem CNPJ_REMOTE_SSH): pipeline e contagem aqui, sem SSH"
setup 74 111 0
printf 'CNPJ_DB_PASSWORD=x\nREIMPORT_RETRY_WAIT=0\n' >"$T/home/apps/vigilo/.env"
run
if [ "$(cat "$T/rc")" = 0 ] && has "cnpj-data-pipeline" && has "recoletar-carteira" \
    && ! has "ssh "; then ok "$t"; else ko "$t"; fi

t="modo local (sem CNPJ_REMOTE_SSH) continua exigindo a senha do Postgres"
setup 74 74 0
printf 'REIMPORT_RETRY_WAIT=0\n' >"$T/home/apps/vigilo/.env"
run
if [ "$(cat "$T/rc")" = 1 ] && has "CNPJ_DB_PASSWORD ausente" && ! has "ssh "; then
    ok "$t"
else ko "$t"; fi

if [ "$FAILS" -gt 0 ]; then
    echo "$FAILS teste(s) falharam"
    exit 1
fi
echo "todos os testes passaram"
