#!/usr/bin/env bash
#
# Baixa a Dívida Ativa da União (PGFN, dados abertos) de um trimestre e importa
# para o Vigilo filtrando pela CARTEIRA. Roda no HOST do VPS.
#
# O dump oficial é enorme (o "Não Previdenciário" passa de 1 GB zipado / dezenas
# de GB abertos) e vem num ZIP por sistema, com vários CSVs dentro. Para não
# estourar o disco, NÃO descompactamos tudo: o pgfn_filter.py (ao lado deste
# arquivo) lê cada CSV em streaming (stdlib zipfile) e mantém só as linhas da
# carteira ANTES de tocar no container — importando um CSV já pequeno por tipo.
# O filtro tira a pontuação do documento (igual ao importador PHP), então é
# imune ao formato do dump. Usa python3 (não precisa de `unzip`).
#
# Fonte: https://dadosabertos.pgfn.gov.br (Dívida Ativa da União).
#
# Uso (no host):
#   bash ~/apps/vigilo/src/deploy/baixar-importar-pgfn.sh [TRIMESTRE] [tipos...]
# Exemplos:
#   bash .../baixar-importar-pgfn.sh                     # trimestre mais novo, os 3 tipos
#   bash .../baixar-importar-pgfn.sh 2026_trimestre_02   # trimestre fixo
#   bash .../baixar-importar-pgfn.sh latest fgts previdenciario
#
# Requer: curl, python3. Recarrega POR TIPO (rodar um tipo substitui só ele).
set -uo pipefail

APP_DIR="$HOME/apps/vigilo"
COMPOSE="docker compose -f $APP_DIR/docker-compose.yml"
BASE_URL="https://dadosabertos.pgfn.gov.br"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
FILTER="$SCRIPT_DIR/pgfn_filter.py"

cd "$APP_DIR" || exit 1
command -v curl    >/dev/null || { echo "!! curl não encontrado"; exit 1; }
command -v python3 >/dev/null || { echo "!! python3 não encontrado (instale com: sudo apt-get install -y python3)"; exit 1; }
[ -f "$FILTER" ] || { echo "!! não achei $FILTER"; exit 1; }

# tipo interno  ->  nome do arquivo (sistema) no CDN
declare -A SISTEMA=(
    [nao-previdenciario]=Dados_abertos_Nao_Previdenciario
    [previdenciario]=Dados_abertos_Previdenciario
    [fgts]=Dados_abertos_FGTS
)

QUARTER="${1:-latest}"
[ "$#" -gt 0 ] && shift
TIPOS=("$@")
[ "${#TIPOS[@]}" -eq 0 ] && TIPOS=(nao-previdenciario previdenciario fgts)

# Sonda o tipo MAIS PESADO: um trimestre só é "pronto" quando o não-previdenciário
# (o último a subir) já está publicado — evita escolher um trimestre incompleto.
quarter_exists() { curl -fsIL --max-time 60 "$BASE_URL/$1/Dados_abertos_Nao_Previdenciario.zip" >/dev/null 2>&1; }

if [ "$QUARTER" = latest ]; then
    echo "==> detectando o trimestre mais novo no CDN..."
    Y=$(date +%Y)
    QUARTER=""
    for y in "$Y" "$((Y - 1))"; do
        for q in 04 03 02 01; do
            if quarter_exists "${y}_trimestre_${q}"; then QUARTER="${y}_trimestre_${q}"; break 2; fi
        done
    done
    [ -z "$QUARTER" ] && { echo "!! nenhum trimestre disponível no CDN"; exit 1; }
fi
echo "==> trimestre: $QUARTER"

WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

echo "==> coletando a carteira..."
$COMPOSE exec -T app php artisan vigilo:carteira-cnpjs </dev/null > "$WORK/carteira.txt" \
    || { echo "!! não consegui listar a carteira"; exit 1; }
N_CART=$(grep -c . "$WORK/carteira.txt" || true)
[ "${N_CART:-0}" -eq 0 ] && { echo "!! carteira vazia — nada a importar"; exit 1; }
echo "==> carteira: $N_CART CNPJ(s)"

rc=0
for tipo in "${TIPOS[@]}"; do
    sis="${SISTEMA[$tipo]:-}"
    if [ -z "$sis" ]; then
        echo "!! tipo desconhecido: $tipo (use nao-previdenciario|previdenciario|fgts)"; rc=1; continue
    fi

    url="$BASE_URL/$QUARTER/$sis.zip"
    zip="$WORK/$tipo.zip"
    out="$WORK/$tipo.csv"

    echo "==> [$tipo] baixando $url"
    if ! curl -fL --retry 3 --max-time 7200 -o "$zip" "$url"; then
        echo "!! [$tipo] download falhou"; rc=1; continue
    fi

    echo "==> [$tipo] filtrando pela carteira (streaming, sem descompactar tudo)"
    if ! n=$(python3 "$FILTER" "$zip" "$WORK/carteira.txt" "$out"); then
        echo "!! [$tipo] filtro falhou (zip corrompido ou sem coluna de documento) — nada importado"; rm -f "$zip" "$out"; rc=1; continue
    fi
    echo "==> [$tipo] $n inscrição(ões) da carteira encontrada(s)"
    rm -f "$zip"

    echo "==> [$tipo] importando no container"
    if ! $COMPOSE cp "$out" app:/tmp/pgfn.csv; then
        echo "!! [$tipo] falha ao copiar para o container"; rm -f "$out"; rc=1; continue
    fi
    # --allow-empty: o pgfn_filter.py já validou a estrutura; zero inscrição é
    # legítimo (a carteira pode não ter dívida neste tipo) e deve limpar o tipo.
    $COMPOSE exec -T app php artisan vigilo:importar-pgfn /tmp/pgfn.csv --tipo="$tipo" --allow-empty </dev/null \
        || { echo "!! [$tipo] import falhou"; rc=1; }
    $COMPOSE exec -T app rm -f /tmp/pgfn.csv </dev/null || true
    rm -f "$out"
done

echo "==> concluído (trimestre $QUARTER)"
exit "$rc"
