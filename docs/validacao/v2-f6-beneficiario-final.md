# V2-F6 — Recursão do grafo + beneficiário final estrutural

> Sobe a cadeia societária (sócios PJ → seus sócios → … → pessoas físicas no topo) para revelar quem controla no fim. **Estrutural** — sem a % de participação (que não existe na base aberta da Receita), então não aplica o critério legal de ≥25%. A % fica como enriquecimento pago futuro.

- **Data:** 2026-09-21
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 100/100** (`acao: seguir`, sem blockers)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **252/252** (762 asserts)

## O que entrou

- **`OwnershipGraphService::beneficialOwners($cnpj, ?maxDepth)`** — BFS subindo a cadeia: sócio **PF/estrangeiro** vira beneficiário; sócio **PJ** (`identificador '1'`) é expandido pela própria `cnpj_basico` (8 primeiros díg do documento). **Cycle-safe** (`visited` por `cnpj_basico`, nunca revisita), limitado por **profundidade** (`config('cnpj.graph.max_depth')`, default 5) e por **teto de empresas visitadas** (`max_companies`, default 300). Uma PF alcançada por vários caminhos aparece **uma vez, no menor nível**.
- **DTO `App\DTO\Graph\BeneficialOwner`** (nome, documento, tipo `pf|ext`, nível) serializável.
- **API**: `GET /api/cnpj/{cnpj}/grafo` passou a incluir `beneficiarios` (além de `centro/nos/arestas`), mantendo o escopo à carteira.
- **Tela**: painel **"Beneficiários finais (estrutura)"** na página do grafo, listando as PFs (nome, documento, nível) com o aviso de que é estrutural (sem %, não é o ≥25% legal).

## Testes
`OwnershipGraphServiceTest` (beneficiário): PF direta (nível 1); PJ→PF (nível 2); teto de profundidade (não sobe além, `maxDepth:1` → 0); cycle-safe (A↔B se possuem → termina); dedup mantendo o menor nível. `CnpjGraphTest`: resposta inclui `beneficiarios`. `CompanyGraphTest`: a tela mostra o painel.

## Achado
- **A1 (baixo, tradeoff):** o render do grafo faz **duas travessias** da base (`for()` = grupo econômico reverso; `beneficialOwners()` = cadeia de controle) — computam coisas diferentes, ambas capadas. Registrado como **DT-13** para monitorar via profiling; não desconta.

## Deploy
Tela ganhou markup novo → **`./src/deploy/deploy.sh --assets`**. Sem migration.

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

## Roadmap V2 — concluído
V2-F1 (base) · F2 (driver local) · F3 (re-coleta) · F4 (grafo dados/API) · F5 (tela) · **F6 (recursão + beneficiário final)**. Enriquecimento futuro: **% de participação** (fonte paga por CNPJ) e expansão interativa (clicar nó recentra/aprofunda a tela).
