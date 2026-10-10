# Fatia — Triagem em lote (seleção múltipla)

Melhoria da tela de Alertas (`App\Livewire\Alerts\Inbox`): além da triagem
linha a linha, agora dá para **selecionar vários alertas** e aplicar uma ação em
massa — iniciar análise, virar caso ou descartar (com motivo único).

## O que mudou

- **`Inbox`**: `public array $selected`, `toggleSelectAll()`, `clearSelection()`,
  `bulkStartAnalysis()`, `bulkPromoteToCase()` e o par
  `beginBulkDismiss()`/`confirmBulkDismiss()` (motivo único, validado 3–500).
  As ações rodam um **único UPDATE escopado à organização**
  (`transitionSelected()`: `scopedEvents()->whereIn('id', …)->whereIn('triage_status', $from)`),
  então só transicionam alertas **elegíveis** (ex.: "iniciar análise" só afeta
  `novo`) e nunca tocam alertas de outra org. Gravam `triaged_at`/`triaged_by_id`.
- **Fonte única de "selecionável"**: `visibleEvents()` monta a página (teto 100) e
  `openIds()` extrai os IDs em aberto; tanto o `render()` quanto o
  `toggleSelectAll()` derivam daí — o "selecionar todos" nunca marca alertas fora
  da página visível (correção do A1).
- **Limpeza de seleção**: trocar o filtro de triagem **ou** de severidade zera a
  seleção (`setTriage`/`setSeverity` → `clearSelection()`).
- **View**: checkbox por linha só nos alertas em aberto (`$st->isOpen()`), barra de
  ações em lote com contador + "Selecionar todos (N)" + "Limpar", e o formulário
  do motivo único de descarte. `wire:key` por linha.

Sem migration (só comportamento/UI). O fluxo de linha única pré-existente
permanece intacto.

## Testes

`AlertsInboxTest` (+8): selecionar todos / limpar; select-all sob o filtro "todos"
pega só os abertos visíveis; iniciar análise em lote só nos `novo`; virar caso em
lote; descarte em lote exige motivo e aplica a todos; ação em lote não toca outra
org; limpar seleção ao trocar triagem e ao trocar severidade.

## Portões

Pint ok · PHPStan nível 8 (0 erros) · Pest **351 verdes**.

## Validação (Juiz)

Ciclo 1 — **APROVADO 85/100**, sem blockers. Dois achados confirmados, ambos
resolvidos no mesmo ciclo:

- **A1** (média) — "selecionar todos" vinha de uma query à parte
  (`selectableIds()`), que divergia da página sob o filtro "todos" com >100
  eventos. **Corrigido**: fonte única via `visibleEvents()`/`openIds()`.
- **A2** (baixa) — AC "limpar seleção ao trocar severidade" sem teste.
  **Corrigido**: teste adicionado.

## Deploy

Sem migration. Mudou Blade (view) → `deploy.sh --assets`.
