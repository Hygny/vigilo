#!/usr/bin/env bash
#
# Manutenção da base CNPJ, chamada pelo cron do host. Roda SEMANALMENTE
# (a Receita publica ~1x/mês, sem dia fixo — rodar toda semana garante pegar o
# dump novo em até 7 dias, sem depender de acertar o dia da publicação).
#
# Fluxo:
#   1) reimport do dump da Receita (upsert — não dropa as tabelas);
#   2) SÓ se o reimport trouxe dados novos (processed_files cresceu):
#        2a) re-coleta da carteira (gera os alertas do que mudou entre dumps);
#        2b) normalização de sócios (refresca socios.nome_norm da busca OSINT).
#   Nas semanas sem dump novo, (2) é pulado — a rodada é um no-op de segundos.
#
# Ao final (e também quando o reimport aborta) grava uma linha em
# `maintenance_runs` via `vigilo:maintenance-record`, que o super-admin vê em
# /admin. Como grava TODA semana, o card prova que o cron está vivo.
#
# Regras de falha:
#   - se o REIMPORT falhar, aborta (não mexe em dado parcial);
#   - (2a) e (2b) são independentes: uma falhar não impede a outra.
#
# Cron sugerido (toda segunda, 03:00):
#   0 3 * * 1 /usr/bin/bash ~/apps/vigilo/src/deploy/reimport-mensal.sh >> ~/apps/vigilo/cnpj-mensal.log 2>&1
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
PG_USER="${CNPJ_DB_USERNAME:-cnpj}"
PG_PASS="${CNPJ_DB_PASSWORD:?defina CNPJ_DB_PASSWORD em ~/apps/vigilo/.env}"
PG_DSN="postgres://${PG_USER}:${PG_PASS}@data-postgres-1:5432/cnpj"

STARTED_AT="$(date -Is)"
REIMPORT=skip
RECOLETA=skip
NORMALIZAR=skip
MSG=""

is_num() { case "${1:-}" in '' | *[!0-9]*) return 1 ;; *) return 0 ;; esac }

# Quantos arquivos o pipeline já processou (idempotência da Receita vive aqui).
# Vazio se a tabela não existir / não der pra consultar.
pf_count() {
    $COMPOSE exec -T -e PGPASSWORD="$PG_PASS" postgres \
        psql -U "$PG_USER" -d cnpj -tAc "SELECT count(*) FROM processed_files" 2>/dev/null | tr -d '[:space:]'
}

record_run() {
    $COMPOSE exec -T app php artisan vigilo:maintenance-record \
        --kind=cnpj_mensal --started="$STARTED_AT" \
        --reimport="$REIMPORT" --recoleta="$RECOLETA" --normalizar="$NORMALIZAR" \
        --message="$MSG" \
        || echo "!! [$(date -Is)] não consegui registrar o maintenance-run"
}

BEFORE="$(pf_count)"

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

AFTER="$(pf_count)"

# Houve dump novo? Sim se a contagem de arquivos processados subiu. Se não deu
# pra contar (tabela ausente etc.), roda os passos por segurança.
if is_num "$BEFORE" && is_num "$AFTER"; then
    [ "$AFTER" -gt "$BEFORE" ] && NEW_DATA=1 || NEW_DATA=0
else
    NEW_DATA=1
    MSG="${MSG:+$MSG; }contagem de processed_files indisponível; rodei os passos por segurança"
fi

if [ "$NEW_DATA" = 0 ]; then
    echo "==> [$(date -Is)] sem dump novo (processed_files: ${BEFORE} → ${AFTER}) — pulando recoleta/normalizar"
    MSG="${MSG:+$MSG; }sem dump novo (base já atualizada)"
    record_run
    echo "==> [$(date -Is)] manutenção concluída (no-op)"
    exit 0
fi

echo "==> [$(date -Is)] dump novo detectado (processed_files: ${BEFORE} → ${AFTER})"

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
echo "==> [$(date -Is)] manutenção concluída"
