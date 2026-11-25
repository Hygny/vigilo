#!/usr/bin/env bash
#
# (Re)monta a tabela da BUSCA DE PESSOAS (`busca_pessoa`): uma linha por pessoa
# provável = (nome do sócio, documento mascarado) — sócios PF (2) e estrangeiros
# (3) —, com a chave normalizada do nome (`busca_norm`, a mesma da busca de
# empresas) num GIN trigram, e quantas empresas ela tem. A Receita mascara o CPF
# (`***NNNNNN**`), então homônimos com os mesmos 6 dígitos do meio viram UMA
# linha (vínculo provável — a tela avisa) e homônimos com outro documento, outra.
#
# `chave` (uuid do md5 de documento|nome) é o identificador opaco da pessoa na
# URL do app — nome e CPF não aparecem em link nem em log de acesso.
#
# Monta numa tabela nova e troca no fim (a busca do app segue no ar durante a
# montagem) — a conta mora em sql/pessoas-montar.sql, a mesma que o teste SQL
# (tests/deploy/cnpj-db-sql.test.sh) roda. ~10 min e ~4,1 GB na VM (2026-11,
# 18,4 M pessoas prováveis: tabela + PK + GIN + btree do nome).
#
#   pessoas-popular.sh             # monta de novo
#   pessoas-popular.sh --se-mudou  # só se o dump carregado mudou desde a última
#                                  # montagem (o reimport.sh chama assim)
set -euo pipefail

DIR="$(dirname "$(dirname "$(readlink -f "$0")")")"
psql_cnpj() { docker exec -i cnpj-postgres psql -U cnpj -d cnpj -X -v ON_ERROR_STOP=1 "$@"; }

echo "==> [$(date -Is)] objetos da busca (busca_norm, tabela)"
psql_cnpj -q <"$DIR/sql/busca.sql"
psql_cnpj -q <"$DIR/sql/pessoas.sql"

# Assinatura do dump carregado (arquivos processados pelo pipeline).
assinatura="$(psql_cnpj -tA -c "SELECT count(*) || '|' || coalesce(max(processed_at)::text, '') FROM processed_files" </dev/null)"
if [ "${1:-}" = --se-mudou ]; then
    atual="$(psql_cnpj -tA -c "SELECT assinatura FROM busca_pessoa_cargas WHERE id = 1" </dev/null)"
    if [ "$atual" = "$assinatura" ]; then
        echo "==> [$(date -Is)] busca de pessoas já reflete este dump — nada a fazer"
        exit 0
    fi
fi

echo "==> [$(date -Is)] montando busca_pessoa_nova"
{
    echo '\timing on'
    echo "SET maintenance_work_mem = '2GB';"
    echo "SET work_mem = '1GB';"
    cat "$DIR/sql/pessoas-montar.sql"
    echo "SELECT count(*) AS pessoas, pg_size_pretty(pg_total_relation_size('busca_pessoa')) AS tamanho FROM busca_pessoa;"
} | psql_cnpj

psql_cnpj -q -v assinatura="$assinatura" <<'SQL'
INSERT INTO busca_pessoa_cargas (id, assinatura, pessoas, montado_em)
    SELECT 1, :'assinatura', count(*), now() FROM busca_pessoa
    ON CONFLICT (id) DO UPDATE SET assinatura = EXCLUDED.assinatura, pessoas = EXCLUDED.pessoas, montado_em = EXCLUDED.montado_em;
SQL

"$DIR/bin/papel-app.sh"
echo "==> [$(date -Is)] busca de pessoas pronta"
