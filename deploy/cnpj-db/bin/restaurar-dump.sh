#!/usr/bin/env bash
#
# Restaura /data/dump/cnpj.dump (pg_dump -Fc) na base VAZIA desta VM — usado
# na migração (Fase 2 do roteiro) e como recuperação rápida. Restaura em
# paralelo e refaz as estatísticas (o PG 17 não as leva no dump).
#
# Rodar na VM, em background (leva horas):
#   nohup ~/cnpj/bin/restaurar-dump.sh > ~/restaurar-dump.log 2>&1 &
set -euo pipefail

DUMP=/dump/cnpj.dump # caminho DENTRO do container (/data/dump no host)
JOBS="${JOBS:-4}"
PSQL=(docker exec cnpj-postgres psql -U cnpj -d cnpj -X -v ON_ERROR_STOP=1)

# Restaurar por cima de uma base com dados colidiria/duplicaria objetos.
tables="$("${PSQL[@]}" -tAc "SELECT count(*) FROM pg_tables WHERE schemaname = 'public'")"
if [ "$tables" != 0 ]; then
    echo "!! a base já tem ${tables} tabela(s) — restauração abortada"
    exit 1
fi

# Mais memória/WAL só durante a carga: os índices são recriados do zero
# (4 jobs × 2 GB + 6 GB de shared_buffers cabem nos 24 GB).
"${PSQL[@]}" -c "ALTER SYSTEM SET maintenance_work_mem = '2GB'" \
    -c "ALTER SYSTEM SET max_wal_size = '16GB'" -c "SELECT pg_reload_conf()" >/dev/null
trap '"${PSQL[@]}" -c "ALTER SYSTEM RESET maintenance_work_mem" -c "ALTER SYSTEM RESET max_wal_size" -c "SELECT pg_reload_conf()" >/dev/null' EXIT

echo "==> [$(date -Is)] pg_restore (${JOBS} jobs)"
docker exec cnpj-postgres pg_restore -U cnpj -d cnpj -j "$JOBS" --no-owner --exit-on-error "$DUMP"

echo "==> [$(date -Is)] vacuum + analyze"
docker exec cnpj-postgres vacuumdb -U cnpj -d cnpj -j "$JOBS" --analyze

# As tabelas nascem do superuser; o app entra com o papel não-superuser dono delas.
"$(dirname "$(readlink -f "$0")")/papel-app.sh"

echo "==> [$(date -Is)] restauração concluída"
