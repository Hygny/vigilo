# V2-F4 — Grafo societário (Dados + API), Camada 1

> A fundação de dados do diferencial da V2: serviço + estrutura serializável + endpoint autenticado que montam o grafo societário de um CNPJ. Sem tela ainda (a visualização é fatia posterior).

- **Data:** 2026-09-21
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 100/100** (`acao: seguir`, sem blockers)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **244/244** (740 asserts)

## O que entrou

- **`App\Services\Graph\OwnershipGraphService::for($cnpj)`** — monta o grafo **Camada 1** da base local (conexão `cnpj`):
  - **centro**: a empresa consultada (razão social + situação, traduzida);
  - **sócios diretos**: consulta em `socios` por `cnpj_basico` (join `qualificacoes_socios` para o rótulo da qualificação);
  - **grupo econômico**: consulta **reversa** por `cnpj_cpf_do_socio` (usa o `idx_socios_documento`) → as demais empresas de cada sócio.
  - Nós **deduplicados** por id estável (um sócio em N empresas aparece 1 vez); arestas deduplicadas por `de|para`; expansão reversa **limitada por sócio** (`config('cnpj.graph.reverse_limit')`, default 25) para o grafo não explodir. Degrada com graça se o centro não estiver na base (nó-centro rotulado pelo CNPJ).
- **DTOs** `App\DTO\Graph\{GraphNode, GraphEdge, OwnershipGraph}` — serializáveis (`toArray` → `centro`/`nos`/`arestas`). Tipos de nó: `empresa`, `socio_pf`, `socio_pj`, `socio_ext`.
- **Endpoint** `GET /api/cnpj/{cnpj}/grafo` (`Api\CnpjGraphController`, Sanctum + `throttle:cnpj-api`) — **escopado à carteira da organização** (igual ao `/api/cnpj/{cnpj}`): só monta o grafo para um CNPJ que a org monitora (senão 404); 422 para CNPJ != 14 dígitos; 401 sem auth. O pivô parte sempre da carteira (não é raspagem de CNPJ arbitrário); o grafo, aí sim, revela empresas conectadas fora da carteira.
- **Refactor**: o mapa de situação cadastral (código→texto) saiu do `LocalCnpjProvider` para `App\Support\SituacaoCadastral` (fonte única), consumido pelo provider e pelo grafo — sem duplicação.

## Testes
- `OwnershipGraphServiceTest`: grafo com centro + sócios (PF/PJ) + grupo econômico via aresta reversa, com dedup; cap reverso aplicado (limite 2 → 2 de 3); degradação do centro ausente; resolução do container.
- `CnpjGraphTest`: 401 sem auth · 404 fora da carteira (mesmo existindo na base) · 200 monitorado (`centro`/`nos`) · 422 CNPJ inválido.

## Achados (todos baixos — aplicados na hora)
- **A1** — arestas não deduplicadas → **corrigido** (chave `de|para`).
- **A2** — reverso usava INNER join a `empresas` → **corrigido** (`leftJoin`, simétrico ao centro).
- **A3** — degradação do centro ausente sem teste → **corrigido** (teste adicionado).

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25 |
| Corretude & regra de negócio | 20 | 5 | 20 |
| Segurança & autorização | 15 | 5 | 15 |
| Arquitetura & responsabilidades | 12 | 5 | 12 |
| Banco & performance | 12 | 5 | 12 |
| Testes | 10 | 5 | 10 |
| Legibilidade & manutenção | 6 | 5 | 6 |
| **Total** | **100** | | **100** |

## Próximo
**V2-F5** — recursão do grafo (níveis além da Camada 1) + beneficiário final; e a **tela** do grafo no painel (visualização dos nós/arestas com selo de status, expansível por nível).
