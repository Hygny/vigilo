#!/usr/bin/env bash
#
# Fecha o histórico cadastral do dump recém-carregado (saídas/entradas de
# sócios, carimbo do dump, sanidade) — ver sql/pos-reimport.sql. Chamado pelo
# reimport.sh depois de um import bem-sucedido; idempotente (um dump = uma
# rodada), então pode ser re-rodado à mão sem efeito colateral.
#
# Saída 4 = a rodada foi ABORTADA pela sanidade (saídas de sócios demais):
# nenhum sócio foi apagado e alguém precisa olhar (historico_execucoes).
set -euo pipefail

DIR="$(dirname "$(dirname "$(readlink -f "$0")")")"
psql_cnpj() { docker exec -i cnpj-postgres psql -U cnpj -d cnpj -X -q -v ON_ERROR_STOP=1 "$@"; }

psql_cnpj "$@" <"$DIR/sql/pos-reimport.sql"

read -r ref status <<<"$(psql_cnpj -At -F ' ' -c "SELECT referencia, status FROM historico_execucoes ORDER BY referencia DESC LIMIT 1")"
if [ "${status:-}" = abortado ]; then
    echo "!! [$(date -Is)] pos-reimport ABORTADO no dump ${ref} — nenhum sócio apagado; ver a observação em historico_execucoes" >&2
    exit 4
fi
