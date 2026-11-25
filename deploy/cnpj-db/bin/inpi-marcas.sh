#!/usr/bin/env bash
#
# Carrega as MARCAS do INPI (dados abertos, dadosabertos.inpi.gov.br) de
# titulares PESSOA JURÍDICA na base desta VM — tabela `inpi_marcas`, por raiz de
# CNPJ. Marca registrada é bem móvel penhorável: entra no "Bens e recebíveis" e
# no monitoramento de devedora do app.
#
# Chamado toda semana pelo reimport-mensal.sh do VPS via SSH (verbo `inpi` do
# gateway.sh). O INPI republica os CSVs sem dia fixo: enquanto a versão
# (Last-Modified dos dois arquivos) for a já carregada, a chamada é um no-op.
#
# Fluxo: confere a versão → COPY dos dois CSVs (streaming, sem gravar em disco)
# em tabelas de staging UNLOGGED → monta a tabela de CARGA só com titulares PJ
# (CNPJ de 14 dígitos) + dados da marca → sanidade → troca atômica → registra
# em `inpi_cargas`. Falha no meio deixa a carga anterior servindo.
#
# LGPD: titular pessoa física (CPF mascarado pelo INPI) NÃO é guardado.
#
# Uso: inpi-marcas.sh [--force]
# Saída 3 = SÓ sem espaço em /data (o psql também sai 3 em erro de script com
# ON_ERROR_STOP — por isso toda etapa de SQL termina em `|| die ... 1`).
set -euo pipefail

BASE_URL="https://dadosabertos.inpi.gov.br/download/marcas"
DEPOSITANTES="$BASE_URL/MARCAS_DEPOSITANTES.csv"
BIBLIOGRAFICOS="$BASE_URL/MARCAS_DADOS_BIBLIOGRAFICOS.csv"
# Staging (~3 GB) + tabela nova coexistindo com a antiga, sem encostar no
# mínimo de 25 GB do reimport da Receita.
MIN_FREE_GB="${INPI_MIN_FREE_GB:-30}"
MIN_ROWS="${INPI_MIN_ROWS:-500000}"
MIN_RATIO_PCT="${INPI_MIN_RATIO_PCT:-80}"

FORCE=0
[ "${1:-}" = --force ] && FORCE=1

log() { echo "==> [$(date -Is)] $*"; }
die() { echo "!! [$(date -Is)] $*" >&2; exit "${2:-1}"; }

psql_cnpj() { docker exec -i cnpj-postgres psql -U cnpj -d cnpj -X -q -v ON_ERROR_STOP=1 "$@"; }

exec 9>/tmp/cnpj-inpi.lock
flock -n 9 || { log "outra carga do INPI já em andamento — saindo"; exit 0; }

# O app lê com o papel vigilo_app (dono das tabelas — ver papel-app.sh): a tabela
# de controle nasce dele, senão uma 1ª carga que falhe deixa o app sem ler nada.
psql_cnpj <<'SQL' || die "não consegui criar inpi_cargas" 1
CREATE TABLE IF NOT EXISTS inpi_cargas (
    versao text PRIMARY KEY,
    marcas bigint NOT NULL,
    carregado_em timestamptz NOT NULL DEFAULT now()
);
DO $$
BEGIN
    IF EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'vigilo_app') THEN
        ALTER TABLE inpi_cargas OWNER TO vigilo_app;
    END IF;
END
$$;
SQL

# Versão publicada = Last-Modified dos dois arquivos.
ultima_mod() { curl -fsIL --max-time 60 "$1" | tr -d '\r' | awk -F': ' 'tolower($1) == "last-modified" { v = $2 } END { print v }'; }
VERSAO="$(ultima_mod "$DEPOSITANTES") | $(ultima_mod "$BIBLIOGRAFICOS")"
[[ "$VERSAO" =~ [0-9]{4}.*\|.*[0-9]{4} ]] || die "não consegui ler a versão dos arquivos do INPI (fora do ar?)"

EM_USO="$(psql_cnpj -tAc "SELECT versao FROM inpi_cargas ORDER BY carregado_em DESC LIMIT 1")"
if [ "$FORCE" = 0 ] && [ "$VERSAO" = "$EM_USO" ]; then
    log "marcas do INPI (${VERSAO}) já carregadas — nada a fazer"
    exit 0
fi

# Sobras de uma rodada que morreu sem passar pelo trap (SIGKILL, reboot): ~3 GB
# que não podem contar contra a checagem de espaço.
psql_cnpj -c "DROP TABLE IF EXISTS inpi_stg_depositantes, inpi_stg_marcas, inpi_marcas_carga" >/dev/null 2>&1 || true

free_gb="$(df -BG --output=avail /data | tail -1 | tr -dc '0-9')"
[ -n "$free_gb" ] || die "não consegui medir o espaço livre em /data" 3
[ "$free_gb" -ge "$MIN_FREE_GB" ] || die "/data com ${free_gb} GB livres (mínimo ${MIN_FREE_GB}) — carga do INPI abortada" 3

limpar() {
    local rc=$?
    psql_cnpj -c "DROP TABLE IF EXISTS inpi_stg_depositantes, inpi_stg_marcas, inpi_marcas_carga" >/dev/null 2>&1 || true
    exit "$rc"
}
trap limpar EXIT

