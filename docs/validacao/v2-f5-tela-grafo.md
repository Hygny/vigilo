# V2-F5 — Tela do grafo societário (Camada 1)

> A visualização do diferencial da V2: renderiza no painel o grafo Camada 1 (dados da V2-F4), a partir da página da empresa. SVG radial server-side, sem dependência JS.

- **Data:** 2026-09-21
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 99/100** (`acao: seguir`, sem blockers)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **247/247** (748 asserts)

## O que entrou

- **Rota** `companies/{company}/grafo` (grupo `auth`+`verified`) + componente **`App\Livewire\Companies\Graph`** que re-checa `authorize('view', $company)` no mount (policy = escopo da org) e monta o grafo via `OwnershipGraphService::for($company->cnpj)`.
- **Layout radial server-side**: empresa no **centro**, sócios no **anel interno**, grupo econômico (empresas por sócio em comum) no **anel externo**; SVG inline (nós = círculos, arestas = linhas), **sem lib JS**. Cores por papel e situação (centro em crítico quando BAIXADA/INAPTA/SUSPENSA/NULA, senão a cor da "empresa consultada"); sócio em âmbar; grupo em neutro. Legenda + contadores (nº sócios, nº empresas do grupo). Responsivo (viewBox + container `overflow-x-auto`, sem largura fixa).
- **Degrada com aviso** "Grafo indisponível" (try/catch em `Throwable`) se a base CNPJ estiver fora — nunca 500.
- **Link "Grafo societário"** (ícone `hub`) na página da empresa.

## Segurança (verificação adversarial do Juiz)
- **XSS**: rótulos, `<text>` e `<title>` escapados por Blade `{{ }}`; nenhum dado do usuário entra em `style`/`fill`/`stroke` (só `var(--...)` controlado). Sem `{!! !!}`.
- **Cross-org**: `authorize('view')` → 403 para empresa de outra org (testado).
- **Colisão Livewire**: os auxiliares `fill`/`layout` (que colidiam com `Livewire\Component`) foram renomeados para `nodeFill`/`buildLayout`; todos os helpers são `private`.

## Testes
`CompanyGraphTest` (HTTP, conexão `cnpj` → sqlite): renderiza centro + sócio + empresa do grupo (aresta reversa); mostra "Grafo indisponível" quando a base cai; **403** cross-org.

## Achado
- **A1 (baixo, cosmético):** em dark mode a cor do centro ATIVA (`--ok`) divergia do swatch "Empresa consultada" (`--primary`) da legenda → **corrigido**: centro usa `--primary` salvo situação negativa (`--crit`), alinhando com a legenda.

## Deploy
Tem classes Tailwind novas (`max-w-[1100px]`) e o ícone `hub` → **`./src/deploy/deploy.sh --assets`**. Sem migration.

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25 |
| Corretude & regra de negócio | 20 | 5 | 20 |
| Segurança & autorização | 15 | 5 | 15 |
| Arquitetura & responsabilidades | 12 | 5 | 12 |
| Banco & performance | 12 | 5 | 12 |
| Testes | 10 | 5 | 10 |
| Legibilidade & manutenção | 6 | 5→5 | 6 (A1 corrigido) |
| **Total** | **100** | | **99 → 100** (com A1 corrigido) |

## Próximo
**V2-F6** — recursão do grafo (níveis além da Camada 1) + beneficiário final; expansão interativa (clicar um nó recentra/aprofunda) e % de participação (Camada 2).
