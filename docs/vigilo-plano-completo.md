# Vigilo — Documentação Completa & Plano de Evolução

> Documento único: o projeto até hoje (2026-09-16), o que ainda falta (Asaas e cia.) e a V2 definitiva com passo a passo por fatia. Reúne e substitui `PROJETO.md` + `vigilo-v2-plano.md`.

---

# PARTE 1 — O PROJETO HOJE

## 1.1 O que é
**Vigilo** é um **SaaS B2B de KYB** (Know Your Business) — *due diligence contínua de CNPJ*. A organização sobe sua carteira de CNPJs (fornecedores, clientes, franqueados) e o Vigilo **detecta e alerta** mudanças relevantes no cadastro da Receita: situação cadastral, sócios/administradores, endereço, CNAE, porte, razão social.

- **Não é lead-gen** — disciplina LGPD (dado de sócio só para monitorar a carteira própria).
- **Estágio:** MVP **em produção** (`https://vigilosecure.com`). **21 fatias**, **204 testes verdes**, PHPStan nível 8, Pint limpo.

## 1.2 Stack & padrões
| Camada | Tecnologia |
|---|---|
| Backend | Laravel 13, PHP 8.4.24 |
| Front | TALL — Blade + Livewire 3 + Volt, Alpine, Tailwind v3 (Vite) |
| Banco | MySQL (produção: Redis p/ cache/fila/sessão) |
| Auth | Breeze (Livewire) + Sanctum (API) |
| Testes/estático | Pest (SQLite `:memory:`), Larastan nível 8, Pint |
| Design | Design System "âmbar & ink", dark mode, Material Symbols |

Camadas: Controller/Livewire magros → **Service/Action** → Model → **Job**. Regra de negócio nunca no Blade.

## 1.3 Modelo de dados
```
Organization ──< User            (role Admin/Usuário; is_super_admin; plan; suspended_at)
Organization ──< Portfolio ──< MonitoredCompany ──< CompanySnapshot ──< CompanyPartner (QSA)
                                        └──< ChangeEvent        (mudanças detectadas)
Portfolio ──< PortfolioScheduledRun     (histórico do agendamento)
ImpersonationLog                        (auditoria de "logar como")
```
Enums: `Role`, `Plan`, `Severity`, `RefreshStatus`, `ChangeType`.

## 1.4 O coração — fluxo de dados
```
[Provider CNPJ] → SnapshotService.store → CompanyDiffer.diff(anterior, atual)
                                                 │
                                     change_events + Notification (e-mail + inbox)
```
1. **Coleta** — contrato `CnpjDataProvider` (driver trocável em `config/cnpj.php`). Hoje: `BrasilApiProvider`. Traz cadastro + situação + CNAE + porte + endereço + **QSA (sócios)**.
2. **Snapshot** — `SnapshotService` grava `CompanySnapshot` + `CompanyPartner`.
3. **Diff** — `CompanyDiffer` (função pura) compara com o snapshot anterior.
4. **Alerta** — `RefreshMonitoredCompanyJob` persiste `change_events` (transação) e dispara `CompanyChangeDetected` (e-mail + database).

**Detecções:** mudança de situação (crítico se BAIXADA/INAPTA/SUSPENSA/NULA), **entrada já negativa** (fatia 21), sócio adicionado/removido, endereço, CNAE, porte, razão/fantasia.
**Confiabilidade:** jobs `ShouldBeUnique`, `WithoutOverlapping`, `RateLimited`, `retryUntil`; status por empresa (ok/não encontrado/erro).

## 1.5 Funcionalidades
| Área | O que faz |
|---|---|
| Multi-tenant | Tudo por `organization_id` (scope global no `Portfolio`; filtro explícito em `User`/`MonitoredCompany`) |
| Carteiras | Portfólios em cards; CRUD; import CSV com `label` |
| Coleta | Refresh por empresa/portfólio/lote; estado "pendente" |
| Diff/alertas | Detecção + inbox + e-mail |
| Agendamento | Coleta mensal por portfólio (calendário, próxima execução); cmd `portfolios:run-scheduled` |
| API | `GET /api/cnpj/{cnpj}` (Sanctum, escopo por org, histórico); cmd `vigilo:api-token` |
| Papéis | Admin/Usuário; Usuário opera mas não exclui carteira nem gerencia usuários |
| Super-admin | Painel `/admin`: lista orgs c/ uso, onboarda org+admin, suspende/reativa, promove/rebaixa, **impersona** (auditoria), define **plano** |
| Planos+quota | Free/Pro/Business; trava por nº de CNPJs; `config/plans.php` |

