#!/usr/bin/env bash
#
# Roda o pipeline da Receita (upsert) contra a base desta VM. Chamado pelo
# deploy/reimport-mensal.sh do VPS via SSH (verbo `reimport` do gateway.sh).
# UMA tentativa por chamada — o retry fica no VPS.
#
# O pipeline roda num container NOMEADO e destacado: se o SSH do VPS cair no
# meio, o import continua; a chamada seguinte (retry) encontra o container
# rodando e só espera por ele, em vez de começar outro em paralelo.
#
# Saída 3 = sem espaço em /data (não adianta re-tentar).
set -euo pipefail

DIR="$(dirname "$(dirname "$(readlink -f "$0")")")"
NAME=cnpj-reimport
MIN_FREE_GB="${MIN_FREE_GB:-25}"
# Ao reconectar a um import em andamento, só o log daqui pra frente (o
# anterior já foi para o cnpj-mensal.log do VPS).
LOG_ARGS=(--since "$(date -u +%Y-%m-%dT%H:%M:%SZ)")

set -a
# shellcheck disable=SC1091
. "$DIR/.env"
set +a

if [ "$(docker inspect -f '{{.State.Running}}' "$NAME" 2>/dev/null)" = true ]; then
    echo "==> [$(date -Is)] reimport já em andamento na VM — aguardando o término"
else
    # O upsert reescreve todas as linhas e o volume cresce antes de o
    # autovacuum reciclar o espaço: melhor abortar do que lotar o disco.
    free_gb="$(df -BG --output=avail /data | tail -1 | tr -dc '0-9')"
    case "$free_gb" in
        '' | *[!0-9]*)
            echo "!! [$(date -Is)] não consegui medir o espaço livre em /data — reimport abortado"
            exit 3
            ;;
    esac
    if [ "$free_gb" -lt "$MIN_FREE_GB" ]; then
        echo "!! [$(date -Is)] /data com ${free_gb} GB livres (mínimo ${MIN_FREE_GB}) — reimport abortado"
        exit 3
    fi
    # A limpeza do container da rodada anterior acontece só aqui, no início de
    # uma rodada nova — nunca depois do wait (uma chamada órfã removeria o
    # container sob uma reconexão que ainda espera por ele).
    docker rm "$NAME" >/dev/null 2>&1 || true
    docker run -d --name "$NAME" --network host \
        -e DATABASE_URL="postgres://cnpj:${CNPJ_DB_PASSWORD}@127.0.0.1:5432/cnpj" \
        -e LOADING_STRATEGY=upsert \
        ghcr.io/caiopizzol/cnpj-data-pipeline >/dev/null
    LOG_ARGS=() # rodada nova: log inteiro
fi

docker logs -f "${LOG_ARGS[@]}" "$NAME" 2>&1 || true
code="$(docker wait "$NAME")"

# As tabelas têm de seguir com o papel do app (uma versão nova do pipeline
# que crie/recrie tabela as faria nascer do superuser). Idempotente.
if [ "$code" = 0 ]; then
    "$DIR/bin/papel-app.sh" || echo "!! [$(date -Is)] papel-app.sh falhou — rode ~/cnpj/bin/papel-app.sh na VM"
fi

# O 3 é reservado à checagem de disco acima (o VPS não re-tenta): um 3 vindo
# do próprio pipeline vira falha comum.
[ "$code" = 3 ] && code=1
exit "$code"
