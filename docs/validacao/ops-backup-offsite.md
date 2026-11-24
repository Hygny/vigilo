# Validação — ops · backup do MySQL com cópia fora do VPS + 1ª checagem ao adicionar empresa

- **Data:** 2026-11-24
- **Ciclo:** 1
- **Veredito:** APROVADO COM RESSALVAS — **77/100** (`acao: seguir`), sem blockers
- **Portões (Juiz):** Pint ok · PHPStan nível 8 ok · Pest 592/592 · `backup-db.test.sh` 18/18 · `reimport-mensal-remote.test.sh` 13/13

## Escopo
- **1ª checagem automática:** `AddCompanyToPortfolio` (portfólio e perfil) e `CompanyImporter` (CSV) enfileiram `RefreshMonitoredCompanyJob` para cada empresa criada (baseline; alerta só se já entra negativa/na Lista Suja). Mensagens avisam da fila.
- **Backup:** `deploy/backup-db.sh` (VPS: dump `--single-transaction` validado, troca atômica, cópia na VM com sha256 conferido, rotação 30 + 12); `cnpj-db/bin/guardar-backup.sh` + verbo `guardar-backup` no `gateway.sh` (VM: valida, atômico, rotação 90 + 24); `cnpj-db/bin/rotacionar-backups.sh`; `provisionar-vm.sh` cria `/data/backups/vigilo`; restauração em `docs/deploy.md`.

## Achados e resolução
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| A1 | alta (segurança) | Verbo `guardar-backup` aceitava data impossível/futura e sobrescrevia backup bom — com a chave do VPS dava para empurrar os backups reais para fora da rotação e apagar o histórico da VM | ✅ Corrigido: só data real e não futura (folga de 1 dia), teto de 512 MiB, **nunca sobrescreve** (idêntico = no-op com sha; diferente = saída 6). O VPS não re-tenta recusa definitiva (2/3/6). Testes: datas inválidas/futuras, sobrescrita recusada, reenvio idempotente, teto de tamanho |
| A2 | média (testes) | Conferência do sha256 sem teste negativo (mutação sobrevivia) | ✅ Corrigido: caso com a "VM" devolvendo sha errado → rc 1, 3 tentativas, "não confere" |
| A3 | média (deploy) | Scripts novos executados direto; `core.fileMode=false` gravaria 100644 → exit 126 no Linux | ✅ Corrigido: commitados com `git add --chmod=+x` |
| A4 | baixa (aceite) | Mensagem do import CSV não avisava da fila | ✅ Corrigido + asserção (`assertSee`/`assertDontSee`) |
| A5 | baixa (testes) | Asserções frouxas (`-le 33`, mensagens, `assertNothingPushed` no Perfil) | ✅ Corrigido: contagem exata (31), mensagens conferidas, caminhos tristes do Perfil sem job, caminho feliz com contagem 1 e `!rebaseline` |
| A6 | baixa (performance, suspeita) | Dispatch por linha no import síncrono de até 10k linhas | ➖ Mantido: o import só cria até as vagas do plano (Business = 2000), então são ≤ 2000 dispatches — mesmo padrão do "Atualizar todas". Registrado como **DT-27** caso os tetos de plano subam |
| A7 | baixa (doc) | Restauração não documentada | ✅ Corrigido: seção "Backup do banco (MySQL) e restauração" em `docs/deploy.md` |

Observações do Juiz sem desconto: falha do backup não aparece no `/admin` e `backup-db.log` não tem rotação → **DT-28**. Backups antigos com 664 → ajustados para 600 no deploy.

Portões após as correções: Pint ok · PHPStan 0 · Pest 592/592 · `backup-db.test.sh` 24/24 · `reimport-mensal-remote.test.sh` 13/13.
