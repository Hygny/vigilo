#!/usr/bin/env bash
#
# Comando forçado da chave SSH do VPS na VM. No ~/.ssh/authorized_keys:
#   restrict,command="/home/ubuntu/cnpj/bin/gateway.sh" ssh-ed25519 AAAA... vigilo-vps-cnpj
# O VPS só consegue pedir os verbos abaixo — nada de shell na VM. O
# `guardar-backup <nome>` recebe o backup do MySQL do Vigilo pelo stdin
# (cópia fora do VPS — deploy/backup-db.sh).
set -euo pipefail

BIN="$(dirname "$(readlink -f "$0")")"

case "${SSH_ORIGINAL_COMMAND:-}" in
    reimport) exec "$BIN/reimport.sh" ;;
    pf-count) exec "$BIN/pf-count.sh" ;;
    pgfn) exec "$BIN/pgfn.sh" ;;
    inpi) exec "$BIN/inpi-marcas.sh" ;;
    # Backup do MySQL do Vigilo pelo stdin; o nome é validado no script.
    "guardar-backup "*) exec "$BIN/guardar-backup.sh" "${SSH_ORIGINAL_COMMAND#guardar-backup }" ;;
    # (O verbo `receive-dump` da cópia inicial saiu após a migração — se um dia
    # precisar re-copiar, ver a Fase 2 de docs/vigilo-cnpj-oracle-migracao.md.)
    *)
        echo "comando não permitido: ${SSH_ORIGINAL_COMMAND:-<vazio>}" >&2
        exit 2
        ;;
esac
