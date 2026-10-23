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
    # Só para a cópia inicial (Fase 2 do roteiro) — removido na Fase 5. Grava
    # num .tmp e só troca o arquivo bom se veio algo plausível: o `cat` sai 0
    # no EOF, então stdin vazio ou pg_dump morto no começo não pode apagar a
    # cópia de restauração. (Truncado no meio o pg_restore recusa depois.)
    receive-dump)
        tmp=/data/dump/cnpj.dump.tmp
        cat >"$tmp"
        if [ "$(stat -c %s "$tmp")" -lt 1048576 ]; then
            echo "dump recebido com menos de 1 MB — mantido o anterior" >&2
            rm -f "$tmp"
            exit 1
        fi
        mv -f "$tmp" /data/dump/cnpj.dump
        ;;
    *)
        echo "comando não permitido: ${SSH_ORIGINAL_COMMAND:-<vazio>}" >&2
        exit 2
        ;;
esac
