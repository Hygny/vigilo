#!/usr/bin/env bash
#
# Carrega a Dívida Ativa da União (PGFN, dados abertos) INTEIRA na base desta
# VM — tabela `pgfn_dividas`, só devedores pessoa jurídica. Dá o sinal de
# dívida ativa para QUALQUER CNPJ (API OSINT, grafo, dossiê, carteira).
#
# Chamado toda semana pelo reimport-mensal.sh do VPS via SSH (verbo `pgfn` do
# gateway.sh). A PGFN publica por trimestre: enquanto o trimestre mais novo do
# CDN já estiver em `pgfn_cargas`, a chamada é um no-op de segundos.
#
# Fluxo: detecta o trimestre → baixa os 3 zips → pgfn_carga.py (streaming, só
# PJ) → COPY numa tabela de CARGA → índice + sanidade → troca atômica com a
# `pgfn_dividas` → registra em `pgfn_cargas`. A tabela em uso só sai depois que
# a nova está pronta: falha no meio deixa a carga anterior servindo.
#
# Uso: pgfn.sh [TRIMESTRE|latest] [--force]
#   pgfn.sh                      # trimestre mais novo; no-op se já carregado
#   pgfn.sh 2026_trimestre_03    # trimestre fixo
#   pgfn.sh latest --force       # recarrega mesmo se já carregado
#
# Saída 3 = sem espaço em /data.
set -euo pipefail

DIR="$(dirname "$(dirname "$(readlink -f "$0")")")"
BASE_URL="https://dadosabertos.pgfn.gov.br"
WORK_ROOT="/data/pgfn"
# Folga para os zips (~1,6 GB) + a tabela nova coexistindo com a antiga, SEM
# encostar no mínimo de 25 GB que o reimport da Receita exige.
MIN_FREE_GB="${PGFN_MIN_FREE_GB:-30}"
# Sanidade: uma carga com bem menos linhas que a anterior (ou quase vazia) é
# layout quebrado/download truncado — não troca.
MIN_ROWS="${PGFN_MIN_ROWS:-1000000}"
MIN_RATIO_PCT="${PGFN_MIN_RATIO_PCT:-50}"

QUARTER="${1:-latest}"
FORCE=0
[ "${2:-}" = --force ] && FORCE=1

log() { echo "==> [$(date -Is)] $*"; }
die() { echo "!! [$(date -Is)] $*" >&2; exit "${2:-1}"; }

psql_cnpj() { docker exec -i cnpj-postgres psql -U cnpj -d cnpj -X -q -v ON_ERROR_STOP=1 "$@"; }

exec 9>/tmp/cnpj-pgfn.lock
flock -n 9 || { log "outra carga da PGFN já em andamento — saindo"; exit 0; }

psql_cnpj -c "CREATE TABLE IF NOT EXISTS pgfn_cargas (
    trimestre varchar(20) PRIMARY KEY,
    inscricoes bigint NOT NULL,
    carregado_em timestamptz NOT NULL DEFAULT now()
)"

# Tabela de carga que sobrou de uma rodada que morreu sem passar pelo trap
# (SIGKILL, reboot): ~7 GB que não podem contar contra a checagem de espaço.
psql_cnpj -c "DROP TABLE IF EXISTS pgfn_dividas_carga" 2>/dev/null

# Falhou depois de criar a tabela de carga (sanidade, layout, COPY): solta o
# espaço na hora — os zips ficam para a próxima tentativa não baixar de novo.
CARGA_CRIADA=0
limpar_carga() {
    local rc=$?
    if [ "$rc" != 0 ] && [ "$CARGA_CRIADA" = 1 ]; then
        psql_cnpj -c "DROP TABLE IF EXISTS pgfn_dividas_carga" >/dev/null 2>&1 || true
    fi
    exit "$rc"
}
trap limpar_carga EXIT

# tipo interno -> nome do arquivo no CDN
declare -A SISTEMA=(
    [nao-previdenciario]=Dados_abertos_Nao_Previdenciario
    [previdenciario]=Dados_abertos_Previdenciario
    [fgts]=Dados_abertos_FGTS
)

# Um trimestre só está "pronto" quando o não-previdenciário (o maior, último a
# subir) já foi publicado.
quarter_exists() { curl -fsIL --max-time 60 "$BASE_URL/$1/Dados_abertos_Nao_Previdenciario.zip" >/dev/null 2>&1; }

if [ "$QUARTER" = latest ]; then
    Y="$(date +%Y)"
    QUARTER=""
    for y in "$Y" "$((Y - 1))"; do
        for q in 04 03 02 01; do
            if quarter_exists "${y}_trimestre_${q}"; then QUARTER="${y}_trimestre_${q}"; break 2; fi
        done
    done
    [ -n "$QUARTER" ] || die "nenhum trimestre da PGFN encontrado no CDN"
