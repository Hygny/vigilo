# Validação — Alerta de risco nas empresas ligadas

- **Data:** 2026-11-25
- **Ciclo:** 1
- **Veredito:** APROVADO — **87/100** (`acao: seguir`), sem blockers
- **Portões (Juiz):** Pint ok · PHPStan nível 8 ok · Pest 695/695 (699/699 após as ressalvas) · migration reversível

## Escopo
- `company_neighbor_flags` (sinais conhecidos de cada empresa ligada à carteira) + `monitored_companies.neighbor_risk_synced_at`.
- `RiscoAoRedorMonitor`: compara o "risco ao redor" atual com o conhecido; 1ª vez = baseline silencioso; só sinal NOVO vira alerta; nunca compara com dado incompleto.
- `vigilo:varrer-risco-ao-redor` (09:30/12:30 BRT): um alerta `ChangeType::RiscoAoRedor` por ligada com sinal novo (alta: Lista Suja/sanção; média: dívida/situação), notificação + webhook, registro no card "Rotinas".

## Achados e resolução
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| A1 | média | Flags e alertas em commits separados (alerta podia se perder) | ✅ `aplicar()` + `persistEvents()` no mesmo `DB::transaction` |
| A2 | baixa | Falha na situação dos sócios PJ engolida → alerta falso depois | ✅ `situacaoConsultada` no relatório; `completo()` exigido pelo monitor e pelo cache |
| A3 | média | Ligada que some uma rodada era esquecida → alertas repetidos | ✅ Só esquece após 3 rodadas seguidas ausente (`ausencias`) |
| A4 | média | Ramos sem teste | ✅ Ligada que some/volta/sai de vez, ligada limpa, severidade média, linha do card, situação não lida |
| A5 | baixa | Códigos graves como texto solto | ✅ `EmpresaLigadaRisco::CODIGOS_GRAVES` |
