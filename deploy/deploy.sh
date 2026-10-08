#!/usr/bin/env bash
#
# Deploy do Vigilo no VPS. Rode de dentro de ~/apps/vigilo:
#
#   ./deploy.sh            deploy rapido  (pull + composer + migrate + optimize + restart)
#   ./deploy.sh --assets   idem + rebuild do front (Vite/Tailwind) — quando muda js/css
#   ./deploy.sh --build    idem + rebuild da imagem (quando muda o Dockerfile) + restart nginx
#
set -euo pipefail

APP_DIR="$HOME/apps/vigilo"
SRC="$APP_DIR/src"
DO_BUILD=0
DO_ASSETS=0

for arg in "$@"; do
  case "$arg" in
    --build)  DO_BUILD=1; DO_ASSETS=1 ;;   # rebuild de imagem tambem refaz os assets
    --assets) DO_ASSETS=1 ;;
    *) echo "uso: ./deploy.sh [--assets] [--build]"; exit 1 ;;
  esac
done

echo "==> git pull"
git -C "$SRC" pull --ff-only

cd "$APP_DIR"

if [ "$DO_BUILD" = 1 ]; then
  echo "==> build da imagem"
  docker compose build
  docker compose up -d
fi

echo "==> composer install"
docker compose exec -T app composer install --no-dev --optimize-autoloader

if [ "$DO_ASSETS" = 1 ]; then
  echo "==> build dos assets (Vite)"
  docker run --rm -v "$SRC:/app" -w /app node:20-alpine sh -c "npm ci && npm run build"
fi

echo "==> migrate + optimize"
docker compose exec -T app php artisan migrate --force
docker compose exec -T app php artisan optimize

echo "==> restart dos servicos"
# O nginx entra SEMPRE no restart: quando o app reinicia e ganha um IP interno
# novo, o nginx preso ao IP antigo derruba o site com timeout (mesmo com o
# resolver). Reiniciar os dois juntos recarrega o opcache do FPM e re-resolve o
# upstream. Custa um blip de ~1s por deploy — barato perto de uma queda.
docker compose restart app queue scheduler nginx

echo "==> deploy OK ✅"
