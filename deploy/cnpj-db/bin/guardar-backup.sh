#!/usr/bin/env bash
#
# Cópia FORA do VPS dos backups do MySQL do Vigilo. Recebe o arquivo pelo stdin
# (verbo `guardar-backup <nome>` do gateway.sh — o VPS manda com
# deploy/backup-db.sh) e guarda em /data/backups/vigilo:
#   1) só aceita nome db-AAAA-MM-DD.sql.gz (nada de caminho) com data REAL e
#      não futura (folga de 1 dia p/ fuso);
#   2) grava num temporário (teto de tamanho) e VALIDA antes de aceitar: gzip
#      íntegro + dump completo (última linha "-- Dump completed");
#   3) NUNCA sobrescreve um backup já guardado: reenvio idêntico é no-op (devolve
#      o sha — o `--enviar-existentes` fica idempotente); conteúdo diferente com o
#      mesmo nome é recusado;
#   4) troca atômica (mv) — um envio que cai no meio nunca vira backup;
#   5) rotação: 90 mais recentes + o 1º de cada um dos 24 últimos meses
#      (bin/rotacionar-backups.sh);
#   6) imprime o sha256 do arquivo guardado — o VPS confere com o do original.
#
# (1) e (3) blindam a cópia contra quem tiver a chave do VPS: sem eles, nomes
# "do futuro" entrariam na rotação como os mais recentes e empurrariam os
# backups reais para fora, e um gzip qualquer substituiria um backup bom.
#
# Saídas: 0 ok · 2 nome/data inválidos · 3 sem espaço em /data · 4 arquivo
# corrompido/incompleto/grande demais (descartado) · 6 já existe com conteúdo
# diferente (recusado; o guardado fica).
set -euo pipefail
export LC_ALL=C

BIN="$(dirname "$(readlink -f "$0")")"
DEST="${VIGILO_BACKUP_DIR:-/data/backups/vigilo}"
DIARIOS="${VIGILO_BACKUP_DIARIOS:-90}"
MENSAIS="${VIGILO_BACKUP_MENSAIS:-24}"
MIN_LIVRE_MB="${VIGILO_BACKUP_MIN_LIVRE_MB:-2048}"
MAX_BYTES="${VIGILO_BACKUP_MAX_BYTES:-536870912}" # 512 MiB (hoje ~4 MB)

nome="${1:-}"
if ! [[ "$nome" =~ ^db-[0-9]{4}-[0-9]{2}-[0-9]{2}\.sql\.gz$ ]]; then
    echo "nome de backup inválido: ${nome:-<vazio>}" >&2
    exit 2
fi
dia="${nome:3:10}"
if [ "$(date -d "$dia" +%F 2>/dev/null)" != "$dia" ] || [[ "$dia" > "$(date -d '+1 day' +%F)" ]]; then
    echo "data inválida ou futura no nome do backup: ${nome}" >&2
    exit 2
fi

umask 077
mkdir -p "$DEST"

livre_mb="$(df -Pm "$DEST" | awk 'NR == 2 { print $4 }')"
if ! [ "${livre_mb:-0}" -ge "$MIN_LIVRE_MB" ] 2>/dev/null; then
    echo "sem espaço em ${DEST} (${livre_mb:-?} MB livres, mínimo ${MIN_LIVRE_MB}) — backup ${nome} recusado" >&2
    exit 3
fi

tmp="$DEST/.${nome}.$$.tmp"
trap 'rm -f "$tmp"' EXIT

head -c "$((MAX_BYTES + 1))" >"$tmp"

if [ "$(wc -c <"$tmp")" -gt "$MAX_BYTES" ]; then
    echo "backup ${nome} maior que o teto (${MAX_BYTES} bytes) — descartado" >&2
    exit 4
fi
if ! gzip -t "$tmp" 2>/dev/null; then
    echo "backup ${nome} corrompido (gzip inválido) — descartado" >&2
    exit 4
fi
if ! zcat "$tmp" | tail -n 1 | grep -q '^-- Dump completed'; then
    echo "backup ${nome} incompleto (sem '-- Dump completed' no fim) — descartado" >&2
    exit 4
fi

if [ -e "$DEST/$nome" ]; then
    if cmp -s "$tmp" "$DEST/$nome"; then
        sha256sum "$DEST/$nome" | cut -d' ' -f1 # reenvio idêntico: nada a fazer
        exit 0
    fi
    echo "a VM já guarda ${nome} com conteúdo diferente — recusado (backup guardado nunca é sobrescrito)" >&2
    exit 6
fi

mv -f "$tmp" "$DEST/$nome"
trap - EXIT

"$BIN/rotacionar-backups.sh" "$DEST" "$DIARIOS" "$MENSAIS" >&2

sha256sum "$DEST/$nome" | cut -d' ' -f1
