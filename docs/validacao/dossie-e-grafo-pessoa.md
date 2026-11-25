# Validação — D: Dossiê completo + grafo pela pessoa

- **Data:** 2026-11-25
- **Ciclo:** 1
- **Veredito:** APROVADO COM RESSALVAS, **77/100** (`acao: seguir`), sem blockers.
- **Portões (Juiz):** Pint ok · PHPStan nível 8 ok · Pest 816/816 (822/822 após as ressalvas) · sem migration

## Escopo
- **Dossiê PDF:** cada seção nova vem do mesmo serviço da tela e degrada sozinha. As seções novas:
  - capital social e Simples/MEI nos dados cadastrais;
  - participações completas (até 300, com "N de M"), com a dívida ativa de cada uma;
  - risco ao redor;
  - contratos públicos;
  - bens e recebíveis: aeronaves, marcas, participações e créditos a receber;
  - indícios de esvaziamento, com a data da devedora quando houver.
- **`Pessoas\Grafo` (`/pessoas/{chave}/grafo`):**
  - o grafo nasce centrado na pessoa, casada por documento e nome (`GraphPage::rootPerson()`);
  - dá para pivotar para uma empresa e voltar à pessoa;
  - propriedades `#[Locked]`;
  - o acesso fica na trilha `pessoa_consultas` (tipo `grafo`) e conta no mesmo teto do perfil;
  - responde 404 para chave desconhecida, estrangeiro ou pessoa sem documento, e 403 para quem está fora de uma organização.

  A página da pessoa ganhou o link "Ver no grafo societário".

## Ressalvas e resolução
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| A1 | alta | "Risco ao redor" no PDF dizia "nenhuma" com a PGFN fora e omitia o aviso de CGU velha | ✅ Mesma regra da tela (`semSinalConfiavel()`), "nas fontes que puderam ser consultadas" e aviso de cópia desatualizada; teste |
| A2 | média | O teste da lista completa não distinguia o comportamento novo do antigo | ✅ `reverse_limit` = 5 no teste e 301 participações → "Listando 300 de 301" |
| A3 | média | Seções sem teste do caminho feliz | ✅ Testes de capital/Simples/MEI, risco ao redor (limpo, com sinal e com fonte fora), contratos e indícios com `recuperacao_marco` |
| A4 | média (suspeita) | O PDF roda em sequência os blocos que a tela carrega lazy | ⏳ Medido em produção com cache frio (ver abaixo) |
| A5 | baixa | Regra de estrangeiro do grafo ≠ regra do link | ✅ `$pessoa->estrangeiro` no `abort_if`, constante morta removida; teste |
| A6 | baixa | Contratos no PDF listavam só os vigentes | ✅ `ContratosReport::destaques(10)`: vigentes primeiro, depois os encerrados mais recentes, marcados |
| A7 | baixa | Comentário divergente | ✅ Alinhado ao comportamento |
| A8 | baixa | Ternário aninhado do foco | ✅ `GraphPage::foraDaRaiz()` |
| A9 | baixa | Lacunas no teste do grafo da pessoa | ✅ 404 sem documento e link escondido para estrangeiro |
