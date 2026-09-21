# V2-F1 — Runbook da base CNPJ (PostgreSQL) no VPS

Passo a passo operacional para subir a base CNPJ própria e carregar o dump da Receita. A parte de **código** ([repo]) já está no projeto (serviço `postgres` no compose, conexão `cnpj` read-only, comando `cnpj:status`); aqui está a parte que **você** roda no VPS.

> ⚠️ A carga inicial leva **horas** e ocupa dezenas de GB. Rode fora de pico e **monitore o disco** (`df -h`). Você tem ~92 GB livres; a base normalizada com índices costuma ficar em ~40–80 GB.

---

## 0. Pré-requisitos (senha do Postgres)

O compose lê a senha de `~/apps/vigilo/.env` (arquivo do compose, ao lado de `docker-compose.yml`), e o app lê de `src/.env`. Use **a mesma senha nos dois**.

```bash
cd ~/apps/vigilo

# .env do compose (cria o container Postgres):
cat >> .env <<'EOF'
CNPJ_DB_USERNAME=cnpj
CNPJ_DB_PASSWORD=<uma-senha-forte-sem-simbolos>
EOF

# .env do app (mesma senha) — edite src/.env e confirme:
#   CNPJ_DB_HOST=data-postgres-1
#   CNPJ_DB_DATABASE=cnpj
#   CNPJ_DB_USERNAME=cnpj
#   CNPJ_DB_PASSWORD=<a-mesma-senha>
nano src/.env
```

Depois de mexer no `src/.env`, recacheie a config do app:
```bash
docker compose exec app php artisan config:cache
```

## 1. Deploy do código da V2-F1

Primeiro traga o código (conexão + compose + comando) e suba o Postgres:
```bash
cd ~/apps/vigilo
./src/deploy/deploy.sh --build   # --build: o compose ganhou um serviço novo (postgres)
docker compose up -d postgres
docker compose ps                # postgres deve ficar 'healthy'
```

## 2. Carga inicial (o dump da Receita)

Roda o pipeline num container efêmero na rede `data`, gravando direto no `data-postgres-1`. **Várias horas.** Rode dentro de `tmux`/`screen` para não cair com a sessão SSH:

```bash
tmux new -s cnpj
docker run --rm --network data \
  -e DATABASE_URL="postgres://cnpj:<a-mesma-senha>@data-postgres-1:5432/cnpj" \
  -e LOADING_STRATEGY=upsert \
  ghcr.io/caiopizzol/cnpj-data-pipeline
# acompanhe o df -h em outra aba: watch -n 60 df -h /
```

## 3. Validar a carga

```bash
docker compose exec app php artisan cnpj:status
```
Deve listar `empresas`, `estabelecimentos` e `socios` com contagens na casa de milhões. Se der erro de conexão, revise o serviço `postgres` e as `CNPJ_DB_*` do `src/.env`.

## 4. Índices (inclui o índice reverso do grafo)

O pipeline cria as tabelas; os índices que o **grafo societário** (V2-F4+) precisa você aplica depois da carga. **Confirme os nomes das colunas** antes (variam conforme a versão do pipeline):

```bash
docker compose exec postgres psql -U cnpj -d cnpj -c "\d socios"
docker compose exec postgres psql -U cnpj -d cnpj -c "\d estabelecimentos"
```

Com os nomes confirmados, crie os índices (ajuste a coluna do documento do sócio conforme o `\d socios` — costuma ser `cnpj_cpf_socio` ou `cpf_cnpj_socio`):

```sql
-- Índice REVERSO: "quais empresas este sócio participa?" (o coração do grafo)
CREATE INDEX IF NOT EXISTS idx_socios_documento ON socios (cnpj_cpf_socio);

-- Junções por CNPJ básico (empresa ↔ estabelecimentos ↔ sócios)
CREATE INDEX IF NOT EXISTS idx_socios_cnpj_basico       ON socios (cnpj_basico);
CREATE INDEX IF NOT EXISTS idx_estab_cnpj_basico        ON estabelecimentos (cnpj_basico);

-- Lookup direto por CNPJ completo (14 díg = básico+ordem+dv)
CREATE INDEX IF NOT EXISTS idx_estab_cnpj_completo
  ON estabelecimentos (cnpj_basico, cnpj_ordem, cnpj_dv);
```

Aplicar via psql:
```bash
docker compose exec postgres psql -U cnpj -d cnpj -f - <<'SQL'
-- cole os CREATE INDEX aqui (após confirmar as colunas)
SQL
```

Revalide um lookup e a consulta reversa:
```bash
# um CNPJ específico:
docker compose exec postgres psql -U cnpj -d cnpj \
  -c "SELECT cnpj_basico, cnpj_ordem, cnpj_dv FROM estabelecimentos WHERE cnpj_basico='11222333' LIMIT 5;"
# empresas de um sócio (aresta reversa):
docker compose exec postgres psql -U cnpj -d cnpj \
  -c "SELECT cnpj_basico, nome_socio FROM socios WHERE cnpj_cpf_socio='***123456**' LIMIT 20;"
```

## 5. Reimport mensal (upsert)

A Receita publica ~1x/mês. Agende o reimport no cron do host (dia 5, 03:00, fora de pico):

```cron
0 3 5 * * cd ~/apps/vigilo && docker run --rm --network data -e DATABASE_URL="postgres://cnpj:<senha>@data-postgres-1:5432/cnpj" -e LOADING_STRATEGY=upsert ghcr.io/caiopizzol/cnpj-data-pipeline >> ~/apps/vigilo/cnpj-reimport.log 2>&1
```

> A **re-coleta da carteira** após cada dump (gerar alertas do que mudou entre um mês e outro) é a **V2-F3** — entra depois do driver local (V2-F2).

## Critério de aceite (V2-F1)
- `cnpj:status` retorna contagens reais das três tabelas.
- Um lookup por CNPJ e a consulta reversa por documento de sócio retornam correto e rápido (com os índices do passo 4).

## Próximo
**V2-F2** — driver `LocalCnpjProvider` que lê dessa base (em vez da BrasilAPI), montando o mesmo `CompanyData`. Sem rate limit → habilita a re-coleta em lote (V2-F3) e o grafo (V2-F4+).
