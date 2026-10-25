#!/usr/bin/env bash
#
# Fecha o histórico cadastral do dump recém-carregado (saídas/entradas de
# sócios, carimbo do dump, sanidade) — ver sql/pos-reimport.sql. Chamado pelo
# reimport.sh depois de um import bem-sucedido; idempotente (um dump = uma
# rodada), então pode ser re-rodado à mão sem efeito colateral.
set -euo pipefail

DIR="$(dirname "$(dirname "$(readlink -f "$0")")")"

docker exec -i cnpj-postgres psql -U cnpj -d cnpj -X -q -v ON_ERROR_STOP=1 "$@" <"$DIR/sql/pos-reimport.sql"
