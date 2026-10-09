# Fatia — Endereço no grifo (visual)

Item 3 (e último) do roadmap. Mostra no canvas societário as **empresas no mesmo
endereço** (CEP + número) do centro — uma dimensão a mais além de sócios/grupo/
filiais. Atrás de um toggle, como as conexões por sócio PF.

## O que mudou

- **`OwnershipGraphService::for($cnpj, $includePfGroup = false, $includeAddress = false)`**:
  com `$includeAddress`, acrescenta os "vizinhos de endereço" — outras empresas
  (cnpj_basico ≠ centro) no mesmo **CEP** (filtro indexado) + **número
  normalizado** (`App\Support\AddressNumber`, "Nº 100" ≡ "100"), ligados por
  aresta `endereco`. Dedup por basico, teto `cnpj.graph.address_limit` (25),
  varredura capada (`ADDRESS_SCAN`). CEP só-zeros/vazio → não faz nada.
- **`App\Support\AddressNumber`** (novo): normalização de número, fonte única —
  o `EmpresaLookup` (OSINT-2) passou a usá-la (removida a cópia privada).
- **`Companies\Graph`**: `$showAddress` + `toggleAddress()`; passa a flag ao
  `for()`; `toCytoscape` expõe `type` na aresta; conta `addressCount` à parte
  (o "grupo econômico" exclui os vizinhos de endereço).
- **`grifo.js`**: aresta `edge[type = "endereco"]` pontilhada **teal**
  (distinta de sócio/sólida e provável/tracejada-laranja); token
  `--graph-edge-address` (claro/escuro) no app.css.
- **View**: botão "Mostrar empresas no mesmo endereço" (modo empresa), legenda +
  contagem no cabeçalho.

Reusa o índice `idx_estabelecimentos_cep` da OSINT-2 — **sem custo novo de base**.

## Testes

`OwnershipGraphServiceTest` (+vizinho casa por CEP+número normalizado; exclui
número/CEP diferente; omitido por padrão). `CompanyGraphTest` (toggle revela o
vizinho + aresta `endereco`). `VigilanciaPorEnderecoTest` segue verde (refator do
`AddressNumber`). Pintura do canvas confirmada no deploy (sem camada de browser
no CI — DT-9).

## Portões

Pint ok · PHPStan nível 8 (0 erros) · Pest 340 verdes.

## Deploy

Sem migration. Mudou JS/CSS/Blade → `deploy.sh --assets`. Precisa do índice de
CEP da OSINT-2 já aplicado (mesma `CREATE INDEX idx_estabelecimentos_cep`).
