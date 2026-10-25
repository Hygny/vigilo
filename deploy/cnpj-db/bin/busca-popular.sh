#!/usr/bin/env bash
#
# Carga INICIAL das tabelas da busca por nome (busca_razao / busca_fantasia —
# ver sql/busca.sql). Roda UMA vez por base (ou depois de uma recarga do zero);
# dali em diante os gatilhos mantêm as tabelas a cada reimport. Re-rodar não
# duplica nem perde linha (ON CONFLICT DO NOTHING), MAS derruba e remonta os
# índices: a busca do app fica fora (timeout) até o fim — só re-rode numa base
# nova/recarregada ou numa janela combinada.
#
#   busca-popular.sh             # completa o que faltar
#   busca-popular.sh --recriar   # esvazia as tabelas e carrega do zero (mudança
#                                # de schema/normalização da busca)
#
# Os índices são (re)criados DEPOIS da carga — montar um GIN de dezenas de
# milhões de linhas de uma vez é muito mais rápido do que mantê-lo linha a linha
# durante o INSERT. ~1,5–2 h na VM (o GIN não paraleliza no PG 17).
set -euo pipefail

DIR="$(dirname "$(dirname "$(readlink -f "$0")")")"
psql_cnpj() { docker exec -i cnpj-postgres psql -U cnpj -d cnpj -X -v ON_ERROR_STOP=1 "$@"; }

if [ "${1:-}" = --recriar ]; then
    # Antes do busca.sql: com as tabelas vazias, os CREATE INDEX dele são instantâneos.
    echo "==> [$(date -Is)] esvaziando as tabelas da busca (--recriar)"
    psql_cnpj -q -c "DO \$\$ BEGIN
        IF to_regclass('busca_razao') IS NOT NULL THEN TRUNCATE busca_razao; END IF;
        IF to_regclass('busca_fantasia') IS NOT NULL THEN TRUNCATE busca_fantasia; END IF;
    END \$\$"
fi

echo "==> [$(date -Is)] objetos da busca (tabelas, normalização e gatilhos)"
psql_cnpj -q <"$DIR/sql/busca.sql"

echo "==> [$(date -Is)] carga das tabelas e índices"
psql_cnpj <<'SQL'
\timing on
SET maintenance_work_mem = '2GB';
SET work_mem = '1GB';
DROP INDEX IF EXISTS idx_busca_razao_gin;
DROP INDEX IF EXISTS idx_busca_fantasia_gin;

-- UF/situação da razão social = as da matriz (identificador_matriz_filial = 1).
INSERT INTO busca_razao (cnpj_basico, nome, uf, situacao)
    SELECT x.cnpj_basico, x.nome, e.uf, e.situacao_cadastral FROM (
        SELECT cnpj_basico, busca_norm(razao_social) AS nome FROM empresas
        WHERE razao_social IS NOT NULL AND razao_social <> ''
    ) x
    LEFT JOIN estabelecimentos e ON e.cnpj_basico = x.cnpj_basico AND e.identificador_matriz_filial = 1
    WHERE btrim(x.nome) <> ''
    ON CONFLICT (cnpj_basico) DO NOTHING;

INSERT INTO busca_fantasia (cnpj_basico, cnpj_ordem, cnpj_dv, nome, uf, situacao)
    SELECT cnpj_basico, cnpj_ordem, cnpj_dv, nome, uf, situacao_cadastral FROM (
        SELECT cnpj_basico, cnpj_ordem, cnpj_dv, busca_norm(nome_fantasia) AS nome, uf, situacao_cadastral
        FROM estabelecimentos
        WHERE nome_fantasia IS NOT NULL AND nome_fantasia <> ''
    ) x
    WHERE btrim(nome) <> ''
    ON CONFLICT (cnpj_basico, cnpj_ordem, cnpj_dv) DO NOTHING;

CREATE INDEX idx_busca_razao_gin ON busca_razao USING gin (uf, situacao, nome gin_trgm_ops);
CREATE INDEX idx_busca_fantasia_gin ON busca_fantasia USING gin (uf, situacao, nome gin_trgm_ops);
ANALYZE busca_razao;
ANALYZE busca_fantasia;
SQL

"$DIR/bin/papel-app.sh"
echo "==> [$(date -Is)] busca por nome pronta"
