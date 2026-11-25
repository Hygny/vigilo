# Validação — Operação · Painel de rotinas no /admin + DT-29/31/32

- **Data:** 2026-11-25
- **Ciclo:** 1
- **Veredito:** APROVADO — **85/100** (`acao: seguir`), sem blockers
- **Portões (Juiz):** Pint ok · PHPStan nível 8 ok · Pest 688/688 · `backup-db.test.sh` 25/25

## Escopo
- **Card "Rotinas"** no `/admin` (fecha DT-28): backup do banco, cópia das sanções da CGU, varredura de sanções da carteira e índice da busca de pessoas — última rodada, resultado, "Atrasada" quando o agendamento para em silêncio, "Indisponível" com a base fora. Registro em `maintenance_runs` (`vigilo:rotina` chamado pelo `backup-db.sh` no host; `vigilo:sancoes-cgu` e `vigilo:varrer-sancoes` registram sozinhos); índice de pessoas comparando a assinatura do dump.
- **DT-29:** estrutura societária do perfil em `Empresas\PerfilEstrutura` (lazy).
- **DT-31:** cache curto do "risco ao redor" (só resultado completo).
- **DT-32:** `pessoas-popular.sh` confere o disco antes de montar.

## Achados e resolução
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| A1 | média | Testes não cobriam o registro "falhou" do disjuntor nem o "não registra" do `--arquivo` | ✅ Asserções nos dois testes |
| A2 | baixa | Rodada manual com `--fonte` virava a rodada da rotina (mascarava falha de outra fonte) | ✅ Só registra a rodada completa (sem `--fonte` nem `--arquivo`) |
| A3 | baixa | Regra de "com atenção" no Blade | ✅ `RotinaStatus::precisaAtencao()` |
| A4 | baixa | `KINDS_SIMPLES` e `--started` sem uso | ✅ Removidos |
| A5 | baixa | Horários em UTC ao lado de periodicidade em Brasília | ✅ Datas do card (e do card da manutenção) em `America/Sao_Paulo` |
| A6 | baixa | Falha do registro do backup sem rastro | ✅ `backup-db.sh` loga "registro da rodada no app falhou" (o backup segue) |