## 1.6 Segurança & multi-tenancy
- Rotas atrás de `auth`(+`verified`); admin `admin`; plataforma `super-admin` — **e cada método público de Livewire re-checa a permissão** (o `/livewire/update` não reaplica o middleware da rota).
- **Sem autocadastro** (`/register` removido) — onboarding manual.
- `is_super_admin`, `suspended_at`, `plan`, `email_verified_at` ficam **fora do `$fillable`** (só via `forceFill`).
- Org suspensa bloqueia login e desloga mid-sessão; impersonação é isenta.
- **Débito:** `organization_id`/`role` ainda mass-assignable (DT-2).

## 1.7 Deploy & qualidade
- VPS Hostinger, Docker Compose (Traefik + MySQL `data-mysql-1` + Redis `data-redis-1`, redes `web`/`data`). `deploy.sh` (`--assets`/`--build`).
- Portões: Pint, PHPStan nível 8, `php artisan test` (204 verdes). Cada incremento = "Fatia" validada pelo **Juiz** (`.claude/agents/validador-fatia.md`); docs em `docs/validacao/`.

## 1.8 Fatias entregues (resumo)
- **1–6** MVP · **7–10** dedup de fila, estado pendente, filtro por status, agendamento mensal · **11–12** scheduler + API pública · **13–16** Design System + pt-BR + gestão de usuários + remoção do `/register` · **17–21 (Prioridade 1 + fix):** 17 restringir Usuário · 18 super-admin/back-office · 19 impersonação · 20 planos+quota · 21 alerta de entrada já negativa.

---

# PARTE 2 — O QUE AINDA FALTA (curto prazo)

## 2.1 Fatia 22 — Integração Asaas (fechar a cobrança)
Única parte da monetização que falta. A camada de planos+quota (fatia 20) e o mecanismo de suspensão (fatia 18) **já existem** — falta plugar o pagamento recorrente.

**Decisões já tomadas:** gateway **Asaas** (PIX/boleto/cartão); planos Free/Pro/Business.
**Depende de você:** criar conta Asaas e gerar a **API key de sandbox**; decidir **ciclo** (mensal) e se há **trial**.

**Passo a passo (V2 da cobrança):**
1. **[você]** Criar conta Asaas → Configurações → Integrações → **API key (sandbox)** e o **webhook token**.
2. **[repo]** `config/asaas.php` + vars no `.env` (`ASAAS_BASE_URL` sandbox/prod, `ASAAS_API_KEY`, `ASAAS_WEBHOOK_TOKEN`) — segredo nunca no código.
3. **[repo]** Persistir os vínculos na `organizations`: `asaas_customer_id`, `asaas_subscription_id`, `billing_status` (trialing/active/past_due/canceled) — fora do `$fillable`.
4. **[repo]** `AsaasClient` (Service) — criar/atualizar **customer** e **subscription** (valor = preço do plano, ciclo mensal) ao ativar um plano pago; retornar o link de checkout (PIX/boleto/cartão).
5. **[repo]** **Webhook** `POST /webhooks/asaas` (sem `auth`, valida o token do header): `PAYMENT_CONFIRMED/RECEIVED` → marca `active` e **reativa** a org; `PAYMENT_OVERDUE` → `past_due` e **suspende** a org (reusa `suspended_at`); `SUBSCRIPTION_DELETED`/cancelamento → downgrade p/ Free ou suspende.
6. **[repo]** **Tela de billing** do admin da org: plano atual, status, próxima fatura, link de pagamento, histórico.
7. **[repo]** Resolver **DT-11** (atomicidade da quota) nesse passe — contagem+criação em `DB::transaction` com lock.
8. **[repo]** Testes: criação de subscription (HTTP fake do Asaas), e cada evento de webhook → suspende/reativa/atualiza status.
9. Juiz → push → **[você]** configurar a URL do webhook no painel Asaas + a API key no `.env` do VPS.

## 2.2 Reprocessamento da carteira (alerta retroativo)
A fatia 21 alerta CNPJ que **entra** já negativo, mas não os que **já estão** na carteira baixados de antes. Comando opt-in `php artisan vigilo:reprocessar-negativos` que varre a carteira e gera alerta crítico para os já-negativos (uma leva só).

## 2.3 Débito técnico aberto
- **DT-2** — `organization_id`/`role` mass-assignable (tratar no fluxo de convite).
- **DT-3/4/5** — regra de agendamento duplicada; teste do `nextScheduledRun`; presets em 2 lugares.
- **DT-9** — sem camada de teste de UI (JS/Dusk).
- **DT-10/11** — dois TOCTOU baixos (guarda "nunca sem admin"; quota check-then-create — DT-11 resolve na fatia 22).

---

# PARTE 3 — VIGILO V2 (Rastreamento Societário Profundo)

