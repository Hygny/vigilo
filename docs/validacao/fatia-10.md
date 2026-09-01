# Fatia 10 — Agendamento mensal por portfólio (cron + histórico)

- **Data:** 2026-08-14
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 98/100** (`acao: seguir`)
- **Portões:** Pint ok · PHPStan nível 8 ok · Pest ok (129 verdes) · Migrations reversíveis (validadas por rollback pelo Juiz)

## Resumo
Cada portfólio agenda seus refreshes em **vários dias do mês** (`portfolios.schedule_days` JSON). Um comando `portfolios:run-scheduled`, agendado **diariamente às 03:00**, roda a Action `RunDuePortfolioSchedules`: acha os portfólios devidos hoje (clamp de fim de mês via `min(dia, diasNoMes)`), dispara via `QueuePortfolioRefresh` (jobs `ShouldBeUnique` → lote deduplicado) e grava um `portfolio_scheduled_runs`. Idempotente (guard `ran_on` + unique `(portfolio_id, ran_on)`). UI no portfólio: grade de dias 1–31 + histórico de execuções.

O antigo `DispatchMonthlyRefreshJob` (blanket monthly) foi **removido** — superado pelo agendamento por portfólio, o que também **resolve o DT-1** (era o `Bus::batch` dele sem dedup).

## Achados (ambos resolvidos no mesmo ciclo)
- **A1** (baixa, testes) — faltava provar o MESMO portfólio disparando em dois dias distintos do mês. **Resolvido**: teste `runs the same portfolio on each of its scheduled days across the month` (dias 5 e 20 → 2 runs; dia 12 → nenhum).
- **A2** (baixa, legibilidade) — `dispatched_count` sempre == `companies_count` no caminho do scheduler; o "X de Y" confundia. **Resolvido**: histórico passa a exibir "N empresa(s) enfileirada(s)".

## Arquivos
Novos: `RunDuePortfolioSchedules`, `RunScheduledPortfolioRefreshes` (command), `PortfolioScheduledRun` (+ factory), 2 migrations, 2 testes.
Alterados: `Portfolio`, `Portfolios\Show` (+ blade), `routes/console.php`, `ScheduleTest`, `PortfolioShowTest`.
Removidos: `DispatchMonthlyRefreshJob` (+ teste).
