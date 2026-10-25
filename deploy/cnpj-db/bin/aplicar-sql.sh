#!/usr/bin/env bash
#
# Aplica os objetos do Vigilo que vivem DENTRO da base CNPJ além das tabelas do
# pipeline da Receita (gatilhos do histórico cadastral, busca por nome...).
# Tudo idempotente (IF NOT EXISTS / CREATE OR REPLACE): o reimport.sh chama
# antes de cada import, então uma base restaurada ou recriada volta a ter os
# gatilhos sozinha. Ordem importa: historico.sql antes de busca.sql.
set -euo pipefail

DIR="$(dirname "$(dirname "$(readlink -f "$0")")")"

for sql in historico busca; do
    f="$DIR/sql/$sql.sql"
    [ -f "$f" ] || continue
    docker exec -i cnpj-postgres psql -U cnpj -d cnpj -X -q -v ON_ERROR_STOP=1 <"$f"
done
