# Fatia 21 — Alerta quando um CNPJ entra no monitoramento já em situação negativa

> Bug reportado: adicionar um CNPJ que já está BAIXADA/INAPTA/SUSPENSA/NULA não gerava nenhum alerta.

- **Data:** 2026-09-16
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 100/100** (`acao: seguir`, sem blockers, sem achados)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **204/204** (597 asserts)

## O bug

`CompanyDiffer` só emitia alertas em **transições**. A primeira coleta era sempre baseline sem alerta (`diff` retornava `[]` quando `previous === null`). Consequência: um CNPJ adicionado já em situação negativa tinha o snapshot salvo, mas **nenhum alerta** — só alertaria se a situação mudasse depois. Para due diligence, adicionar um fornecedor já inapto **tem** que alertar na hora.

## A correção

No baseline, em vez de retornar `[]` seco: se a empresa já entra em situação negativa (BAIXADA/INAPTA/SUSPENSA/NULA), emite **1 `ChangeEventData` `SituacaoChanged` crítico** com `oldValue = null` (sem valor anterior → a UI mostra "— → BAIXADA") e `newValue` = a situação. Empresa que entra **ATIVA** (ou situação vazia) continua sem alerta — baseline silencioso preservado.

Reaproveita o `ChangeType::SituacaoChanged` (inbox e e-mail já renderizam) e a coluna `change_events.from_snapshot_id` nullable (evento de baseline não tem snapshot anterior). Notificação à organização dispara normalmente.

**Escopo:** vale para **adições novas** (que passam pelo baseline). Empresas já na carteira não recebem alerta retroativo — se necessário, um comando de reprocessamento pode ser feito à parte.

## Testes

- `CompanyDifferTest`: entra negativa → 1 crítico (`oldValue` null) para cada um dos 4 status; entra ATIVA → `[]`.
- `RefreshMonitoredCompanyJobTest`: ponta a ponta — 1ª coleta BAIXADA → 1 evento crítico persistido (`from_snapshot_id` null) + notificação enviada.

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25,0 |
| Corretude & regra de negócio | 20 | 5 | 20,0 |
| Segurança & autorização | 15 | 5 | 15,0 |
| Arquitetura & responsabilidades | 12 | 5 | 12,0 |
| Banco & performance | 12 | 5 | 12,0 |
| Testes | 10 | 5 | 10,0 |
| Legibilidade & manutenção | 6 | 5 | 6,0 |
| **Total** | **100** | | **100** |
