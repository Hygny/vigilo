#!/usr/bin/env bash
#
# Baixa a Dívida Ativa da União (PGFN, dados abertos) de um trimestre e importa
# para o Vigilo filtrando pela CARTEIRA. Roda no HOST do VPS.
#
# O dump oficial é enorme (o "Não Previdenciário" passa de 1 GB zipado / dezenas
# de GB abertos) e vem num ZIP por sistema, com um CSV POR UF dentro (SP quebrado
# em partes). Para não estourar o disco, este script NÃO descompacta tudo: ele
# faz streaming de cada CSV (`unzip -p`) e filtra pela carteira ANTES de tocar no
# container (`grep -F`), importando só um CSV já pequeno por tipo. O importador
# ainda refiltra pela carteira no PHP, então o grep é só para reduzir volume.
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
# Requer: curl, unzip. Recarrega POR TIPO (rodar um tipo substitui só ele).
set -uo pipefail

APP_DIR="$HOME/apps/vigilo"
COMPOSE="docker compose -f $APP_DIR/docker-compose.yml"
BASE_URL="https://dadosabertos.pgfn.gov.br"

cd "$APP_DIR" || exit 1
command -v curl  >/dev/null || { echo "!! curl não encontrado";  exit 1; }
command -v unzip >/dev/null || { echo "!! unzip não encontrado"; exit 1; }

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

quarter_exists() { curl -fsIL --max-time 60 "$BASE_URL/$1/Dados_abertos_FGTS.zip" >/dev/null 2>&1; }

# Detecta o trimestre mais novo disponível (probe do ano atual para trás).
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
PAT="$WORK/patterns.txt"

# Carteira (host): dígitos crus + formato pontuado → grep -F pega os dois jeitos.
echo "==> coletando a carteira..."
$COMPOSE exec -T app php artisan vigilo:carteira-cnpjs </dev/null > "$WORK/carteira.txt" \
    || { echo "!! não consegui listar a carteira"; exit 1; }
sed -E 's#^([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})$#\1.\2.\3/\4-\5#' \
    "$WORK/carteira.txt" > "$WORK/carteira_fmt.txt"
sort -u "$WORK/carteira.txt" "$WORK/carteira_fmt.txt" > "$PAT"
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
    members=$(unzip -Z1 "$zip" 2>/dev/null | grep -iE '\.csv$' || true)
    if [ -z "$members" ]; then
        echo "!! [$tipo] nenhum .csv dentro do zip"; rm -f "$zip"; rc=1; continue
    fi

    : > "$out"
    header_done=0
    while IFS= read -r m; do
        [ -z "$m" ] && continue
        if [ "$header_done" -eq 0 ]; then
            unzip -p "$zip" "$m" | head -1 > "$out" # cabeçalho uma vez
            header_done=1
        fi
        unzip -p "$zip" "$m" | tail -n +2 | grep -F -f "$PAT" >> "$out" || true
    done <<< "$members"

    n=$(( $(grep -c . "$out" || echo 1) - 1 )); [ "$n" -lt 0 ] && n=0
    echo "==> [$tipo] $n linha(s) candidata(s) após o filtro"
    rm -f "$zip"

    echo "==> [$tipo] importando no container"
    if ! $COMPOSE cp "$out" app:/tmp/pgfn.csv; then
        echo "!! [$tipo] falha ao copiar para o container"; rm -f "$out"; rc=1; continue
    fi
    $COMPOSE exec -T app php artisan vigilo:importar-pgfn /tmp/pgfn.csv --tipo="$tipo" </dev/null \
        || { echo "!! [$tipo] import falhou"; rc=1; }
    $COMPOSE exec -T app rm -f /tmp/pgfn.csv </dev/null || true
    rm -f "$out"
done

echo "==> concluído (trimestre $QUARTER)"
exit "$rc"
