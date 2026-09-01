# Fatia 7 — Deduplicação na fila

- **Data:** 2026-08-14
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 98/100** (`acao: seguir`)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · Pest ok (113 verdes) · Migrations N/A (`cache_locks` já existe p/ o lock em produção)

## Resumo
`RefreshMonitoredCompanyJob` passou a `implements ShouldBeUnique` com `uniqueId() = id da empresa` e `uniqueFor = 3600`. Cliques repetidos em "Atualizar todas"/"Reprocessar" não empilham mais jobs iguais (bug confirmado: 2121 jobs p/ 717 empresas ≈ 3×). O lock é liberado ao concluir o job (não é `...UntilProcessing`).

## Achados
- **A1** (baixa, testes) — a liberação do lock pós-conclusão não era coberta por teste (`Queue::fake` não processa o job). **Resolvido no mesmo ciclo**: teste `accepts a fresh refresh once the previous one has finished (lock released)` libera o `UniqueLock` e re-despacha, esperando 1 novo job.
- **A2** (baixa, suspeita, fora de escopo) — `DispatchMonthlyRefreshJob` usa `Bus::batch(...)->dispatch()`, que empurra via `bulk()` e **não** passa pelo `PendingDispatch::shouldDispatch()` (quem adquire o `UniqueLock`). O lote mensal pode enfileirar refresh para empresa que já tem job manual pendente. Baixo impacto (1×/mês; `WithoutOverlapping` protege a execução). **Adiado para a Fatia 10** — ver `docs/debito-tecnico.md`.

## Arquivos
- `app/Jobs/RefreshMonitoredCompanyJob.php`
- `tests/Feature/Jobs/RefreshMonitoredCompanyJobTest.php`
