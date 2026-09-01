# Fatia 11 — Redesign UX do Agendamento automático

> Refino de UI sobre a Fatia 10 (agendamento mensal por portfólio).

- **Data:** 2026-08-16
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 94/100** (`acao: seguir`)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` ok (PortfolioShowTest 13/37 verdes; suíte total 146)

## Resumo

A grade crua de 31 botões + lista simples virou um scheduler: calendário de dias-do-mês em 2 colunas com estados (selecionado/hoje/hover/foco visível), presets (Todo dia 1, Todo dia 15, Dias 1 e 15, Último dia), resumo em linguagem natural, caixa **Próxima execução** (calculada espelhando a regra do cron, com "escorregar pro último dia" e skip de "já rodou hoje"), e barra de estado **salvo / alterações não salvas** com Salvar + Descartar. Ícones SVG (não emoji), `aria-pressed`/`aria-label`/`role="group"`, textos pt-BR. Backend: `Show.php` ganhou `applyPreset`, `clearScheduleDays`, `discardScheduleChanges`, `normalizedScheduleDays`, `nextScheduledRun`, `scheduleFiresOn`; `RunDuePortfolioSchedules` e o schema ficaram intocados. Verificação adversarial confirmou: authz no caminho de escrita (`saveSchedule` → `authorize('update')`), sem N+1, `schedule_days` segue `list<int>|null`.

## Critério de aceite

Todos os 7 itens **atendidos** (componente moderno; preserva toggle/salvar/null/último-dia; resumo+presets+próxima-execução coerente com o cron; estado salvo vs não-salvo + descartar; a11y; gates verdes com testes de preset e descartar; sem regressão de dados/cron).

## Achados (ressalvas → débito técnico DT-3, DT-4, DT-5)

- **A1** (média, arquitetura, confirmado) — regra "dispara nesta data / último-dia escorrega" duplicada entre `Show::scheduleFiresOn` e `RunDuePortfolioSchedules::matchedDay`, sem fonte única. → **DT-3**.
- **A2** (baixa, testes, confirmado) — `nextScheduledRun` (lógica mais densa da adição) sem teste próprio. → **DT-4**.
- **A3** (baixa, legibilidade, confirmado) — conjuntos de dias dos presets definidos duas vezes (`applyPreset` e array do Blade). → **DT-5**.

*Observação neutra (sem desconto):* a caixa "Próxima execução" usa os dias **salvos** enquanto o calendário reflete a seleção **pendente** — correto por design (mostra o que o cron fará de fato); o banner "Alterações não salvas" mitiga.

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25,0 |
| Corretude & regra de negócio | 20 | 5 | 20,0 |
| Segurança & autorização | 15 | 5 | 15,0 |
| Arquitetura & responsabilidades | 12 | 4 | 9,6 |
| Banco & performance | 12 | 5 | 12,0 |
| Testes | 10 | 4 | 8,0 |
| Legibilidade & manutenção | 6 | 4 | 4,8 |
| **Total** | **100** | | **94** |
