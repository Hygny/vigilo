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
liga/desliga o realce vermelho das empresas com situação cadastral negativa
(baixada/inapta/suspensa/nula). **On por padrão.**

- `Graph::$showNegative` + `toggleNegative()` → despacha o evento `grifo-negative`
  (não rebuilda o grafo).
- `grifo.js`: `highlightNegative` entra em `fillFor()`/`styleSheet()`; o método
  `setNegative()` (ouvindo `@grifo-negative.window`) só reestiliza o canvas — sem
  re-layout nem nova consulta. A legenda "Situação negativa" só aparece quando o
  realce está ligado.

Sem migration. Só UI/JS.

## Testes

`AlertsInboxTest` (+2): caso mostra "Ver no grafo" apontando para `companies.graph`;
alerta não-caso não mostra o link. `CompanyGraphTest` (+1): `toggleNegative`
alterna `$showNegative` e despacha `grifo-negative` com o flag.

## Portões

Pint ok · PHPStan nível 8 (0 erros) · Pest **358 verdes** · Vite build ok.

## Validação (Juiz)

Ciclo 1 — **APROVADO 98/100**, sem blockers. Achado A1 (baixa, testes: faltava
assert negativo do link) **resolvido no mesmo ciclo**. A2/A3/A4 observações sem
desconto (A2 re-render server pré-existente dos toggles; A3 mirror da lista
`NEGATIVE` PHP↔JS já documentado; A4 sem toggle no modo pessoa — compatível com
o aceite, que amarra o botão à posição do toggle de endereço, só-empresa).

## Deploy

Sem migration. Mudou Blade + JS → `deploy.sh --assets`.
