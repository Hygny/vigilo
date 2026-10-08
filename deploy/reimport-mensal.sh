#!/usr/bin/env bash
#
# Manutenção mensal da base CNPJ, chamada pelo cron do host:
#   1) reimport do dump da Receita (upsert — não dropa as tabelas);
#   2) re-coleta da carteira (gera os alertas do que mudou entre dumps);
#   3) normalização de sócios (refresca socios.nome_norm da busca OSINT).
#
# Ao final (e também quando o reimport aborta) grava uma linha em
# `maintenance_runs` via `vigilo:maintenance-record`, que o super-admin vê em
# /admin (data da última execução, status de cada passo, erro).
#
# Regras de falha:
#   - se o REIMPORT falhar, aborta (não mexe em dado parcial);
#   - (2) e (3) são independentes: uma falhar não impede a outra.
#
# Cron sugerido (dia 5, 03:00, fora de pico):
#   0 3 5 * * /usr/bin/bash ~/apps/vigilo/src/deploy/reimport-mensal.sh >> ~/apps/vigilo/cnpj-mensal.log 2>&1
#
# Ver docs/vigilo-v2-f1-runbook.md.
set -uo pipefail

APP_DIR="$HOME/apps/vigilo"
COMPOSE="docker compose -f $APP_DIR/docker-compose.yml"

cd "$APP_DIR" || exit 1

# Credenciais do Postgres (as mesmas do compose).
set -a
# shellcheck disable=SC1091
. "$APP_DIR/.env"
set +a
PG_DSN="postgres://${CNPJ_DB_USERNAME:-cnpj}:${CNPJ_DB_PASSWORD:?defina CNPJ_DB_PASSWORD em ~/apps/vigilo/.env}@data-postgres-1:5432/cnpj"

STARTED_AT="$(date -Is)"
REIMPORT=skip
RECOLETA=skip
NORMALIZAR=skip
MSG=""

record_run() {
    $COMPOSE exec -T app php artisan vigilo:maintenance-record \
        --kind=cnpj_mensal --started="$STARTED_AT" \
        --reimport="$REIMPORT" --recoleta="$RECOLETA" --normalizar="$NORMALIZAR" \
        --message="$MSG" \
        || echo "!! [$(date -Is)] não consegui registrar o maintenance-run"
}

echo "==> [$(date -Is)] reimport (upsert)"
if docker run --rm --network data \
      -e DATABASE_URL="$PG_DSN" \
      -e LOADING_STRATEGY=upsert \
      ghcr.io/caiopizzol/cnpj-data-pipeline; then
    REIMPORT=ok
else
    REIMPORT=fail
    MSG="reimport falhou (ver cnpj-mensal.log)"
    echo "!! [$(date -Is)] reimport FALHOU — abortando (dado pode estar parcial)"
    record_run
    exit 1
fi

echo "==> [$(date -Is)] re-coleta da carteira (alertas)"
if $COMPOSE exec -T app php artisan vigilo:recoletar-carteira; then
    RECOLETA=ok
else
    RECOLETA=fail
    MSG="${MSG:+$MSG; }recoletar-carteira falhou"
    echo "!! [$(date -Is)] recoletar-carteira falhou (segue para normalizar)"
fi

echo "==> [$(date -Is)] normalizar sócios (nome_norm da busca por sócio)"
if $COMPOSE exec -T app php artisan vigilo:normalizar-socios; then
    NORMALIZAR=ok
else
    NORMALIZAR=fail
    MSG="${MSG:+$MSG; }normalizar-socios falhou"
    echo "!! [$(date -Is)] normalizar-socios falhou"
fi

record_run
echo "==> [$(date -Is)] manutenção mensal concluída"
