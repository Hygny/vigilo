# Fatia 4 — Jobs + scheduler + rate limit + notificações

- **Data:** 2026-08-14 (validação retroativa)
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 96/100** (`acao: seguir`)
- **Portões:** Pint ok · PHPStan nível 8 ok · Pest ok · Migrations reversíveis

## Resumo
`RefreshMonitoredCompanyJob` (fetch → SnapshotService → CompanyDiffer contra o último snapshot → persiste change_events → notifica → `last_refreshed_at`), com `WithoutOverlapping` + `RateLimited('cnpj-refresh')` e eager-load de `partners`. Notification `CompanyChangeDetected` (database+mail) aos usuários da org. Agendamento hoje é por portfólio (Fatia 10). Nota: `DispatchMonthlyRefreshJob` (blanket monthly) foi removido — superado pela Fatia 10.

## Achados (ambos resolvidos no mesmo ciclo)
- **A1** (baixa, corretude) — `Limit::perMinute(0)` bloqueava em vez de desabilitar (o config prometia "0 = desabilita"). **Resolvido**: `RateLimiter::for('cnpj-refresh')` agora usa `$n > 0 ? Limit::perMinute($n) : Limit::none()`.
- **A2** (baixa, corretude) — `change_events` criados fora de transação; falha no meio + retry podia duplicar snapshot e perder notificação. **Resolvido**: snapshot + eventos + `markRefreshed` agora num único `DB::transaction`; a notificação vai depois do commit (usando `$data->razaoSocial`).