## 3.1 Visão & diferencial
Módulo de **grafo societário profundo** (estilo Tianyancha/Qichacha): grafo bidirecional de participações, expansível por nível, selo de status por nó, empresa consultada no centro, até **beneficiário final**. **Nenhum concorrente nacional** (Tratum, Idwall, Neoway, Serasa, Econodata, Speedio) combina **monitoramento contínuo + grafo societário profundo** — esse é o eixo do go-to-market.

## 3.2 Mudança de arquitetura central: base CNPJ própria
Hoje a coleta é via **BrasilAPI** (rate limit) — inviável para grafo recursivo e carteira grande. A V2 troca a fonte por uma **base CNPJ consolidada própria**, alimentada por dump da Receita, **sem API de terceiros**.

**Repo base:** **`caiopizzol/cnpj-data-pipeline`** (mantido; suporta o novo repositório **Nextcloud** da Receita; PostgreSQL normalizado; carga incremental `upsert`; Docker). Descartados: `aphonsoar/...` (desatualizado, pré-Nextcloud) e `cuducos/minha-receita` (guarda 1 JSON por CNPJ — ruim para a aresta reversa do grafo).

**Cadência:** **mensal** (a Receita publica ~1x/mês; quinzenal reprocessaria o mesmo dump à toa).

**Dois bancos, separados:**
- **App (MySQL, atual)** — intacto.
- **Base CNPJ (PostgreSQL novo, db `cnpj`)** — dump completo em tabelas normalizadas (`empresas`, `estabelecimentos`, `socios`, `dados_simples` + referência), **read-only** para o app, atualizada por upsert mensal. Postgres pelas **CTEs recursivas** do grafo.

**Encaixe elegante:** o Vigilo já tem a abstração `CnpjDataProvider`. Então a base própria entra como **novo driver `LocalCnpjProvider`** (troca em `config/cnpj.php`) — o pipeline snapshot/diff/alerta **não muda**, só a origem do dado. O CNPJ 14 díg. = `cnpj_basico`(8)+`cnpj_ordem`(4)+`cnpj_dv`(2); o provider junta `estabelecimentos` + `empresas` + `socios`.

**Melhoria essencial sobre o pipeline:** **índice reverso em `socios`** (pelo documento do sócio) — os ETLs indexam pela empresa (sentido direto); o índice reverso é o que torna rápida a pergunta "em quais empresas a pessoa X é sócia", o coração do grafo.

**Infra/disco:** CSVs da Receita ~85 GB (processados por arquivo, com limpeza); Postgres final na casa de dezenas de GB. VPS com ~92 GB livres — **suficiente, porém apertado**: monitorar a 1ª carga; se apertar, carregar só `empresas`/`estabelecimentos`/`socios`.

## 3.3 Roadmap V2 — passo a passo por fatia
Mesma disciplina das fatias atuais (build → Juiz → push). **[repo]** = eu faço no código; **[você]** = você roda no VPS.

### V2-F1 · Base CNPJ consolidada (PostgreSQL) — *infra, destrava tudo*
1. **[repo]** Serviço **PostgreSQL** na stack Docker (`data-postgres-1`, db `cnpj`, volume persistente, só na rede interna).
2. **[você]** `docker compose up -d data-postgres-1`.
3. **[você]** **Carga inicial** (várias horas, fora de pico), monitorando `df -h`:
   ```bash
   docker run --rm --network data \
     -e DATABASE_URL=postgres://USER:SENHA@data-postgres-1:5432/cnpj \
     -e LOADING_STRATEGY=upsert \
     ghcr.io/caiopizzol/cnpj-data-pipeline
   ```
4. **[você]** Validar: contagem de linhas em `empresas`, `estabelecimentos`, `socios`.
5. **[repo]** Migration do **índice reverso** em `socios` (documento do sócio) + índices auxiliares.
6. **[repo]** Connection **`cnpj`** (pgsql, read-only) no `config/database.php` + `.env`.
7. **[repo]** Agendamento **mensal** do reimport (`upsert`).
- **Entrega:** base própria consultável pelo Laravel. **Aceite:** consulta de um CNPJ e da lista de empresas de um sócio retorna correto e rápido.

### V2-F2 · Driver `LocalCnpjProvider`
1. **[repo]** Implementar `CnpjDataProvider::fetch` lendo `estabelecimentos`+`empresas`+`socios` da base `cnpj` e montando o `CompanyData` (mesmo DTO de hoje).
2. **[repo]** Trocar o driver em `config/cnpj.php` (`local`); manter BrasilAPI como fallback opcional.
3. **[repo]** Testes com a base local.
- **Entrega:** coleta 100% da base própria, sem BrasilAPI. **Aceite:** snapshot/diff idênticos ao fluxo atual, sem rate limit.

