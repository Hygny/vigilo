# Fatia 8 — Estado "pendente"/progresso ao atualizar pela lista

- **Data:** 2026-08-14
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 98/100** (`acao: seguir`)
- **Portões:** Pint ok · PHPStan nível 8 ok · Pest ok (116 verdes) · Migrations n/a

## Resumo
Bug: `Portfolios\Index::queueRefresh` (atualizar pela lista) só despachava, sem zerar `last_refresh_status` — as empresas não apareciam "pendentes" ao abrir o portfólio. Corrigido extraindo a lógica em `App\Actions\QueuePortfolioRefresh` (zera status→pendente + enfileira em chunks), usado por `Index::queueRefresh`, `Show::queueRefreshAll` e `Show::reprocessPending` (injeção de dependência) — estado idêntico por construção. `dispatchRefreshFor` duplicado removido do `Show`.

## Achados
- **A1** (baixa, testes) — faltava teste anti-regressão de vazamento da constraint entre portfólios. **Resolvido no mesmo ciclo**: teste `never touches companies from another portfolio (constraint stays scoped)` cria 2 portfólios e prova que o B fica intocado.
- **A2** (baixa, legibilidade) — `Show::queueRefresh` (empresa única) reimplementa reset+dispatch inline em vez do Action; **sancionado pelo escopo** (o Action opera sobre portfólio, não empresa única). Sem ação.

## Arquivos
- `app/Actions/QueuePortfolioRefresh.php` (novo)
- `app/Livewire/Portfolios/Index.php`, `app/Livewire/Portfolios/Show.php`
- `tests/Feature/Livewire/PortfolioIndexTest.php`, `tests/Feature/Actions/QueuePortfolioRefreshTest.php` (novo)
