# Fatia — Triagem de eventos (alertas)

Substitui o "reconhecer" binário dos alertas por um fluxo de triagem, para a
equipe tratar o volume de alertas sabendo **quem** fez **o quê** e **por quê**.

## Fluxo

`Novo → Em análise → (Descartado | Virou caso)` — e **Reabrir** volta ao início.
O descarte **exige motivo** (fica no histórico/export).

## O que mudou

- Enum `App\Enums\TriageStatus` (Novo/EmAnalise/Descartado/Caso) com
  label/tone/icon + `isOpen()` + `openValues()`.
- `change_events`: colunas `triage_status` (default `novo`, indexada),
  `triage_reason`, `triaged_at`, `triaged_by_id` (referência solta ao usuário,
  sem FK — padrão `impersonation_logs`). **Backfill:** o que já tinha
  `acknowledged_at` virou `em_analise` (não perde histórico).
- `ChangeEvent`: cast do enum, relação `triagedBy()`, scope **`open()`**
  (= `novo` + `em_analise`) como fonte única de "em aberto".
- `Alerts\Inbox`: filtro de triagem (Em aberto / Casos / Descartados / Todos) +
  severidade; ações **Iniciar análise**, **Virar caso**, **Descartar** (com
  campo de motivo obrigatório, validado) e **Reabrir**; export respeita os
  filtros. Removido o "marcar todos como vistos" (não mapeia pro fluxo).
- `Dashboard`: contador de "em aberto" passou a usar o scope `open()` (antes
  `whereNull(acknowledged_at)`) — consistente com a inbox.
- Export Excel: coluna "Reconhecido em" → **"Triagem"** + **"Motivo"**.

O `acknowledged_at` permanece na tabela como legado (não é mais a fonte de
verdade; `isAcknowledged()` mantido para compat).

## Testes

`AlertsInboxTest` reescrito (iniciar análise, virar caso, descartar com/sem
motivo, reabrir, filtro por triagem, isolamento cross-org). `DashboardTest`,
`AlertsExportTest` e `DomainHelpersTest` seguem verdes. Factory ganhou
`triage_status` default (model realista em memória).

## Portões

Pint ok · PHPStan nível 8 (0 erros) · Pest 322 verdes.

## Deploy

Migration (colunas novas em `change_events`) + Blade novo → `deploy.sh --assets`.
