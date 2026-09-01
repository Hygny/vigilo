# Deploy do Vigilo (VPS)

Arquivos de infraestrutura para subir o Vigilo sobre a stack existente
(Hostinger + Traefik + MySQL 8.4 + Redis, redes Docker `web`/`data`).

| Arquivo | Para quê |
|---|---|
| `Dockerfile` | Imagem PHP 8.4-fpm-alpine + extensões (inclui `redis`) + Composer. |
| `nginx.conf` | Servidor web que serve o Laravel e repassa PHP ao php-fpm. |
| `docker-compose.yml` | Serviços do projeto: `app`, `nginx` (labels do Traefik), `queue`, `scheduler`. |
| `.env.production.example` | Modelo do `.env` de produção (MySQL + Redis + variáveis do CNPJ/API). |

## Resumo (o passo a passo completo está no PDF do manual)

```bash
# no VPS
mkdir -p ~/apps/vigilo && cd ~/apps/vigilo
git clone <repo> src
sudo chown -R $USER:$USER src
cp src/deploy/docker-compose.yml .            # unico arquivo de ops fora do repo

# banco: criar DB+user 'vigilo' no data-mysql-1 (ver manual)
cp src/deploy/.env.production.example src/.env  # e preencha as senhas

docker compose build
docker compose run --rm app composer install --no-dev --optimize-autoloader
docker run --rm -v ~/apps/vigilo/src:/app -w /app node:20-alpine sh -c "npm ci && npm run build"

docker compose up -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
docker compose exec app php artisan storage:link
docker compose exec app php artisan optimize
```

O middleware `trustProxies` já vem configurado em `bootstrap/app.php` (necessário atrás do Traefik).
