# Fatia — Observabilidade da manutenção mensal (super-admin)

Dá ao super-admin visibilidade da rodada mensal da base CNPJ: **quando** foi o
último reimport e **se houve erro** — sem precisar ler log no servidor.

## O que mudou

- **Tabela `maintenance_runs`** (banco do app): 1 linha por rodada — `status`
  (success/failed), status de cada passo (`reimport`/`recoleta`/`normalizar` =
  ok/fail/skip), `message`, `started_at`, `finished_at`.
- **Comando `vigilo:maintenance-record`** grava a linha (status geral = failed
  se qualquer passo for `fail`). Chamado pelo `reimport-mensal.sh` ao final **e
  também quando o reimport aborta** — então todo mês vira uma linha, com erro ou
  sucesso.
- **Card no topo do `/admin`** (só super-admin, via `Admin\Organizations\Index`):
  badge de status, data do último reimport (+ "há X"), os 3 passos com ✓/✗,
  a mensagem de erro se houve, e um histórico das últimas execuções.
- **Alerta de atraso:** se a última execução (qualquer status) for de **mais de
  35 dias** atrás, o card fica **âmbar "Atrasado"** com aviso. Pega o pior caso
  — o cron parar em silêncio (que não gera erro, só deixa de rodar). Uma falha
  recente aparece como **"Falhou"** (vermelho), não como atraso.

Arquivos: migration `…_create_maintenance_runs_table`, `app/Models/MaintenanceRun.php`,
`app/Console/Commands/RecordMaintenanceRun.php`, `app/Livewire/Admin/Organizations/Index.php`
(+ view), `deploy/reimport-mensal.sh` (grava o run), testes
`tests/Feature/Console/RecordMaintenanceRunTest.php` e
`tests/Feature/Admin/MaintenanceCardTest.php`.

## Deploy

Precisa de **migration** (`maintenance_runs`) — o `deploy.sh` roda `migrate`.
Mudou Blade (classes novas) → `--assets`. O registro passa a acontecer sozinho
na próxima rodada do cron mensal; enquanto não rodar, o card mostra "nenhuma
execução registrada".

## Portões

Pint ok · PHPStan nível 8 (0 erros) · Pest 318 verdes (6 novos nesta fatia).