log "staging: baixando e copiando os CSVs do INPI (${VERSAO})"
psql_cnpj <<'SQL' || die "não consegui criar as tabelas de staging" 1
DROP TABLE IF EXISTS inpi_stg_depositantes, inpi_stg_marcas, inpi_marcas_carga;
CREATE UNLOGGED TABLE inpi_stg_depositantes (
    codigo_interno text, numero_inpi text, nome text, cnpj_cpf_titular text, tipo_pfpj_titular text,
    nome_representante_legal text, cnpj_cpf_representante_legal text, tipo_pfpj_representante_legal text,
    estado text, pais text
);
CREATE UNLOGGED TABLE inpi_stg_marcas (
    codigo_interno text, numero_inpi text, data_deposito text, data_publicacao text, data_concessao text,
    data_vigencia text, descricao_apresentacao text, descricao_natureza text, elemento_nominativo text,
    traducao text, apostila text, codigo_situacao text, descricao_situacao text
);
SQL
curl -fsSL --max-time 3600 "$DEPOSITANTES" \
    | psql_cnpj -c "\copy inpi_stg_depositantes FROM STDIN WITH (FORMAT csv, HEADER true)" \
    || die "download/COPY de MARCAS_DEPOSITANTES falhou" 1
curl -fsSL --max-time 3600 "$BIBLIOGRAFICOS" \
    | psql_cnpj -c "\copy inpi_stg_marcas FROM STDIN WITH (FORMAT csv, HEADER true)" \
    || die "download/COPY de MARCAS_DADOS_BIBLIOGRAFICOS falhou" 1

log "montando a tabela de carga (só titulares pessoa jurídica)"
psql_cnpj <<'SQL' || die "montagem da tabela de carga falhou" 1
CREATE TABLE inpi_marcas_carga AS
SELECT DISTINCT ON (d.numero_inpi, d.cnpj_cpf_titular)
    d.numero_inpi::varchar(12) AS numero_inpi,
    d.cnpj_cpf_titular::varchar(14) AS cnpj,
    substr(d.cnpj_cpf_titular, 1, 8)::varchar(8) AS cnpj_basico,
    left(d.nome, 200) AS titular,
    left(m.elemento_nominativo, 200) AS marca,
    left(m.descricao_apresentacao, 40) AS apresentacao,
    left(m.descricao_natureza, 40) AS natureza,
    left(m.codigo_situacao, 12) AS codigo_situacao,
    left(m.descricao_situacao, 120) AS situacao,
    CASE WHEN m.data_deposito ~ '^\d{4}-\d{2}-\d{2}$' THEN m.data_deposito::date END AS data_deposito,
    CASE WHEN m.data_concessao ~ '^\d{4}-\d{2}-\d{2}$' THEN m.data_concessao::date END AS data_concessao,
    CASE WHEN m.data_vigencia ~ '^\d{4}-\d{2}-\d{2}$' THEN m.data_vigencia::date END AS data_vigencia
FROM inpi_stg_depositantes d
LEFT JOIN inpi_stg_marcas m ON m.numero_inpi = d.numero_inpi
WHERE d.tipo_pfpj_titular ILIKE 'PESSOA JUR%'
  AND d.cnpj_cpf_titular ~ '^\d{14}$'
  AND d.numero_inpi ~ '^\d{1,12}$'
ORDER BY d.numero_inpi, d.cnpj_cpf_titular;
DROP TABLE inpi_stg_depositantes, inpi_stg_marcas;
SQL

rows="$(psql_cnpj -tAc "SELECT count(*) FROM inpi_marcas_carga")"
prev="$(psql_cnpj -tAc "SELECT marcas FROM inpi_cargas ORDER BY carregado_em DESC LIMIT 1")"
log "carga com ${rows} marcas de PJ (anterior: ${prev:-nenhuma})"
[ "$rows" -ge "$MIN_ROWS" ] || die "só ${rows} marcas (mínimo ${MIN_ROWS}) — tabela em uso mantida"
if [ -n "$prev" ] && [ $((rows * 100)) -lt $((prev * MIN_RATIO_PCT)) ]; then
    die "carga nova (${rows}) abaixo de ${MIN_RATIO_PCT}% da anterior (${prev}) — tabela em uso mantida"
fi

log "indexando e trocando pela tabela em uso"
VERSAO_SQL="${VERSAO//\'/\'\'}"
psql_cnpj <<SQL || die "índices/troca da tabela falharam — a anterior segue em uso" 1
ALTER TABLE inpi_marcas_carga ADD PRIMARY KEY (numero_inpi, cnpj);
CREATE INDEX inpi_marcas_carga_basico ON inpi_marcas_carga (cnpj_basico);
BEGIN;
DROP TABLE IF EXISTS inpi_marcas;
ALTER TABLE inpi_marcas_carga RENAME TO inpi_marcas;
ALTER INDEX inpi_marcas_carga_pkey RENAME TO inpi_marcas_pkey;
ALTER INDEX inpi_marcas_carga_basico RENAME TO idx_inpi_marcas_basico;
INSERT INTO inpi_cargas (versao, marcas) VALUES ('$VERSAO_SQL', $rows)
    ON CONFLICT (versao) DO UPDATE SET marcas = EXCLUDED.marcas, carregado_em = now();
DO \$\$
BEGIN
    IF EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'vigilo_app') THEN
        ALTER TABLE inpi_marcas OWNER TO vigilo_app;
        ALTER TABLE inpi_cargas OWNER TO vigilo_app;
    END IF;
END
\$\$;
COMMIT;
ANALYZE inpi_marcas;
SQL

log "marcas do INPI carregadas: ${rows} de pessoa jurídica"
