# Validação — Roteiro Tianyancha 1/4 · Perfil público completo (dados)

- **Data:** 2026-11-25
- **Ciclos:** 2
- **Veredito final:** APROVADO — **90/100** (`acao: seguir`), sem blockers
- **Portões (Juiz, ciclo 2):** Pint ok · PHPStan nível 8 ok · Pest 613/613 (615/615 após as ressalvas)

## Escopo
- Grafo societário de **qualquer CNPJ** em `/empresas/{cnpj}/grafo` (`Empresas\Grafo`); lógica comum do grafo extraída para `App\Livewire\Graph\GraphPage` (usada também por `Companies\Graph`, que mantém a policy da carteira).
- **Participações** (empresas em que o centro é sócio PJ) no grafo (aresta `participacao`, contagem, tipo no relatório/Copiar/Excel, estilo e legenda nos dois temas), no perfil e no dossiê PDF (seção própria).
- Perfil público com **natureza jurídica, porte, capital, Simples/MEI** (`DetalhesEmpresaLookup`), **beneficiários finais**, **participações** e **estabelecimentos**; cada bloco degrada sozinho.
- Teto anti-raspagem do perfil compartilhado com o grafo público (`CountsProfileViews`).

## Ciclo 1 — REPROVADO 72
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| A1 | alta (segurança) | `focus`/`focusDocument`/`focusName` sem `#[Locked]`: o cliente mudava o foco por `updates` e contornava os tetos do perfil e do pivô | ✅ `#[Locked]` nas três + teste de recusa do `set()` |
| A2 | média | Dossiê PDF listava investidas como "Grupo econômico"; API ganhou arestas `participacao` sem declaração | ✅ `DossieGenerator::empresasLigadas()` separa participações × grupo (seções próprias) + teste; acréscimo na API declarado |
| A3 | baixa | Dedup com `limit*2` falseava o total de participações | ✅ Dedup no SQL (GROUP BY por investida) + teste |
| A4 | baixa | Perfil percorre `beneficialOwners` a cada render; título do grafo consultava por request | ✅ Título lido no `mount`; custo do perfil → **DT-29** |
| A5 | baixa | `date()` duplicado, `'02'` literal, docblock velho | ✅ `App\Support\DataReceita`, `SituacaoCadastral::code('ATIVA')`, docblock atualizado |

**Achado em produção durante a correção:** na base carregada o documento do sócio PJ vem só com a raiz (8 díg). Participações passaram a casar raiz OU CNPJ de 14 díg; o nó `socio_pj` do grafo exibe o CNPJ da matriz (fica clicável/consulta PGFN). → **DT-30**.

## Ciclo 2 — APROVADO 90
| ID | Severidade | Ressalva | Resolução |
|---|---|---|---|
| A1 | baixa | Falha só da contagem exibia "sócia de 0" com a lista cheia | ✅ Cai para o tamanho da lista |
| A2 | baixa | Grafo mostrava a contagem cortada pelo teto sem aviso | ✅ "25+" quando bate no `reverse_limit` + teste |
| A3 | baixa | "Mostrando N de M" sem teste de componente; mudança do `documento` do sócio PJ na API não declarada | ✅ Teste + DT-30 cita a mudança |
