# Fatia — Link ao grafo em casos + toggle de situação negativa

Dois ajustes de UX pedidos pelo usuário.

## O que mudou

### 1. Alerta "caso" → direto ao grafo
Na Inbox de alertas, um alerta com triagem **"Virou caso"** ganha um botão
**"Ver no grafo"** (ícone `hub`, em destaque âmbar) que navega direto ao grafo
societário da empresa (`route('companies.graph', $event->monitoredCompany)`, via
`wire:navigate`). Fica ao lado do "Reabrir".

### 2. Toggle de "Situação negativa" no canvas
Na tela do grafo, um botão **ao lado de "Mostrar empresas no mesmo endereço"**
mostra/esconde no canvas as empresas com situação cadastral negativa
(baixada/inapta/suspensa/nula). **On por padrão.**

- É um **filtro de nós** (remove as bolas do canvas, como os toggles de PF/
  endereço) — **não** recolorir. O realce vermelho dos nós negativos é fixo
  quando eles estão visíveis.
- `Graph::$showNegative` + `toggleNegative()` → despacha o evento `grifo-negative`
  (não rebuilda o grafo nem reconsulta a base).
- `grifo.js`: `setNegative()` (ouvindo `@grifo-negative.window`) chama
  `applyNegativeFilter()`, que adiciona/remove a classe `is-hidden`
  (`display:none`) nos nós negativos **que não são o centro** (`isNegativeNode`)
  e refaz o layout. O `display:none` tira o nó e **suas arestas** do grafo. O
  filtro é reaplicado no `boot` e após cada `refresh` (troca de dados).
- A **legenda "Situação negativa" fica sempre visível** (não condicionada ao
  toggle).

Sem migration. Só UI/JS.

## Testes

`AlertsInboxTest` (+2): caso mostra "Ver no grafo" apontando para `companies.graph`;
alerta não-caso não mostra o link. `CompanyGraphTest` (+1): `toggleNegative`
alterna `$showNegative` e despacha `grifo-negative` com o flag.

## Portões

Pint ok · PHPStan nível 8 (0 erros) · Pest **358 verdes** · Vite build ok.

## Validação (Juiz)

Ciclo 1 — **APROVADO 98/100** (toggle como realce de cor). O usuário recusou: queria
que o toggle **removesse as bolas do canvas** (filtro, como os outros) e mantivesse
a legenda.

Ciclo 2 (correção) — **APROVADO 98/100**, sem blockers. O toggle passou a filtrar os
nós (não recolorir), preservando o centro e mantendo a legenda sempre. Achado A1
(média, docblocks de `$showNegative`/`toggleNegative` ainda descreviam o realce)
**resolvido no mesmo ciclo**.

## Deploy

Sem migration. Mudou Blade + JS → `deploy.sh --assets`.
