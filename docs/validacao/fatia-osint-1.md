# Fatia OSINT-1 — API OSINT de vigilância (backbone + consulta de CNPJ)

API de **leitura** sobre a base CNPJ própria (PostgreSQL local), para um workflow
externo (n8n). Primeiro dos três endpoints do contrato; os outros dois
(por-endereço, por-sócio) vêm nas fatias OSINT-2/3 e dependem de índices na base.

## O que mudou

- **Grupo `/api/v1/vigilancia`** isolado da `/api/cnpj` (esta segue travada à
  carteira; aquela consulta a base inteira, de propósito).
- **`GET /api/v1/vigilancia/cnpjs/{cnpj}`** → objeto `Empresa` do contrato
  (`razao_social`, `nome_fantasia`, `situacao` em caixa alta, `data_situacao`,
  `data_abertura`, `cnae_principal{codigo,descricao}`, `endereco{...}`,
  `socios[]{nome,qualificacao,data_entrada,documento_mascarado}`), **sem escopo
  de carteira**. `{ "data": {...}, "meta": { "base_referencia": "YYYY-MM" } }`.
- Semântica do contrato: 404 `nao_encontrado` (fora da base), 422 `cnpj_invalido`
  (tamanho), 503 `base_indisponivel` (base fora do ar). Erro sempre
  `{error:{code,message}}`.
- **Token dedicado** com ability literal `vigilancia:osint` (middleware
  `EnsureOsintAbility` — o coringa `*` NÃO entra); comando `vigilo:osint-token`.
- **Rate limit** próprio `vigilancia-api` (config `vigilancia.rate_per_minute`).
- **Auditoria LGPD**: tabela `api_access_logs` grava token/usuário/método/rota/
  params/status/ip de cada chamada (inclusive o 403 por falta de permissão).

Arquivos: `config/vigilancia.php`, `routes/api.php`, `app/Providers/AppServiceProvider.php`,
`app/Http/Controllers/Api/Vigilancia/CnpjController.php`,
`app/Services/Vigilancia/EmpresaLookup.php`, `app/DTO/Vigilancia/{Empresa,Endereco,Socio}.php`,
`app/Support/VigilanciaResponse.php`, `app/Http/Middleware/{EnsureOsintAbility,OsintAuditLog}.php`,
`app/Models/ApiAccessLog.php` (+ migration), `app/Console/Commands/IssueOsintToken.php`,
`tests/Feature/Api/VigilanciaCnpjTest.php`.

## Veredito do Juiz

**APROVADO — 94/100**, sem blockers. Portões: Pint ok · PHPStan nível 8 (0) ·
Pest 285 verdes.

- **A1** (resolvido) — faltava teste do caminho 503 → adicionado.
- **A4** (resolvido) — `socios[]` sem ordem determinística → `orderBy(nome_socio)`.
- **A3** (decisão de contrato, mantido) — `situacao` vazia para código não
  mapeado segue a convenção do projeto; o consumidor trata ausência.

## Go-live check (A2 — antes de `CNPJ_DRIVER=local` servir esta API)

O `EmpresaLookup` é o único acesso à base que o `LocalCnpjProvider` (já em
produção) não exercita. Confirmar na base PostgreSQL real, senão o endpoint
retorna 503:

- `\d cnaes` tem `codigo` + `descricao` (o leftJoin em tabela inexistente é
  fatal no Postgres);
- `\d estabelecimentos` tem `data_inicio_atividade` (se faltar, `data_abertura`
  degrada para null sem erro).

Setar `VIGILANCIA_BASE_REFERENCIA=YYYY-MM` no `.env` (o cron de reimport mensal
atualiza) e emitir o token: `php artisan vigilo:osint-token <email>`.

## Deploy

Sem assets novos. Precisa de **migration** (`api_access_logs`): o `deploy.sh`
roda `migrate` no release. Config nova (`config/vigilancia.php`) exige
`config:cache` — o deploy já refaz. Não precisa `--assets`.
