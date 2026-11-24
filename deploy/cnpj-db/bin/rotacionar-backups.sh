#!/usr/bin/env bash
#
# Rotação dos backups diários do MySQL do Vigilo (db-AAAA-MM-DD.sql.gz) num
# diretório: mantém os <diarios> mais recentes + o 1º backup de cada um dos
# <mensais> meses mais recentes; apaga o resto.
#
#   rotacionar-backups.sh <dir> <diarios> <mensais>
#
# Usado nos dois lados: no VPS (deploy/backup-db.sh, 30 + 12) e na VM
# (bin/guardar-backup.sh, 90 + 24).
#
# Seguro por construção:
#   - só toca arquivos com o nome EXATO db-AAAA-MM-DD.sql.gz, no próprio
#     diretório — não desce em subpastas (ex.: ~/backups/solradar, de outro app,
#     nunca é tocado) nem pega temporários (.db-*.tmp);
#   - conta ARQUIVOS, não dias: se o backup parar por semanas, os últimos
#     <diarios> continuam lá — um buraco no calendário nunca apaga tudo.
set -euo pipefail
export LC_ALL=C # ordem do glob = ordem cronológica (AAAA-MM-DD)

dir="${1:-}"
diarios="${2:-}"
mensais="${3:-}"

num() { case "${1:-}" in '' | *[!0-9]*) return 1 ;; esac; }
if [ -z "$dir" ] || [ ! -d "$dir" ] || ! num "$diarios" || ! num "$mensais" || [ "$diarios" -lt 1 ]; then
    echo "uso: rotacionar-backups.sh <dir existente> <diarios ≥ 1> <mensais ≥ 0>" >&2
    exit 2
fi

shopt -s nullglob
arquivos=("$dir"/db-[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9].sql.gz)
total=${#arquivos[@]}

[ "$total" -gt "$diarios" ] || exit 0

declare -A manter=()

# Os <diarios> mais recentes.
for ((i = total - diarios; i < total; i++)); do
    manter["${arquivos[$i]}"]=1
done

# O 1º backup de cada mês, para os <mensais> meses mais recentes.
declare -A primeiro=()
meses=()
for f in "${arquivos[@]}"; do
    base="${f##*/}"
    mes="${base:3:7}" # db-AAAA-MM-... → AAAA-MM
    if [ -z "${primeiro[$mes]:-}" ]; then
        primeiro[$mes]="$f"
        meses+=("$mes")
    fi
done
for ((i = ${#meses[@]} - mensais; i < ${#meses[@]}; i++)); do
    [ "$i" -ge 0 ] && manter["${primeiro[${meses[$i]}]}"]=1
done

for f in "${arquivos[@]}"; do
    [ -n "${manter[$f]:-}" ] && continue
    rm -f -- "$f"
    echo "rotação: apagado ${f##*/}"
done
