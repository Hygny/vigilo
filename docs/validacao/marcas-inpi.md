# Validação — Fontes novas (H): marcas do INPI

- **Data:** 2026-11-25
- **Ciclo:** 1
- **Veredito:** APROVADO COM RESSALVAS — **79/100** (`acao: seguir`), sem blockers.
- **Portões (Juiz):**
  - Pint ok
  - PHPStan nível 8 ok
  - Pest 792/792; 808/808 após as ressalvas
  - `reimport-mensal-remote.test.sh` 17/17
  - sem migration

## Escopo
- **Pesquisa:** DataJud fica de fora: API sobrecarregada e sem busca por CNPJ. O INPI traz o CNPJ do titular, então entra. Os detalhes e os preços das fontes pagas estão em `docs/fontes-de-dados.md`.
- **Carga na VM:** `deploy/cnpj-db/bin/inpi-marcas.sh`, chamado pelo verbo `inpi` do gateway.
  - Lê os dois CSVs em streaming e guarda só titulares PJ, com troca atômica e sanidade.
  - Rodou em produção: 4.760.638 marcas em 2 min 21 s.
- **Agenda:** passo semanal `run_inpi` no `deploy/reimport-mensal.sh`, com registro no card Rotinas via `vigilo:rotina marcas_inpi`.
- **App:**
  - `MarcasLookup` e `Marca::estadoDe()` classificam cada marca em em vigor, pedido ou inativa. O vocabulário foi conferido contra as cerca de 130 descrições reais.
  - Seção "Marcas registradas (INPI)" em "Bens e recebíveis".
  - Tipo `marca` no monitoramento de devedora; a concessão de um pedido também gera alerta.
  - Linha "Marcas do INPI (cópia)" no card Rotinas.

## Ressalvas e resolução (todas corrigidas antes do commit)
| ID | Severidade | Achado | Resolução |
|---|---|---|---|
| A1 | média | Extintas não eram contadas quando a empresa tinha marca ativa | ✅ "+ N extinta(s), arquivada(s) ou indeferida(s), não listadas", com teste |
| A2 | média | Erro de SQL saía com 3 (código do `ON_ERROR_STOP` do psql) e o card mostrava "VM sem espaço" | ✅ Toda etapa de SQL ou COPY termina em `\|\| die ... 1`; 3 = só espaço |
| A3 | média | "indeferido (mantido em grau de recurso)" e outros estavam como pedido | ✅ Classificação revista contra o `SELECT DISTINCT situacao` real (arquivado de ofício, inviável, para extinguir, com caducidade → inativa; liminar, prorrogação extraordinária, registro contestado → em vigor), com dataset de 24 casos |
| A4 | média | Caminhos sem teste | ✅ Testes de cópia nunca carregada, teto, "Mostrando 10 de 12", contagem no card da devedora e cópia manual antiga |
| A5 | baixa | Sobras de rodada morta só eram apagadas depois do `df` | ✅ DROP antes da checagem de espaço |
| A6 | baixa | `inpi_cargas` podia nascer com dono `cnpj` (ilegível para o app) | ✅ `ALTER OWNER vigilo_app` logo após o `CREATE TABLE IF NOT EXISTS` |
| A7 | baixa | Concessão de pedido já conhecido não alertava | ✅ Estado na chave (`numero:registro` / `numero:pedido`), com teste |
| A8 | baixa | Card "ok" sem rodada registrada, qualquer que fosse a idade da cópia | ✅ Sem rodada, vale a idade da cópia (> 8 dias = atrasada), com teste |
| A9 | baixa | Em falha, a mensagem era a última linha de progresso | ✅ Em falha, a linha `!!`/`ERROR`/`psql`/`curl`, com teste |
| A10 | baixa | Comentários e documentação desatualizados | ✅ Verbos no cabeçalho, docblock do `RegistrarRotina`, seção INPI no `vigilo-cnpj-oracle-migracao.md`, `docs/fontes-de-dados.md` (decisão do DataJud) |
| A11 | baixa | Fatiamento e contagem feitos no Blade | ✅ `marcasExibidas()`, `totalMarcasAtivas()` e `totalMarcasInativas()` no `BensReport` |