### V2-F3 · Re-coleta pós-dump (monitoramento contínuo real)
1. **[repo]** Após cada ingestão mensal, re-snapshot + diff de **toda a carteira** → alertas.
2. **[repo]** Como não há rate limit, processar a carteira inteira em lote.
- **Entrega:** monitoramento contínuo de verdade. **Aceite:** um CNPJ que mudou entre dois dumps gera alerta automaticamente.

### V2-F4 · Grafo societário — Camada 1 (grátis, sem %)
1. **[repo]** Consulta bidirecional em `socios` (direto + **reverso**); montar nós/arestas.
2. **[repo]** **Grupo econômico** = empresas com ≥1 sócio em comum (self-join). **Dedup de PF por CPF** (reusa `keyPartners`).
3. **[repo]** UI **expansível por nível** ("+"), profundidade padrão (ex.: 3 níveis), selo de status por nó, empresa central destacada.
- **Entrega:** grafo navegável. **Aceite:** expandir níveis mostra sócios/investidas com status; grupo econômico correto.

### V2-F5 · Recursão + beneficiário final + alertas por nó
1. **[repo]** Recursão (CTE) no QSA até sobrar só PF = **beneficiário final calculado**; sinalizar "cadeia incompleta" (sócio estrangeiro / sem QSA).
2. **[repo]** Estender alertas para **qualquer nó da árvore**: troca de sócio, nova/removida empresa do grupo, mudança de status em qualquer nó.
- **Entrega:** due diligence profunda. **Aceite:** beneficiário final calculado e alerta disparado por mudança em nó não-diretamente-monitorado.

### V2-F6 · Camada 2 — % de participação (opcional, sob demanda)
1. **[repo]** Fonte de % (Junta Comercial via RPA — grátis em SP; ou base B2B paga) **ativada por CNPJ**, com **cache** (ex.: 6 meses).
2. **[repo]** Fallback: se falhar, grafo cai na Camada 1 (aresta sem peso).
3. **[repo]** **Cobrança por quota de plano** (reusa fatia 20 + Asaas): N consultas de %/mês por plano.
4. **[repo]** Alertas extras: mudança relevante de % (deixou de ser majoritário), concentração de participação.
- **Entrega:** % nas arestas sob demanda. **Aceite:** consulta de % por CNPJ respeita quota e cacheia.

### (paralelo) Outras validações da V2 — *a fechar*
Ex.: processos judiciais (CNJ DataJud, API pública), sanções/PEP, CVM (capital aberto). Definir a lista.

## 3.4 Fontes de dado (V2)
| Dado | Fonte | Custo |
|---|---|---|
| Cadastro, situação, CNAE, endereço, QSA | **Base própria** (dump Receita via caiopizzol) | Grátis (infra própria) |
| % de participação | Camada 2: Junta Comercial (RPA; grátis SP) ou B2B pago (~R$280–890/mês) | Grátis (SP) ou pago |
| Beneficiário final calculado | Recursão no QSA da base própria | Grátis (engenharia) |
| Beneficiário final oficial (e-BEF) | Receita — sem API pública | Indisponível |
| Processos judiciais | CNJ DataJud (API pública) | Grátis |
| Capital aberto | CVM Dados Abertos (~400 empresas) | Grátis |

## 3.5 Decisões
- [x] Cobrança: **Asaas** (fatia 22). Planos Free/Pro/Business; quota por nº de CNPJs.
- [x] V2 base: **`caiopizzol/cnpj-data-pipeline`** → PostgreSQL (db `cnpj`), cadência **mensal**.
- [x] Disco VPS: ~92 GB livres (suficiente, monitorar).
- [ ] Postgres roda no **mesmo VPS** (stack `data`) — confirmar.
- [ ] Trial na cobrança? Ciclo confirmado mensal?
- [ ] Profundidade padrão do grafo (3 níveis?).
- [ ] Lista final das outras validações da V2.

## 3.6 Trilhas de apoio
- **Design:** validar o Design System com a Gabrielle Sanches (levar telas do grafo).
- **Posicionamento/GTM:** validar diferencial vs concorrentes → apresentação → para quem ofertar (compliance, jurídico, financeiro, franqueadoras).
- **Documentação:** API (endpoint já existe) + app para usuário final.
- **Distribuição interna:** avaliar o Vigilo na MoveEdu (due diligence de franqueados/fornecedores).

## 3.7 Ordem de execução sugerida
Fatia 22 (Asaas) → V2-F1 → V2-F2 → V2-F3 → V2-F4 → V2-F5 → V2-F6, com Design/GTM em paralelo.
