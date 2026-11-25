# Validação — Recuperação de bens 2/3: Indícios de esvaziamento e sucessão

- **Data:** 2026-11-25
- **Ciclos:** 2
  - ciclo 1: REPROVADO, **69/100**
  - ciclo 2: APROVADO COM RESSALVAS, **79/100** (`acao: seguir`), sem blockers
- **Portões (Juiz, ciclo 2):** Pint ok · PHPStan nível 8 ok · Pest 743/743 (746/746 após as ressalvas) · sem migration

## Escopo
- **Marco:** 1ª inscrição em dívida ativa da União (`PgfnLookup::primeiraInscricao`) ou data informada no card (validada, `#[Locked]`, 10 por minuto por usuário).
- **Possíveis sucessoras:**
  - candidatas: empresas com estabelecimento no mesmo endereço (`App\Support\Endereco`; prédio lotado conta só a mesma sala) e empresas dos donos (PF por CPF mascarado + nome; PJ pela raiz), incluindo donos que saíram depois do marco;
  - entram só se surgiram depois do marco: abertas depois, filial nova no endereço, ou dono em comum que entrou depois;
  - sinais: endereço, classe CNAE, sócio em comum, telefone/e-mail;
  - ≥3 sinais = forte; 2 sinais com sócio ou CNAE = médio; endereço + contato sozinhos não listam.
- **Outros blocos:** encerramento; donos que entraram depois ("quadro todo trocado", sem a diretoria); saídas de dono e redução de capital (histórico); participações novas.
- **Card:** lazy, no perfil público e na carteira. Cada bloco degrada sozinho, e falha ou leitura cortada nunca vira "Nada encontrado".
- **Cache:** só do relatório completo; DTOs na lista do cache.
- **Refator:** regras de endereço do grafo extraídas para `App\Support\Endereco`.
- **Calibração com dados reais em produção** (só leitura, 88–201 ms): academia inapta → sucessora forte; subsidiária cervejeira → grupo controlador; Petrobras → sem falsos (filtro de donos).

## Ciclo 1 — achados e resolução
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| B1 | blocker | "Nada encontrado" com blocos que falharam | ✅ `NIVEL_PARCIAL` ("Consulta parcial"); testes com base fora e com QSA ilegível |
| A1 | média | Participações cortadas sem aviso / sem teto na tela | ✅ `parcial` no teto de leitura; 10 na tela + "Mostrando N de M" |
| A2 | média | Filial nova de empresa antiga no endereço ignorada | ✅ Avalia o estabelecimento que casou (`filialNoEndereco`) |
| A3 | média | CPF mascarado no log quando a varredura falha | ✅ `semDadosNoLog()` → `ConsultaComTimeout::reportarSemDados`; teste com `Exceptions::fake()` |
| A4 | baixa | Sem limite de datas | ✅ `RateLimiter` 10/min por usuário |
| A5 | baixa | Desempate só pela abertura | ✅ `PossivelSucessora::quando()` |
| A6 | baixa | Teste de cache sem serializar | ✅ `cache.stores.array.serialize = true` |
| A7 | baixa | Ramos sem teste | ✅ PJ pela raiz, teto 20, leitura parcial, nível degradado |
| A8 | baixa | Liquidante etc. como dono | ✅ `SEM_PARTICIPACAO` ampliada |
| A9 | baixa | Cortes sem `parcial` | ✅ candidatas e histórico ligam `parcial` (o filtro de dono do histórico segue em PHP após o teto, com `parcial`) |
| A10 | baixa | Tokens soltos, linha longa, `cepUtil` duplicado | ✅ `NIVEL_*`, `sinais()`/`listavel()`, constantes, grafo usa `Endereco::cepUtil` |

## Ciclo 2 — ressalvas
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| A1 | média | "Nada encontrado" junto com "Leitura parcial" | ✅ `NIVEL_NENHUM` só com tudo lido e sem corte; teste |
| A2 | média | Lotação do prédio ignorava as outras salas | ✅ Conta as raízes de todas as linhas lidas; teste com 15 salas |
| A3 | baixa | Mesma sala guardava a última linha | ✅ Mesma regra de "mais recente por raiz"; teste |
| A4 | baixa | QSA cortado em 400 sem `parcial` nem ORDER BY | ✅ Ordem fixa + `parcial`; teste |
| A5 | baixa | Entrada do sócio em comum dependia da ordem | ✅ Vale a mais antiga por pessoa; teste |
| A6 | baixa | Relatório incompleto re-varre a cada ida e volta | ⏳ **DT-33** |
| A7 | baixa | Rótulos de qualificação | ✅ Lista conferida contra `qualificacoes_socios` real (18 = secretário; 32, 39–43, 46, 51, 60–63 entram) |
| A8 | baixa | Texto apontava seção inexistente na carteira | ✅ Link para o perfil público |
