# Fatia 3 — CompanyDiffer (função pura) + bateria de testes

- **Data:** 2026-08-14 (validação retroativa)
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 100/100** (`acao: seguir`)
- **Portões:** Pint ok · PHPStan nível 8 ok · Pest ok · Migrations n/a

## Resumo
`CompanyDiffer` — função pura, determinística, sem I/O: compara snapshot anterior × novo e devolve `list<ChangeEventData>` com as regras de severidade (situação negativa=critical, sócio=high, endereço/cnae/porte=medium, nome=low, baseline=sem evento). 20 testes cobrindo os 7 tipos + edges.

## Achados
- **A1** (baixa, suspeita, banco) — a pureza depende do consumidor pré-carregar `partners` (projeto sem `preventLazyLoading`). **Já satisfeito**: `RefreshMonitoredCompanyJob` carrega o snapshot anterior com `->with('partners')` e o `SnapshotService` retorna o novo com `load('partners')`. Sem ação nesta fatia (o código do differ é puro; responsabilidade do chamador, atendida).
