# Fatia 22 — Integração de cobrança Asaas

> Plugar o pagamento recorrente. A camada de planos+quota (fatia 20) e a suspensão de organização (fatia 18) já existiam; faltava a cobrança de fato.

- **Data:** 2026-09-16
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 98/100** (`acao: seguir`, sem blockers)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **217/217** (632 asserts) · migration reversível

## O que entrou

- **`config/asaas.php` + `.env.example`** — `ASAAS_BASE_URL` (sandbox/prod), `ASAAS_API_KEY`, `ASAAS_WEBHOOK_TOKEN`, `ASAAS_TIMEOUT`, `ASAAS_TRIAL_DAYS` (0 = sem trial), `ASAAS_BILLING_TYPE` (UNDEFINED = cliente escolhe PIX/boleto/cartão). Segredos só no `.env`, nunca no código.
- **Migration** `add_asaas_billing_to_organizations_table` — `asaas_customer_id`, `asaas_subscription_id`, `billing_status` (default `none`) + índice em `asaas_subscription_id` (lookup do webhook). Colunas sensíveis **fora do `$fillable`** — escritas só via `forceFill`.
- **`App\Enums\BillingStatus`** — `none/trialing/active/past_due/canceled` com `label()`. Separado de `suspended_at`: o status descreve a cobrança, a suspensão corta o acesso.
- **`App\Services\Asaas\AsaasClient`** — client HTTP fino (autentica com `access_token`): `createCustomer`, `createSubscription`, `getSubscription`, `listSubscriptionPayments`, `cancelSubscription` (idempotente no 404). Exceção própria `AsaasException` (rede/HTTP/não configurado); `isConfigured()` para degradar sem credencial.
- **`App\Services\Billing\SubscriptionService`** — regra de negócio: `syncForPlan(org, plan)` (Free cancela; plano pago cria customer+subscription mensal), `handleWebhook(event, payload)` e `pendingInvoiceUrl(payments)`. Toda escrita sensível via `forceFill`.
- **Webhook** `POST /webhooks/asaas` (`AsaasWebhookController`) — rota pública, isenta de CSRF, autenticada pelo token do header (`asaas-access-token`) em tempo constante (`hash_equals`, *fail-closed*). `PAYMENT_CONFIRMED/RECEIVED` → `active` + reativa; `PAYMENT_OVERDUE` → `past_due` + suspende; `SUBSCRIPTION_DELETED` → rebaixa para Free **sem** chamar o Asaas de volta. Idempotente.
- **Tela `/assinatura`** (`App\Livewire\Billing\Index`, middleware `admin`) — plano atual, status, próxima fatura, link para pagar a fatura em aberto e histórico; buscados ao vivo e degradando com elegância se o Asaas estiver fora/não configurado.
- **DT-11 resolvido** — `Portfolios\Show::addCompany` passou a fazer a checagem de vaga + criação em `DB::transaction` com `lockForUpdate` na org (quota atômica).
- **`setPlan` do super-admin** agora reconcilia a assinatura via `syncForPlan`, com `try/catch` de `AsaasException` (um gap de credencial não desfaz a troca de plano — só avisa).

## Testes

- `SubscriptionServiceTest` — cria customer+subscription mensal (HTTP fake, valida `value`/`cycle`/`customer`); trial → `trialing`; Free cancela no Asaas (DELETE) e rebaixa; sem credencial → `AsaasException`.
- `AsaasWebhookTest` — 403 sem token e com token errado; `PAYMENT_CONFIRMED` reativa+`active`; `PAYMENT_OVERDUE` suspende+`past_due`; `SUBSCRIPTION_DELETED` rebaixa p/ Free sem call-back; evento de assinatura desconhecida = no-op.
- `BillingScreenTest` — membro comum 403; admin vê o plano; com cobrança pendente vê o link "Pagar fatura em aberto".

## Achados (ambos de baixa severidade)

- **A1 — chamada duplicada ao endpoint de payments no render** → **corrigido na hora**: a tela busca as cobranças **uma vez** e deriva dela o link da fatura em aberto (`pendingInvoiceUrl`) e o histórico.
- **A2 — webhook público sem throttle** → registrado como **DT-12** (baixa; *fail-closed* por token já protege contra efeito).

## Deploy

Precisa de assets (novas classes Tailwind) e roda migration: **`./deploy.sh --assets`**. Depois, no painel Asaas, configurar a URL do webhook (`https://vigilosecure.com/webhooks/asaas`) + o token, e preencher `ASAAS_API_KEY`/`ASAAS_WEBHOOK_TOKEN` no `.env` do VPS.

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25,0 |
| Corretude & regra de negócio | 20 | 5 | 20,0 |
| Segurança & autorização | 15 | 5 | 15,0 |
| Arquitetura & responsabilidades | 12 | 5 | 12,0 |
| Banco & performance | 12 | 4 | 9,6 |
| Testes | 10 | 5 | 10,0 |
| Legibilidade & manutenção | 6 | 5 | 6,0 |
| **Total** | **100** | | **97,6 → 98** |
