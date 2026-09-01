# Fatia 9 — Filtro de validação por status (erro / não encontrado)

- **Data:** 2026-08-14
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 98/100** (`acao: seguir`)
- **Portões:** Pint ok · PHPStan nível 8 ok · Pest ok (120 verdes) · Migrations reversíveis

## Resumo
Filtro por status da coleta no detalhe do portfólio (`all|pending|ok|nao_encontrado|erro`) que filtra a lista **e** permite disparar a validação só do subconjunto (botão "Revalidar filtrados"). Chips com contagem; `#[Url]` persiste o filtro. Substituiu o antigo `reprocessPending`.

**Bug corrigido no `QueuePortfolioRefresh`**: a ordem era `update(status=null)` → `select` p/ despachar; com filtro por status isso zerava o alvo antes de selecioná-lo (0 jobs). Agora congela os ids (`pluck`) antes de mutar. Teste de regressão em `PortfolioShowTest`.

## Achados (ambos resolvidos no mesmo ciclo)
- **A1** (baixa, banco) — faltava índice p/ o filtro/groupBy. **Resolvido**: migration `2026_08_14_000001` cria índice composto `(portfolio_id, last_refresh_status)`.
- **A2** (baixa, legibilidade) — acoplamento implícito entre as chaves de `FILTERS` e os values do enum `RefreshStatus`. **Resolvido**: teste-guarda `tests/Unit/Enums/RefreshStatusTest.php` amarra os values.

## Arquivos
- `app/Livewire/Portfolios/Show.php`, `app/Actions/QueuePortfolioRefresh.php`
- `resources/views/livewire/portfolios/show.blade.php`
- `database/migrations/2026_08_14_000001_index_refresh_status_on_monitored_companies_table.php`
- `tests/Feature/Livewire/PortfolioShowTest.php`, `tests/Unit/Enums/RefreshStatusTest.php`
