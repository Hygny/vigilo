# Manual de deploy — Vigilo (VPS de produção)

Como subir uma nova versão do Vigilo em produção (`https://vigilosecure.com`), com os comandos e **quando usar cada um**. Para a **primeira instalação** do zero, veja `deploy/README.md`.

---

## TL;DR — qual comando usar

Do VPS, dentro de `~/apps/vigilo`:

```bash
./src/deploy/deploy.sh            # deploy rápido   (código PHP/Blade/rotas/migrations)
./src/deploy/deploy.sh --assets   # + rebuild do front (mudou CSS/JS ou classe Tailwind nova)
./src/deploy/deploy.sh --build    # + rebuild da imagem Docker (mudou Dockerfile/PHP/extensões)
```

> **A migration roda sempre** (`migrate --force`) nas três variantes. As flags controlam **só** o rebuild de assets e de imagem — nunca precise de uma flag "por causa da migration".

### Decisão rápida — o que mudou nesta versão?

| O que você tocou no código | Comando |
|---|---|
| Só PHP (`app/`, `routes/`, `config/`), Blade **sem classe Tailwind nova**, nova migration | `./src/deploy/deploy.sh` |
| `resources/css/**`, `resources/js/**`, `tailwind.config.js`, **ou** classe/utility Tailwind nova num Blade (ex.: `max-w-[860px]`, uma tela nova) | `./src/deploy/deploy.sh --assets` |
| `deploy/Dockerfile`, versão do PHP, extensão nova, `deploy/nginx.conf` | `./src/deploy/deploy.sh --build` |
| **Só o `.env`** (segredo novo, ex.: chave Asaas) — sem `git pull` | não é deploy → ver [Mudei só o `.env`](#mudei-só-o-env) |

**Regra de bolso do `--assets`:** o Tailwind só gera no CSS as classes que existiam nos arquivos **no momento do build**. Adicionou uma classe/tela nova? Sem `--assets`, ela sai **sem estilo** em produção. Na dúvida, use `--assets` (custa ~1 min a mais, nunca quebra nada).

---

## Antes de rodar

1. **Faça o push** da fatia aprovada para o `main` (isso é feito da sua máquina; o VPS só puxa).
2. **Acesse o VPS:**
   ```bash
   ssh lucas@148.230.77.119
   cd ~/apps/vigilo
   ```
3. **Confirme que está tudo de pé** (opcional, mas bom hábito):
   ```bash
   docker compose ps
   ```

---

## O deploy normal (o caso de 90% das fatias)

```bash
cd ~/apps/vigilo
./src/deploy/deploy.sh
```

O script faz, em ordem:

1. `git pull --ff-only` em `src/` — puxa o `main`.
2. `composer install --no-dev --optimize-autoloader` — dependências de produção.
3. `php artisan migrate --force` — aplica migrations pendentes.
4. `php artisan optimize` — cacheia config/rotas/views.
5. `docker compose restart app queue scheduler` — reinicia os serviços do app.

Termina com `==> deploy OK ✅`.

## Deploy com assets (`--assets`)

Quando mudou o front (CSS/JS) **ou** entrou classe Tailwind nova / tela nova:

```bash
cd ~/apps/vigilo
./src/deploy/deploy.sh --assets
```

Igual ao normal, mas entre o `composer install` e o `migrate` ele roda o build do Vite num container efêmero:

```
docker run --rm -v ~/apps/vigilo/src:/app -w /app node:20-alpine sh -c "npm ci && npm run build"
```

> Exemplos reais: **fatia 22 (tela /assinatura)**, fatia 20 (barra de quota), fatia 18 (painel /admin) → **`--assets`**. Fatia 21 (só lógica do `CompanyDiffer`) → deploy normal.

## Deploy com rebuild de imagem (`--build`)

Só quando muda a **imagem Docker** — `deploy/Dockerfile`, versão do PHP, uma extensão nova. Implica `--assets` e ainda reinicia o `nginx`:

```bash
cd ~/apps/vigilo
./src/deploy/deploy.sh --build
```

---

## Casos especiais

### Mudei só o `.env`

Trocar um segredo (ex.: `ASAAS_API_KEY`, `ASAAS_WEBHOOK_TOKEN`) **não passa por `git pull`** — o `.env` de produção vive no VPS em `~/apps/vigilo/src/.env` e nunca vai para o git. Depois de editar:

```bash
cd ~/apps/vigilo
nano src/.env                                   # edite os valores
docker compose exec app php artisan config:cache   # o optimize cacheia config: recarregue
docker compose restart app queue scheduler
```

> Sem o `config:cache`, o Laravel continua lendo o `.env` **antigo em cache** e a mudança não faz efeito.

### Rodar um comando artisan avulso

```bash
docker compose exec app php artisan <comando>
# exemplos:
docker compose exec app php artisan migrate:status
docker compose exec app php artisan tinker
docker compose exec app php artisan queue:work --once   # normalmente o serviço 'queue' já roda sozinho
```

### Criar/promover um super-admin em produção

```bash
# promover um usuário existente (por e-mail) a super-admin:
docker compose exec app php artisan tinker --execute="\App\Models\User::whereEmail('voce@dominio.com')->firstOrFail()->forceFill(['is_super_admin'=>true])->save();"
```

---

## Verificação pós-deploy

```bash
docker compose ps                          # todos os serviços 'Up'
docker compose logs -f --tail=50 app       # erros do app (Ctrl+C para sair)
docker compose logs -f --tail=50 queue     # fila processando jobs
curl -I https://vigilosecure.com/up     # healthcheck do Laravel → 200
```

Abra o site, faça login, confira a tela que mudou.

---

## Rollback (se algo quebrar)

O deploy é um `git pull`, então voltar é voltar o `src/` para o commit anterior e redeployar:

```bash
cd ~/apps/vigilo/src
git log --oneline -5                       # ache o commit bom anterior
git checkout <hash-do-commit-bom>          # ou: git reset --hard <hash>
cd ~/apps/vigilo
./src/deploy/deploy.sh --assets            # redeploy no estado anterior
```

> **Atenção com migrations:** se a versão ruim rodou uma migration destrutiva, voltar o código não desfaz o schema. Reverter migration é caso a caso (`php artisan migrate:rollback` desfaz o **último batch** — confira antes com `migrate:status`). Por isso migrations do projeto são aditivas e reversíveis.

---

## Backup do banco (MySQL) e restauração

**Rotina** — cron do VPS, todo dia 03:00: `deploy/backup-db.sh` (log em `~/apps/vigilo/backup-db.log`).

- `mysqldump --all-databases --single-transaction` do `data-mysql-1` (hoje só a base `vigilo`), validado (gzip íntegro + `-- Dump completed`) antes de virar backup.
- **Local:** `~/backups/db-AAAA-MM-DD.sql.gz` — 30 mais recentes + o 1º de cada um dos 12 últimos meses.
- **Fora do VPS:** cópia na VM da base CNPJ, `/data/backups/vigilo/` — 90 mais recentes + o 1º de cada um dos 24 últimos meses. Vai pelo verbo `guardar-backup` do `gateway.sh` (chave `~/.ssh/cnpj_db`), que só **escreve**: aceita nome com data real e não futura, valida o arquivo e **nunca sobrescreve** um backup guardado. O VPS confere o sha256.
- A rotação só toca `db-AAAA-MM-DD.sql.gz` no próprio diretório — `~/backups/solradar/` (outro app) nunca é tocado.

**Conferir:** `tail -5 ~/apps/vigilo/backup-db.log` → a rodada do dia termina em `cópia fora do VPS ok`. Na VM: `ls -lt /data/backups/vigilo | head`.

**Reenviar à VM** os backups locais (ex.: a VM ficou fora uns dias): `bash ~/apps/vigilo/src/deploy/backup-db.sh --enviar-existentes` (o que a VM já tem igual é ignorado).

**Restaurar** (no VPS — derruba as escritas antes):

```bash
cd ~/apps/vigilo
docker compose stop queue scheduler
zcat ~/backups/db-AAAA-MM-DD.sql.gz \
  | docker exec -i data-mysql-1 sh -c 'exec mysql -uroot -p"$MYSQL_ROOT_PASSWORD"'
docker compose start queue scheduler
```

O dump é `--all-databases`: restaura também o schema `mysql` (usuários/grants) do dia do backup.

**Trazer um backup da VM** (o verbo do gateway não lê): do PC do operador, com a chave pessoal da VM —
`scp ubuntu@159.112.186.240:/data/backups/vigilo/db-AAAA-MM-DD.sql.gz .` e depois
`scp db-AAAA-MM-DD.sql.gz lucas@148.230.77.119:~/backups/`.

---

## Mapa da instalação (para referência)

- **VPS:** Hostinger Ubuntu, `lucas@148.230.77.119`.
- **Layout:** `~/apps/vigilo/` contém `docker-compose.yml` (cópia de `src/deploy/`) e `src/` (o git clone de `Hygny/vigilo`).
- **Serviços (Docker Compose):** `app` (php-fpm), `nginx` (Traefik → `vigilosecure.com`), `queue` (`queue:work`), `scheduler` (`schedule:work`).
- **Infra compartilhada (externa):** Traefik (TLS `le`), `data-mysql-1`, `data-redis-1`, redes `web`/`data`.
- **Proxy:** `bootstrap/app.php` tem `trustProxies(at:'*')` — necessário atrás do Traefik.

## Troubleshooting rápido

| Sintoma | Causa provável | Ação |
|---|---|---|
| **500** logo após subir | permissão de `storage/` | o `entrypoint.sh` faz o chown; `docker compose restart app` e cheque `logs app` |
| **502** depois de `--build` | nginx com o IP antigo do container `app` | `docker compose restart nginx` (o `nginx.conf` já usa `resolver 127.0.0.11`) |
| Mudança de `.env` sem efeito | config em cache | `php artisan config:cache` + restart (ver acima) |
| Tela nova **sem estilo** | faltou `--assets` | rode `./src/deploy/deploy.sh --assets` |
| Alertas/refresh não rodam | serviço `queue`/`scheduler` caído | `docker compose ps` e `docker compose restart queue scheduler` |
