#!/usr/bin/env bash
#
# Refresh INCREMENTAL da PGFN. Roda no HOST, por um cron frequente (~10 min).
#
# Quando um cliente adiciona um CNPJ novo à carteira, ele fica `pgfn_synced_at`
# nulo (pendente). Este script detecta isso e carrega a dívida da PGFN desses
# CNPJs a partir dos ZIPs já em cache (deploy/pgfn-cache), SEM esperar o reimport
# trimestral e SEM rebaixar o dump. Quando não há pendência, é um no-op de
# segundos (só uma consulta rápida à carteira).
#
# OBS (DT-20): quando HÁ pendência, o filtro ainda streama o zip inteiro. Por isso
# a cadência sugerida é 30 min (não 10) — coalesce vários adds numa rodada só e
# evita re-escanear o dump a cada 10 min quando há onboarding em volume.
#
# Como o baixar-importar-pgfn.sh filtra a carteira INTEIRA e reusa o cache, aqui
# só precisamos chamá-lo quando há pendência — ele reimporta tudo do cache e
# marca a carteira como sincronizada ao final. O `flock` lá dentro serializa com
# o reimport trimestral.
#
# Cron sugerido (a cada 30 min):
#   */30 * * * * /usr/bin/bash ~/apps/vigilo/src/deploy/refrescar-pgfn-novos.sh >> ~/apps/vigilo/pgfn-incremental.log 2>&1
set -uo pipefail

APP_DIR="$HOME/apps/vigilo"
COMPOSE="docker compose -f $APP_DIR/docker-compose.yml"
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

cd "$APP_DIR" || exit 1

# Consulta os pendentes. Falha do exec (container/DB fora) NÃO pode ser confundida
# com "0 pendentes" — num produto de risco, não-cobertura silenciosa é perigosa.
if ! pend_out=$($COMPOSE exec -T app php artisan vigilo:carteira-cnpjs --pending </dev/null 2>/dev/null); then
    echo "!! [$(date -Is)] falha ao consultar pendentes (container/DB fora?) — abortando, não é '0 pendentes'"
    exit 1
fi

pendentes=$(printf '%s\n' "$pend_out" | grep -c . || true)
if [ "${pendentes:-0}" -eq 0 ]; then
    exit 0 # nada novo — no-op silencioso
fi

echo "==> [$(date -Is)] $pendentes CNPJ(s) novo(s) sem PGFN — refrescando do cache (incremental)"
# Incremental: só os pendentes (--pending) e com --merge (não recarrega o tipo
# inteiro). O baixar reusa o cache, serializa com flock e marca só esse snapshot.
PGFN_PENDING_ONLY=1 PGFN_MERGE=1 bash "$SCRIPT_DIR/baixar-importar-pgfn.sh"
