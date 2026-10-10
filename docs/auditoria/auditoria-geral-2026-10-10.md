# Auditoria geral do projeto — 2026-10-10

Varredura por 6 auditores (lente do `validador-fatia`) + consolidação por um
revisor Tech Lead (validação lendo o código, corte de falso-positivo,
roteamento). Portões no momento: Pint ok · PHPStan nível 8 (0 erros) · Pest 359.
**Nenhum blocker. Núcleo (multi-tenancy, XSS, mass-assignment, HMAC, autorização
Livewire) passou.**

## 🔴 Corrigir já (confirmados, valor alto)

1. **Webhook do Asaas ignora estorno/chargeback** — `app/Services/Billing/SubscriptionService.php:97`
   O `match($event)` só trata `PAYMENT_CONFIRMED/RECEIVED/OVERDUE` e
   `SUBSCRIPTION_DELETED`. `PAYMENT_REFUNDED` / `PAYMENT_CHARGEBACK_*` /
   `PAYMENT_DELETED` não são tratados → a org segue `Active` (com acesso) após o
   cliente estornar ou dar chargeback. **Furo de monetização.** [alto · P]
   → tratar esses eventos re-suspendendo (`markOverdue`/estado próprio).

2. **SSRF por IP numérico no anti-SSRF** — `app/Rules/PublicHttpsUrl.php:48`
   Via NOVA (distinta do DT-15). Testado: a rule `url` do Laravel aceita
   `https://2130706433`, `https://127.1`, `https://0177.0.0.1`, `https://0x7f.0.0.1`
   e o `FILTER_VALIDATE_IP` os rejeita como IP → caem no ramo "hostname público"
   e passam; o cURL resolve todos para 127.0.0.1. [medio · P]
   → rejeitar host que não seja hostname DNS válido, ou normalizar/bloquear IPv4
   decimal/octal/hex antes do check.

3. **Auditoria LGPD pode ser pulada** — `app/Http/Middleware/OsintAuditLog.php:27`
   `$response = $next($request)` roda ANTES do `ApiAccessLog::create()`. Exceção
   fora de `QueryException` em qualquer controller pula o log de auditoria; e um
   `create()` que falhe transforma uma resposta pronta em 500. [medio · P]
   → `try { … } finally { grava o log }`, com o `create()` em try/catch próprio.

## 🟡 Precisa decisão do usuário (produto/escopo)

- **`focusOn`/`focusPerson` pivotam para QUALQUER CNPJ da base** —
  `app/Livewire/Companies/Graph.php:83,100`. Métodos públicos (endpoints)
  recentram o grafo em qualquer empresa/pessoa da Receita, sem reescopar à
  carteira — incoerente com a API `/api/cnpj` (escopada). O comentário diz "dado
  público da Receita" (intencional). **É feature OSINT aceita ou vetor de
  raspagem a fechar?** Se aceito, documentar; se não, reescopar/rate-limitar.
- **`ImportCompaniesCsvJob` é código morto** — `app/Jobs/ImportCompaniesCsvJob.php:19`.
  A UI importa síncrono; o job assíncrono (que o README promete) nunca é
  disparado. **Ligar o job (import grande fora do request) ou remover job+teste?**
- **Incompletude silenciosa em CEP denso** — `OwnershipGraphService.php:202`
  (`ADDRESS_SCAN=400`) e `EmpresaLookup.php` (`CANDIDATE_SCAN=2000`). Em CEP com
  muitos estabelecimentos, vizinhos reais além do scan somem de forma
  determinística. **Aceitar o tradeoff ou trocar por filtro no SQL/paginação?**

## 🔧 Deploy/infra (não é código de app)

- `idx_estabelecimentos_cep` — passo de deploy já documentado (fatia OSINT-2);
  confirmar aplicado no VPS. **Não é achado novo.**
- `deploy/reimport-mensal.sh:39` — `${CNPJ_DB_PASSWORD:?}` com `set -u` aborta
  ANTES de qualquer `record_run`, quebrando o contrato "grava `maintenance_run`
  sempre". → mover a checagem para depois, ou `record_run` num `trap EXIT`.
  Somar `flock` (evita 2 reimports concorrentes se a janela estourar).

## 📋 Débito técnico (registrar em docs/debito-tecnico.md)

- **N+1 no grafo** — `OwnershipGraphService.php:134` (1 query reversa por sócio)
  e `:392` (1 query por empresa no BFS, até 300). Bounded, mas anexar ao DT-13.
- **Chave de sócio frágil no diff** — `CompanyDiffer.php:135` (chave troca de
  `nome:` para `doc:` quando o documento aparece/some entre dumps → falso
  `PartnerRemoved`+`Added`, 2 alertas High falsos) e `:146` (dois sócios com o
  mesmo doc colapsam → mascara remoção real). Avaliar chave composta.
- **`history_limit` com env vazia → LIMIT 0** — `CnpjLookupController.php:65`.
  `max(1, (int) config(...))` defensivo.
- **`OrganizationScope` no-op sem usuário** — `app/Models/Scopes/OrganizationScope.php:30`.
  Por design (jobs/console veem todos os tenants); footgun latente — registrar.
- **Triagem (3 menores)** — métodos single não validam o estado de origem
  (`Inbox.php` `transition()`); lote não limita `$ids` a `selectableIds`; lote
  autoriza só por scope (single por Policy) — frágil se a Policy ganhar papel.
  Tudo dentro da própria org (sem vazamento). Baixo.
- Nice-to-have: `connectTimeout(5)` no `WebhookSender`; throttle/fila no
  `sendTest`; cap de linhas no export de alertas; `Str::ascii` no `nameParts`;
  contador do grafo refletir o filtro de negativas; FormRequest nos 3
  controllers OSINT; `compareScalar` case-insensitive.

## ⚪ Descartados (falso-positivo / aceito por design)

- Import não-atômico (`Portfolios/Show.php:271`) → **DT-11** (aceito).
- `NormalizeSocios` update por linha → **DT-14** (mitigado, incremental).
- Índice de CEP → passo de deploy documentado, não defeito.
- Máscara de CPF (`Socio.php`) → a Receita mascara na origem; defensivo opcional.

## Top 5 "faça agora" (impacto × esforço)

| # | Item | arquivo:linha | impacto | esf |
|---|---|---|---|---|
| 1 | Billing: estorno/chargeback | `SubscriptionService.php:97` | alto (dinheiro) | P |
| 2 | SSRF IP numérico | `PublicHttpsUrl.php:48` | medio | P |
| 3 | Auditoria LGPD try/finally | `OsintAuditLog.php:27` | medio | P |
| 4 | Contador×canvas do grafo | `Graph.php:230` | baixo (visível ao cliente) | P |
| 5 | connectTimeout + throttle webhook | `WebhookSender.php:29` + `Integrations/Index.php:87` | baixo | P |

> Itens **2 e 5** cabem numa única fatia "endurecimento de webhook". Item **1**
> sozinho (lógica de estado de billing). Item **3** sozinho. Os 🟡 precisam de
> decisão antes de virar fatia.
