# Fatia — Webhook de saída assinado (HMAC)

Item 2 do roadmap. Entrega os alertas da carteira ao sistema do cliente em tempo
real, por webhook assinado — configurado numa tela nova de **Integrações**.

## O que mudou

- **`organizations`**: `webhook_url` + `webhook_secret` (cast `encrypted`, fora do
  `$fillable` — definidos só pela tela de Integrações). Helper `hasWebhook()`.
- **`webhook_deliveries`** (+ model): log append-only de cada entrega
  (organização, event_id, tipo, cnpj, nº de mudanças, status, HTTP, erro).
- **Disparo:** no `RefreshMonitoredCompanyJob`, quando há mudanças **e** a org
  tem webhook, enfileira `SendWebhookNotification` com o payload
  (`event_id`/`event`/`cnpj`/`razao_social`/`detectado_em`/`mudancas[]`).
  **Dispara em todos os alertas** (sem filtro de severidade).
- **`SendWebhookNotification`** (job): `POST` com header
  `X-Vigilo-Signature: sha256=<hmac do corpo>` (via `App\Support\WebhookSignature`)
  + `X-Vigilo-Event-Id` (estável entre retentativas → o consumidor deduplica).
  **Retry** com backoff (`tries=5`); grava a entrega no resultado final
  (sucesso no `handle`, falha no `failed()`).
- **Tela Integrações** (`/integracoes`, só admin do tenant, no menu): define
  URL + segredo (gera um forte; o segredo salvo **não** é pré-carregado por
  segurança), **envia teste** síncrono, e lista as últimas 10 entregas. Inclui
  um bloco "como validar a assinatura".

## Segurança

- Segredo **criptografado** em repouso e **nunca devolvido** ao formulário.
- URL validada por `App\Rules\PublicHttpsUrl`: exige **https** e bloqueia
  localhost / IP privado-reservado (anti-SSRF); o envio usa `withoutRedirecting()`.
  Resíduo (hostname que resolve p/ IP privado, IPv6) em DT-15.
- Envio assinado centralizado em `App\Services\Webhooks\WebhookSender` (job +
  teste usam o mesmo caminho). Tudo admin-only e escopado à organização.

## Contrato (resumo para o consumidor)

`POST` JSON com `X-Vigilo-Signature`. Validar: recomputar
`"sha256=" + hmac_sha256(corpo_cru, segredo)` e comparar com `hash_equals`.
Deduplicar por `X-Vigilo-Event-Id`.

## Testes

`SendWebhookNotificationTest` (assina+entrega+log, pula sem config, lança em
não-2xx, loga falha final), `IntegrationsTest` (admin salva/mantém segredo/
desativa/testa/gera; não-admin 403), `RefreshMonitoredCompanyJobTest`
(+dispara com webhook / não dispara sem).

## Portões

Pint ok · PHPStan nível 8 (0 erros) · Pest 335 verdes (13 novos).

## Deploy

Migration (`organizations` + `webhook_deliveries`) + Blade/menu novos →
`deploy.sh --assets`. Nada de config de ambiente (o segredo é por organização,
pela tela).
