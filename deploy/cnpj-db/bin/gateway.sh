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
    # Só para a cópia inicial (Fase 2 do roteiro) — remover depois da migração.
    receive-dump) exec cat >/data/dump/cnpj.dump ;;
    *)
        echo "comando não permitido: ${SSH_ORIGINAL_COMMAND:-<vazio>}" >&2
        exit 2
        ;;
esac
