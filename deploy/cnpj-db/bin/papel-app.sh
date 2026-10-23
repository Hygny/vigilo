#!/usr/bin/env bash
#
# Papel do app (VPS) na base CNPJ: `vigilo_app`, NÃO-superuser e dono das
# tabelas — o app lê, e a normalização de sócios faz UPDATE/ADD COLUMN/CREATE
# INDEX em `socios` (exige ser dono). O superuser `cnpj` fica só no socket e no
# 127.0.0.1 (pipeline, manutenção): pela rede ninguém ganha COPY ... PROGRAM.
#
# Idempotente. Rodar depois de restaurar ou recriar tabelas (o
# restaurar-dump.sh já chama). Senha em ~/cnpj/.env (CNPJ_APP_PASSWORD, gerada
# pelo provisionar-vm.sh).
set -euo pipefail

DIR="$(dirname "$(dirname "$(readlink -f "$0")")")"

set -a
# shellcheck disable=SC1091
. "$DIR/.env"
set +a
: "${CNPJ_APP_PASSWORD:?defina CNPJ_APP_PASSWORD em ~/cnpj/.env (o provisionar-vm.sh gera)}"

# A senha entra pelo stdin (\set), não pela linha de comando do docker exec
# (que aparece no `ps` e nos `docker events`). Hex — sem aspas a escapar.
{
    printf "\\\\set pw '%s'\n" "$CNPJ_APP_PASSWORD"
    cat <<'SQL'
SELECT format('CREATE ROLE vigilo_app LOGIN NOSUPERUSER NOCREATEDB NOCREATEROLE PASSWORD %L', :'pw')
 WHERE NOT EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'vigilo_app') \gexec
SELECT format('ALTER ROLE vigilo_app PASSWORD %L', :'pw') \gexec
SELECT format('ALTER TABLE public.%I OWNER TO vigilo_app', tablename)
  FROM pg_tables WHERE schemaname = 'public' AND tableowner <> 'vigilo_app' \gexec
SQL
} | docker exec -i cnpj-postgres psql -U cnpj -d cnpj -X -q -v ON_ERROR_STOP=1

echo "==> [$(date -Is)] papel vigilo_app ok (dono de $(docker exec cnpj-postgres psql -U cnpj -d cnpj -X -tAc \
    "SELECT count(*) FROM pg_tables WHERE schemaname = 'public' AND tableowner = 'vigilo_app'" </dev/null) tabelas)"
