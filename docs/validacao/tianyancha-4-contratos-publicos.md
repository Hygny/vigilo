# Validação — Roteiro Tianyancha 4/4 · Contratos públicos

- **Data:** 2026-11-25
- **Ciclos:** 2
- **Veredito final:** APROVADO — **90/100** (`acao: seguir`), sem blockers
- **Portões (Juiz, ciclo 2):** Pint ok · PHPStan nível 8 ok · Pest 672/672 (677/677 após as ressalvas)

## Escopo
- Card **"Contratos públicos"** (`Empresas\ContratosPublicos`, lazy, placeholder próprio) no perfil público e na página da carteira: contratos do Poder Executivo Federal com a empresa como **fornecedora**, consultados na hora na API do Portal da Transparência (`contratos/cpf-cnpj`), sem disco.
- A API não ordena por data nem diz o total: lê até a última página (1ª sozinha, depois lotes paralelos `Http::pool`) ou o teto (40 páginas), com prazo total; revalida o fornecedor pela raiz; dedup por id; mais recentes primeiro.
- **PNCP:** a API oficial de consulta não filtra por fornecedor e a busca do site recusa cliente não-navegador (não burlamos) → link para a busca do próprio site. **Licitações por participante:** nenhuma das duas APIs oferece → fora.
- Cache de 24 h (só resultado bom) + janela curta após falha; LGPD: `contatoResponsavel` (servidor) nunca exibido.

## Ciclo 1 — REPROVADO 74
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| B1 | blocker | Cache inoperante em produção: `serializable_classes => false` → Redis devolvia `__PHP_Incomplete_Class` (o cache do `RiscoService` tinha o MESMO defeito, escondido) | ✅ Lista explícita só dos DTOs cacheados (`RiscoReport`, `Sancao`, `FonteSancao`, `ContratosReport`, `ContratoPublico`); teste com store que serializa (inclui classe fora da lista bloqueada) |
| A1 | média | Placeholder reaproveitado de "Risco" | ✅ Placeholder próprio |
| A2 | média | Faltavam testes: falha em página ≥ 2, ConnectionException no pool, dedup | ✅ Os três |
| A3 | baixa | Corte no meio da palavra | ✅ `preserveWords: true` + teste real |
| A4 | baixa | Texto do truncado impreciso | ✅ "lidos N; API sem ordem por data: pode haver mais, inclusive mais recentes" |
| A5 | baixa | Pior caso de latência | ✅ Prazo total entre lotes → indisponível |
| A6 | baixa | Falha sem backoff | ✅ Janela `falha_minutos` |
| A7 | baixa | Formatação no Blade / docblock | ✅ `ContratoPublico::detalhe()` |

## Ciclo 2 — APROVADO 90
| ID | Severidade | Ressalva | Resolução |
|---|---|---|---|
| A1 | alta (processo) | Placeholder não rastreado no git | ✅ Incluído no commit |
| A2 | baixa | Configuração da requisição duplicada (pool × simples) | ✅ `PortalTransparenciaClient::configurar()` |
| A3 | baixa | Sem teste do truncado, do "mais recentes de N" e da montagem no perfil | ✅ Três testes |
| A4 | baixa | Selo "vigente" ignorava a situação | ✅ Rescindido/cancelado/anulado/encerrado/extinto/suspenso não é vigente; situação aparece no detalhe |
| A5 | baixa | 200 com corpo que não é lista virava "nenhum contrato" | ✅ Vira indisponível (não cacheia) + teste |
| A6 | baixa | Truncado sem link; total sem marca de parcial | ✅ "ao menos N" + link da página da empresa no Portal |
| A7 | baixa | Client importava o consumidor | ✅ Docblock em texto, sem import |
