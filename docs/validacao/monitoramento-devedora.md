# Validação — Recuperação de bens 3/3: Monitoramento de devedora

- **Data:** 2026-11-25
- **Ciclos:** 2
  - Ciclo 1: REPROVADO, **69/100**
  - Ciclo 2: APROVADO COM RESSALVAS, **84/100** (`acao: seguir`), sem blockers
- **Portões (Juiz, ciclo 2):** Pint ok · PHPStan nível 8 ok · Pest 772/772 (775/775 após as ressalvas) · migration reversível (up/rollback/up)

## Escopo
- **Card `Companies\Recuperacao`** (página da carteira): marca/desmarca a empresa como devedora e aceita a data da dívida, opcional. `DevedorMonitor::marcar()`/`desmarcar()` gravam em transação, sob a trava da linha.
- **`SincronizarDevedorJob`:** grava a linha de base. Só mexe nos tipos que ainda não têm uma e não conta como checagem do dia.
- **`DevedorMonitor`:** consulta e aplica, por tipo de item:
  - aeronave ativa;
  - participação;
  - contrato federal vigente;
  - possível sucessora.

  Tipo com fonte incompleta não é comparado. Erro marca `falhou` e vai para o log sem o SQL. A linha de base é por tipo (`recuperacao_tipos`). Item que some só é esquecido após 3 rodadas seguidas. Se o marco mudar no meio, as sucessoras são descartadas. Os itens vistos são gravados em lote (upsert).
- **`vigilo:varrer-devedores`** (08:00 e 13:00 BRT):
  - um `ChangeEvent` por item novo (`BemNovo`/`SucessoraNova`), alta para aeronave sem ônus ou sucessora forte;
  - notificação e webhook;
  - disjuntor, que abre quando a base CNPJ cai;
  - retomável: rodada com falha não vale como checagem do dia;
  - linha no card Rotinas.
- **Privacidade:** `RazaoSocial::semDocumento()` tira o CPF da razão social de MEI/EI no alerta e no card de indícios.

## Ciclo 1 — achados e resolução
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| A1 | alta | CPF mascarado de MEI/EI no alerta (Inbox, e-mail, webhook) | ✅ `RazaoSocial::semDocumento()` no alerta e no card de indícios; testes |
| A2 | média | Disjuntor cego a fonte parcial (base fora = rodada verde) | ✅ `ConsultaDevedor::falhou`; falha conta no disjuntor e não atualiza `recuperacao_synced_at` |
| A3 | baixa | Job atualizava a checagem do dia | ✅ Com `soLinhaDeBase` grava só `recuperacao_tipos` |
| A4 | média | Corrida em marcar/desmarcar × aplicar/job | ✅ Transação + `lockForUpdate`; `aplicar()` descarta sucessoras se o marco mudou |
| A5 | média | Teste vazio e lacunas | ✅ Testes de fonte incompleta, contratos, teto, disjuntor, retomada, notificação e devedora desmarcada no meio |
| A6 | baixa | Validação manual do marco no mount | ✅ `Validator` com as mesmas regras |
| A7 | baixa | Sem índice; updateOrCreate item a item | ✅ Índice em `recuperacao_desde`; upsert em lote |

## Ciclo 2 — ressalvas (corrigidas antes do commit)
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| A1 | média | Histórico cadastral fora deixava as sucessoras incompletas, comparadas como completas | ✅ `saidas === null` = `falhou` e tipo não comparado; teste com 3 rodadas |
| A2 | baixa | Devedora fora da base CNPJ ficava com `falhou` permanente | ✅ `EsvaziamentoReport::foraDaBase` = não se aplica; card mostra o motivo; teste |
| A3 | baixa | Regras da data duplicadas | ✅ `App\Support\DataDivida::REGRAS` nos dois cards |
| A4 | baixa | Sem teste de sucessora forte = alta | ✅ Teste |
| A5 | baixa | Horário da rotina escrito à mão | ✅ `SweepDevedores::CRON`/`HORARIOS` (agenda, card Rotinas, card da devedora) |
