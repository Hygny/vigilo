# Validação — Recuperação de bens 1/3: Bens e recebíveis

- **Data:** 2026-11-25
- **Ciclos:** 2
  - Ciclo 1: REPROVADO — **63/100**
  - Ciclo 2: APROVADO — **89/100** (`acao: seguir`), sem blockers
- **Portões (Juiz, ciclo 2):** Pint ok · PHPStan nível 8 ok · Pest 712/712 (716/716 após as ressalvas do ciclo 2) · migration reversível

## Escopo
- `vigilo:aeronaves-anac` (diário 07:15 BRT): cópia local do Registro Aeronáutico Brasileiro (ANAC, dados abertos). A recarga é total, num átomo, lida e gravada em lotes. Os pisos (absoluto, relativo à carga anterior, de vínculos e de JSON inválido) são conferidos antes do commit. A rodada fica registrada no card "Rotinas".
- `AeronavesLookup`: aeronaves por raiz do CNPJ (vínculo certo) e por nome de pessoa (vínculo possível). O teto vale por aeronave, com contagem à parte.
- Card lazy **Bens e recebíveis** (perfil público e página da carteira). Mostra:
  - aeronaves com ônus, "sem ônus" e % de propriedade;
  - participações (quotas/ações);
  - contratos federais vigentes, como créditos a receber;
  - o aviso de que imóveis, veículos e contas não têm fonte aberta.
- Página da pessoa: **Possíveis aeronaves** pelo nome.
- LGPD: de pessoa física fica só o nome normalizado, o % e a UF. O documento da ANAC não é guardado, porque a máscara dele complementa a da Receita.

## Ciclo 1 — achados e resolução
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| A1 | alta | O arquivo inteiro ia para a RAM (pico de 128–134 MB) e estourava os 128M do container | ✅ Leitura e gravação em lotes de 500 dentro da transação, download por `sink`. Pico de 50 MB com o arquivo real (46 MB nos sintéticos do Juiz), também a 64M |
| A2 | média | % de propriedade sobrescrito pela linha de operador | ✅ % = soma dos vínculos de proprietário (teto 100); UF do dono; papéis em ordem fixa |
| A3 | média | `limit(200)` por linha cortava aeronaves sem aviso | ✅ Teto por aeronave (`LIMITE`) + `contagemDaEmpresa()` + "Mostrando N de M". No real: raiz com 904 aeronaves |
| A4 | média | JSON torto trocava a cópia boa por 0 vínculos | ✅ Teto de 1% de JSON inválido e piso de 0,5 vínculo por aeronave |
| A5 | média | Participações e % sem teste | ✅ Teste com 12 participações (link, "Mostrando 10 de 12") e com "(100%)"/"(50%)" |
| A6 | baixa | Contratos truncados sem "ao menos" | ✅ "ao menos" no resumo e na linha de recebíveis |
| A7 | baixa | PF guardava o nome cru | ✅ `nome` só para PJ (coluna nullable); 0 PF com nome ou documento no banco real |
| A8 | baixa | `temAlgo()` morto; teto sem constante | ✅ Removido; `AeronavesLookup::LIMITE` |

## Ciclo 2 — ressalvas (corrigidas antes do commit)
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| A1 | média | Marca reservada (3.081 no real) e "MATRICULA CANCELADA" sem data (6) saíam como ativas "sem ônus" | ✅ `Aeronave::cancelada()`/`reservada()`/`ativa()` são a fonte única. A ordenação e a contagem usam o SQL derivado das mesmas constantes, e o card tem o selo "marca reservada · não matriculada". No real, a raiz com 904 aeronaves passa de 292 para 51 ativas |
| A2 | baixa | Piso absoluto de 10 mil (28% da cópia); a contagem da carga anterior não era lida | ✅ O arquivo precisa ter ao menos 80% das aeronaves da carga anterior (`bens.aeronaves.queda_maxima` = 0,2) |
| A3 | baixa | Sem teste do download com falha nem do "não consultado" da pessoa | ✅ Testes com HTTP 503 (rodada falha, cópia mantida) e com a página da pessoa antes da 1ª carga |
| A4 | baixa | Papel/tipo como texto solto | ✅ Constantes `Aeronave::PAPEL_*`/`TIPO_*`/`PAPEIS` e `rotuloPapeis()` nas views |
