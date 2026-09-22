# V2-F8 — Grafo interativo estilo Obsidian (Cytoscape) + foco por pessoa

> O "grifo" virou um canvas interativo: física de força, arrastar, zoom/pan, nomes dentro das bolhas, cores distintas empresa×sócio (claro e escuro), e clique que recentra — inclusive numa **pessoa** (todas as empresas dela).

- **Data:** 2026-09-22
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 99/100** (`acao: seguir`, sem blockers; A1 corrigido)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **261/261** (792 asserts) · `npm run build` ok (Cytoscape empacotado, ~139 KB gzip)

## O que entrou

- **Cytoscape client-side** (`resources/js/grifo.js`, componente Alpine `grifo`, importado em `resources/js/app.js`): layout de força (`cose`), **zoom/pan só no canvas** (roda + botões +/−/ajustar), rótulo **dentro** da bolha, arrastar nós. Inicializa a partir de `@js($cyto)`.
- **Cores por papel, validadas nos dois temas** (`--graph-*` em `app.css`): **empresa = azul `#1D4ED8`**, **sócio = âmbar `#B45309`**, **situação negativa = vermelho `#DC2626`**, **nó central com anel**. Rótulo branco com contraste ≥4.5:1 em ambos (fills saturados iguais nos dois temas; no escuro só mudam aresta e anel). Re-aplica ao trocar tema (`MutationObserver` em `data-theme`).
- **Clique recentra**: empresa/sócio PJ → `focusOn(cnpj)`; **PF → `focusPerson(doc, nome)`**. O componente Livewire produz `cyto` (nós com `role`/`isCenter`/`cnpj`/`doc`/`name`/`situacao` + arestas) + beneficiários; ao trocar foco dispara `grifo-update` e o canvas (`wire:ignore`) atualiza pelo evento (sem morph que quebre o Cytoscape).
- **PF casa por documento + nome** (`OwnershipGraphService::forPerson($doc, ?$name)` filtra `nome_socio`), reduzindo a colisão do CPF mascarado. O **nome viaja como dado JS** (dentro de `@js($cyto)`, escapado pelo Blade), **nunca** em atributo HTML → sem injeção. `focusPerson` sanitiza o doc (`[0-9*]`) e limita o nome; `focusOn`/`focusPerson` ignoram entrada inválida e limpam o foco oposto.
- **Modo pessoa**: oculta o painel de beneficiários (não chama `beneficialOwners`); a tela degrada ("Grafo indisponível") se a base cair.

## Segurança (verificação adversarial do Juiz)
- **XSS**: nenhum `{!! !!}` de dado; tudo via `@js($cyto)` (escapa HTML+JS) e `{{ }}`; rótulos pintados no canvas do Cytoscape (`label: data(label)`), não em `innerHTML`.
- **SQLi**: `forPerson` usa binding em `->where('s.nome_socio', $name)`.
- **Multi-tenant**: `mount` faz `authorize('view')`; foco lê base pública da Receita (não escopada por tenant), consistente com o design.

## Achado
- **A1 (baixo):** a lista `NEGATIVE` (JS) duplicava o conjunto negativo de `SituacaoCadastral` (PHP) → **corrigido** com comentários cruzados nos dois arquivos (atualizar ambos ao incluir novo código negativo).

## Deploy
Assets novos (JS + CSS) → **`./src/deploy/deploy.sh --assets`** (o deploy roda `npm ci && npm run build`, que instala o Cytoscape). Sem migration.

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

> Observação: a renderização/interatividade do canvas não é testável em PHP (sem camada JS/Dusk — débito DT-9). Validado: dados (`cyto`), segurança, contraste e build. Confirmação visual final é no navegador após o deploy.
