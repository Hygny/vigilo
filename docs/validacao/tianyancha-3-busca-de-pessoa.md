# Validação — Roteiro Tianyancha 3/4 · Busca e página de pessoa

- **Data:** 2026-11-25
- **Ciclos:** 2
- **Veredito final:** APROVADO — **85/100** (`acao: seguir`), sem blockers
- **Portões (Juiz, ciclo 2):** Pint ok · PHPStan nível 8 ok · Pest 653/653 (656/656 após as ressalvas) · `tests/deploy/cnpj-db-sql.test.sh` verde na VM

## Escopo
- **`/pessoas`** (`Pessoas\Busca`): sócios PF/estrangeiros por nome e sobrenome; cada resultado = pessoa PROVÁVEL (nome + CPF mascarado) com nº de empresas; idêntico e "começa com" primeiro; recusa CPF.
- **`/pessoas/{chave}`** (`Pessoas\Perfil`): chave opaca (uuid do md5 de documento|nome — nunca nome/CPF na URL); empresas (mesmo CPF mascarado E mesmo nome), homônimos (mesmo nome, outro CPF), aviso de vínculo provável.
- **LGPD:** termo fora da URL; trilha `pessoa_consultas` (busca e página) com poda por retenção; tetos por usuário; sócio PF do perfil da empresa linka à pessoa.
- **VM:** `busca_pessoa` (18,4 M pessoas, ~4,1 GB com o btree), `sql/pessoas.sql` + `sql/pessoas-montar.sql` + `bin/pessoas-popular.sh` (troca atômica, `--se-mudou` pela assinatura do dump), chamado pelo `reimport.sh`.

## Ciclo 1 — REPROVADO 74
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| A1 | alta | Nome idêntico sumia atrás do teto de candidatos (LIMIT sem ORDER BY) | ✅ Leitura prioritária por btree (`idx_busca_pessoa_nome`, 0,15 ms) antes dos termos + teste |
| A2 | média | Base fora no pgsql virava PDOException (500) | ✅ `App\Support\ConsultaComTimeout` (fonte única) + catch `QueryException\|PDOException` nas três telas (inclui `Empresas\Busca`, mesmo defeito) + teste com pgsql inalcançável |
| A3 | média | `pessoa()` sem statement_timeout | ✅ Dentro do `ConsultaComTimeout`; 57014 vira mensagem |
| A4 | baixa | Homônimo com o mesmo CPF | ✅ `documento != atual` + teste |
| A5 | baixa | Nome do agendamento | ✅ `vigilo-prune-logs` |
| A6 | baixa | Lacunas de teste | ✅ Exato com teto, 57014, abas/menu, chave fixada em PHP e no teste SQL da VM |
| A7 | baixa | `comTimeout` duplicado, limite chumbado | ✅ `ConsultaComTimeout`, `BuscaPessoas::MAX_HOMONIMOS` |

Extra de produção: estrangeiro vem com documento `00000000000000` → "sem documento", empresas pelo `nome_norm`.

## Ciclo 2 — APROVADO 85
| ID | Severidade | Ressalva | Resolução |
|---|---|---|---|
| A1 | baixa | Idêntico × "começa com" num OR (BitmapOr na ordem física) | ✅ Consultas separadas (idêntico, depois prefixo) + teste com prefixo acima do teto |
| A2 | baixa | Timeout da leitura por termos descartava os idênticos | ✅ Leitura prioritária fora da transação dos termos; estouro só nos termos devolve os idênticos com `truncado` + teste |
| A3 | baixa | Janela "permission denied" após a troca | ✅ `busca_pessoa_nova` passa a `vigilo_app` ANTES da troca (`pessoas-montar.sql`) |
| A4 | baixa | Nome buscado no `laravel.log` (bindings da QueryException) | ✅ `ConsultaComTimeout::reportarSemDados()` (só SQLSTATE/conexão) + teste |
| A5 | baixa | Asserção das abas fraca | ✅ Regex no `aria-current` da âncora de Pessoas (e não na de Empresas) |
| A6 | baixa | Decisão PF/chave no Blade; `'2'/'3'` soltos | ✅ `Socio::chavePessoa()`; constantes `SOCIO_PF`/`SOCIO_ESTRANGEIRO` |

Observação do Juiz sem desconto: o `reimport.sh` remonta ~4,1 GB sem checar disco depois do upsert → **DT-32**.