fi
[[ "$QUARTER" =~ ^[0-9]{4}_trimestre_0[1-4]$ ]] || die "trimestre inválido: $QUARTER"

if [ "$FORCE" = 0 ] && [ "$(psql_cnpj -tAc "SELECT 1 FROM pgfn_cargas WHERE trimestre = '$QUARTER'")" = 1 ]; then
    log "PGFN ${QUARTER} já carregada — nada a fazer"
    exit 0
fi

free_gb="$(df -BG --output=avail /data | tail -1 | tr -dc '0-9')"
[ -n "$free_gb" ] || die "não consegui medir o espaço livre em /data" 3
[ "$free_gb" -ge "$MIN_FREE_GB" ] || die "/data com ${free_gb} GB livres (mínimo ${MIN_FREE_GB}) — carga da PGFN abortada" 3

WORK="$WORK_ROOT/$QUARTER"
mkdir -p "$WORK" || die "não consegui criar $WORK (a pasta /data/pgfn do ubuntu vem do provisionar-vm.sh)"
log "PGFN ${QUARTER}: baixando os zips em ${WORK}"
ARGS=()
for tipo in nao-previdenciario previdenciario fgts; do
    f="$WORK/$tipo.zip"
    if [ ! -s "$f" ]; then
        curl -fL --retry 3 --retry-delay 30 --max-time 3600 -sS -o "$f.part" "$BASE_URL/$QUARTER/${SISTEMA[$tipo]}.zip"
        mv "$f.part" "$f"
    fi
    ARGS+=("$tipo=$f")
done

log "carregando na tabela de carga (só pessoa jurídica)"
CARGA_CRIADA=1
psql_cnpj <<'SQL'
DROP TABLE IF EXISTS pgfn_dividas_carga;
CREATE TABLE pgfn_dividas_carga (
    cnpj varchar(14) NOT NULL,
    cnpj_basico varchar(8) NOT NULL,
    tipo varchar(20) NOT NULL,
    tipo_devedor text,
    numero_inscricao text,
    tipo_situacao text,
    situacao text,
    receita text,
    data_inscricao date,
    ajuizado boolean NOT NULL,
    valor numeric(16, 2)
);
SQL
python3 "$DIR/bin/pgfn_carga.py" "${ARGS[@]}" \
    | psql_cnpj -c "\copy pgfn_dividas_carga FROM STDIN WITH (FORMAT csv)"

rows="$(psql_cnpj -tAc "SELECT count(*) FROM pgfn_dividas_carga")"
prev="$(psql_cnpj -tAc "SELECT inscricoes FROM pgfn_cargas ORDER BY carregado_em DESC LIMIT 1")"
log "carga com ${rows} inscrições de PJ (anterior: ${prev:-nenhuma})"
[ "$rows" -ge "$MIN_ROWS" ] || die "só ${rows} linhas (mínimo ${MIN_ROWS}) — tabela em uso mantida"
if [ -n "$prev" ] && [ $((rows * 100)) -lt $((prev * MIN_RATIO_PCT)) ]; then
    die "carga nova (${rows}) abaixo de ${MIN_RATIO_PCT}% da anterior (${prev}) — tabela em uso mantida"
fi

log "indexando e trocando pela tabela em uso"
# Índice coberto: o resumo por empresa (contagem/soma/ajuizadas) sai só do índice.
psql_cnpj <<SQL
CREATE INDEX pgfn_dividas_carga_basico ON pgfn_dividas_carga (cnpj_basico) INCLUDE (valor, ajuizado);
BEGIN;
DROP TABLE IF EXISTS pgfn_dividas;
ALTER TABLE pgfn_dividas_carga RENAME TO pgfn_dividas;
ALTER INDEX pgfn_dividas_carga_basico RENAME TO idx_pgfn_dividas_basico;
INSERT INTO pgfn_cargas (trimestre, inscricoes) VALUES ('$QUARTER', $rows)
    ON CONFLICT (trimestre) DO UPDATE SET inscricoes = EXCLUDED.inscricoes, carregado_em = now();
-- O app lê com o papel vigilo_app (dono das tabelas — ver papel-app.sh). Na
-- mesma transação, para a tabela nova nunca ficar ilegível para o app.
DO \$\$
BEGIN
    IF EXISTS (SELECT 1 FROM pg_roles WHERE rolname = 'vigilo_app') THEN
        ALTER TABLE pgfn_dividas OWNER TO vigilo_app;
        ALTER TABLE pgfn_cargas OWNER TO vigilo_app;
    END IF;
END
\$\$;
COMMIT;
ANALYZE pgfn_dividas;
SQL

# Os zips só servem para esta carga (a próxima baixa de novo): libera o disco.
find "$WORK_ROOT" -mindepth 1 -maxdepth 1 -exec rm -rf {} +
log "PGFN ${QUARTER} carregada: ${rows} inscrições de pessoa jurídica"
