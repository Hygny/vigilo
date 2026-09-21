# V2-F3 — Re-coleta da carteira (pós-dump) + modo re-baseline

> Transforma o dump mensal em monitoramento contínuo real: após cada carga, re-coleta toda a carteira em lote (sem rate limit) → snapshot/diff/alerta. E absorve a transição BrasilAPI→local (A3 da V2-F2) com um modo silencioso.

- **Data:** 2026-09-21
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 100/100** (`acao: seguir`, sem blockers, sem achados)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **236/236** (701 asserts)

## O que entrou

- **Comando `vigilo:recoletar-carteira`** (`App\Console\Commands\RecollectPortfolios`): re-coleta **toda a carteira, de todas as organizações** (roda no console, sem escopo de org), enfileirando um `RefreshMonitoredCompanyJob` por empresa em chunks de 300, reusando a dedup `ShouldBeUnique` do job. Informa a contagem e lembra de `CNPJ_THROTTLE_PER_MINUTE=0` (a base local não tem rate limit).
- **Modo `--rebaseline`**: o `RefreshMonitoredCompanyJob` ganhou `public bool $rebaseline = false` (retrocompatível — os 4 dispatchers existentes seguem com 1 argumento). Quando `true`, grava o snapshot **sem** gerar `change_events` nem notificação — usado **uma vez** ao trocar a fonte de dados, para a 1ª coleta local não disparar alertas de mera diferença de formato.
- **Runbook atualizado** (`docs/vigilo-v2-f1-runbook.md`): item 5 (ativar `CNPJ_DRIVER=local` + `--rebaseline` uma vez) e item 6 (cron mensal encadeando `pipeline reimport` → `vigilo:recoletar-carteira` **sem** a flag, para gerar os alertas).

## Testes
- `RecollectPortfoliosTest`: enfileira 1 job por empresa cross-org (3 orgs → 3 jobs); passa a flag corretamente (`--rebaseline` true/false); reporta zero quando não há empresas.
- `RefreshMonitoredCompanyJobTest` (novo caso): em modo rebaseline, ATIVA→BAIXADA **grava o snapshot** mas gera **0 eventos** e **nenhuma notificação** — provando que a supressão é real (o mesmo par gera 1 evento crítico no modo normal).

## Como usar (produção)
1. Transição (uma vez): `CNPJ_DRIVER=local` + `CNPJ_THROTTLE_PER_MINUTE=0` → `config:cache` → `php artisan vigilo:recoletar-carteira --rebaseline`.
2. Mensal (cron): `pipeline (upsert)` → `php artisan vigilo:recoletar-carteira` (sem flag) → alertas do que mudou entre dumps.

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25 |
| Corretude & regra de negócio | 20 | 5 | 20 |
| Segurança & autorização | 15 | 5 | 15 |
| Arquitetura & responsabilidades | 12 | 5 | 12 |
| Banco & performance | 12 | 5 | 12 |
| Testes | 10 | 5 | 10 |
| Legibilidade & manutenção | 6 | 5 | 6 |
| **Total** | **100** | | **100** |

## Próximo
**V2-F4** — grafo societário (Camada 1): consulta bidirecional em `socios` (direto + reverso via `idx_socios_documento`), grupo econômico por sócio em comum, dedup de PF por documento.
