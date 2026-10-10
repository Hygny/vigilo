# Fatia — Relatório de ligações abaixo do grafo

Painel **"Ligações"** abaixo do canvas do grifo (`App\Livewire\Companies\Graph`):
a lista das entidades conectadas ao centro atual, com **Copiar** (texto para
e-mail) e **Exportar Excel**. Complementa o canvas e os painéis de Filiais /
Beneficiários.

## O que mudou

- **`GraphConnection`** (novo DTO): uma linha do relatório — `nome`, `documento`
  (CNPJ formatado ou CPF mascarado), `tipo`, `situacao`.
- **`Graph::buildConnections()`**: classifica cada nó/filial pelo tipo de ligação —
  **Sócio** (sócios diretos PF/PJ/estrangeiro), **Grupo econômico** (empresas por
  sócio em comum), **Mesmo endereço** (vizinhas por CEP+número, quando o toggle
  está ligado), **Filial** (do painel de Filiais, relação certa por CNPJ) e
  **Empresa do sócio** (no modo pessoa). Fonte única da tabela, do Copiar e do
  Excel. `connectionsText()` monta o texto pt-BR agrupado por tipo.
- **`Graph::exportConnections()`**: ação Livewire que baixa o .xlsx via
  **`GraphConnectionsExport`** (novo, espelha `AlertsExcelExport`). Recomputa o
  grafo no próprio request → respeita foco + toggles.
- **View**: card com a tabela (Nome / CNPJ-Documento / Tipo de ligação /
  Situação), **Copiar** (Alpine + `navigator.clipboard`) e **Exportar Excel**.
  O relatório reflete foco (recentrar) e os toggles de sócio PF / endereço.

Sem migration (comportamento/UI + 2 classes novas). Reaproveita o grafo e os
índices já existentes — sem custo novo de base.

## Testes

`CompanyGraphTest` (+5): classificação das ligações (Sócio/Grupo) + documento
mascarado; vizinha de endereço só com o toggle ligado; export `.xlsx`
(`assertFileDownloaded`); modo pessoa → "Empresa do sócio"; texto do Copiar
agrupado por tipo em pt-BR.

## Portões

Pint ok · PHPStan nível 8 (0 erros) · Pest **356 verdes**.

## Validação (Juiz)

Ciclo 1 — **APROVADO COM RESSALVAS 84/100**, sem blockers. Quatro achados, todos
resolvidos no mesmo ciclo:

- **A1** (alta, corretude) — o texto do Copiar vinha cacheado no `x-data` do
  Alpine e não reavaliava no morph do Livewire, então ficava defasado após
  toggle/foco. **Corrigido**: o texto é lido na hora do clique via
  `$refs.copytext.textContent` de um nó oculto que o Livewire re-renderiza a cada
  mudança.
- **A2** (baixa) — detecção de CNPJ de 14 dígitos duplicada. **Corrigido**: helper
  único `cnpjDigits()` reusado em `formatDocument()` e `toCytoscape()`.
- **A3** (baixa) — rótulo "Ligação" na tabela ≠ "Tipo de ligação" no Excel.
  **Corrigido**: unificado para "Tipo de ligação".
- **A4** (média, testes) — faltava cobrir o modo pessoa e o conteúdo do Copiar.
  **Corrigido**: 2 testes adicionados.

## Deploy

Sem migration. Mudou Blade (view) → `deploy.sh --assets`.
