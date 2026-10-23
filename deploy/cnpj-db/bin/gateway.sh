#!/usr/bin/env bash
#
# Comando forçado da chave SSH do VPS na VM. No ~/.ssh/authorized_keys:
#   restrict,command="/home/ubuntu/cnpj/bin/gateway.sh" ssh-ed25519 AAAA... vigilo-vps-cnpj
# O VPS só consegue pedir os verbos abaixo — nada de shell na VM.
set -euo pipefail

BIN="$(dirname "$(readlink -f "$0")")"

case "${SSH_ORIGINAL_COMMAND:-}" in
    reimport) exec "$BIN/reimport.sh" ;;
    pf-count) exec "$BIN/pf-count.sh" ;;
    # (O verbo `receive-dump` da cópia inicial saiu após a migração — se um dia
    # precisar re-copiar, ver a Fase 2 de docs/vigilo-cnpj-oracle-migracao.md.)
    *)
        echo "comando não permitido: ${SSH_ORIGINAL_COMMAND:-<vazio>}" >&2
        exit 2
        ;;
esac
