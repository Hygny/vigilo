# V2-F1 — Base CNPJ consolidada (PostgreSQL)

> Início da V2 (grafo societário). Sobe a infra da base CNPJ própria (dump da Receita) em PostgreSQL no mesmo VPS. Fatia de **infra/ops**: o consumo da base (driver `LocalCnpjProvider`) é a V2-F2.

- **Data:** 2026-09-20
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 95/100** (`acao: seguir`, sem blockers)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **226/226** (661 asserts)

## O que entrou (código — [repo])

- **Serviço PostgreSQL no compose** (`deploy/docker-compose.yml`): `postgres:17-alpine` como container `data-postgres-1`, `POSTGRES_DB=cnpj`, volume persistente `cnpj-pgdata`, **só na rede interna `data`** (sem `ports:` público), healthcheck `pg_isready`. Usuário/senha via env (`${CNPJ_DB_USERNAME:-cnpj}` / `${CNPJ_DB_PASSWORD:?…}`) — **nunca hardcoded**.
- **Conexão `cnpj`** (pgsql) em `config/database.php`, lendo `CNPJ_DB_*` com defaults sensatos (host `data-postgres-1`, db `cnpj`). **Inerte** até ser usada — o default segue `mysql` (prod) / `sqlite` (teste), então o fluxo atual não muda. Vars documentadas em `.env.example` e `deploy/.env.production.example`.
- **Comando `cnpj:status`** (`app/Console/Commands/CnpjBaseStatus.php`): conta `empresas`/`estabelecimentos`/`socios` da conexão `cnpj` para validar a carga; degrada com erro claro e **exit de falha** se a base estiver indisponível (não estoura exceção crua).

## Operação (VPS — [você])
Passo a passo completo em **`docs/vigilo-v2-f1-runbook.md`**: senha nos dois `.env`, `docker compose up -d postgres`, carga do pipeline `caiopizzol/cnpj-data-pipeline` (horas, monitorar `df -h`), validação com `cnpj:status`, índices (incluindo o **reverso** em `socios` para o grafo, com a ressalva de confirmar os nomes das colunas pós-carga) e o reimport mensal via cron.

## Deploy
Como o compose ganhou um serviço novo, o deploy é **com rebuild** (uma vez): `./src/deploy/deploy.sh --build`, depois `docker compose up -d postgres`. Sem migration no fluxo do app (a base é externa, criada pelo pipeline).

## Testes
- `CnpjBaseStatusTest`: aponta a conexão `cnpj` para sqlite em memória → conta as três tabelas (caminho feliz) e falha com graça quando uma tabela não existe (caminho triste). Cobre o comando sem depender de um Postgres real.

## Achado
- **A1 (média):** `deploy/.env.production.example` trazia a senha do Postgres como placeholder em vez de vazia → **corrigido** (`CNPJ_DB_PASSWORD=` vazio, como no `.env.example`).

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 4→5 | 25 (A1 corrigido) |
| Corretude & regra de negócio | 20 | 5 | 20 |
| Segurança & autorização | 15 | 5 | 15 |
| Arquitetura & responsabilidades | 12 | 5 | 12 |
| Banco & performance | 12 | 5 | 12 |
| Testes | 10 | 5 | 10 |
| Legibilidade & manutenção | 6 | 5 | 6 |
| **Total** | **100** | | **95 → 100** (com A1 corrigido) |

## Próximo
**V2-F2** — driver `LocalCnpjProvider` lendo `estabelecimentos`+`empresas`+`socios` da base `cnpj` e montando o mesmo `CompanyData`; troca em `config/cnpj.php` (`local`), BrasilAPI como fallback. Habilita a re-coleta em lote (V2-F3) e o grafo (V2-F4+).
