# V2-F2 — Driver `LocalCnpjProvider` (base CNPJ própria)

> Troca a fonte de dados da BrasilAPI para a base PostgreSQL local (V2-F1), **sem mudar** o pipeline snapshot/diff/alerta. Sem rate limit → habilita re-coleta em lote (V2-F3) e o grafo (V2-F4+).

- **Data:** 2026-09-21
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 94/100** (`acao: seguir`, sem blockers)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **232/232** (A2 incluído)

## O que entrou

- **`App\Providers\Cnpj\LocalCnpjProvider`** (`implements CnpjDataProvider`): `fetch($cnpj)` divide o CNPJ em `cnpj_basico`(8)+`cnpj_ordem`(4)+`cnpj_dv`(2), busca em `estabelecimentos` pela PK com `leftJoin` em `empresas`/`municipios`/`naturezas_juridicas`, e os sócios em `socios` com `leftJoin` em `qualificacoes_socios`. Monta **o mesmo `CompanyData`** de hoje (2 queries por fetch, tudo em PK/índice).
- **Tradução de código → texto** (essencial para o `CompanyDiffer` continuar detectando situação negativa):
  - situação `01/02/03/04/08` → `NULA/ATIVA/SUSPENSA/INAPTA/BAIXADA` (código fora do mapa → `''`, que o baseline trata como não-negativo);
  - porte `01/03/05` → `ME/EPP/DEMAIS` (`00`/desconhecido → `null`);
  - município / natureza jurídica / qualificação do sócio resolvidos por join, **com fallback ao código** quando não há linha de referência.
- **Contrato preservado**: não encontrado → `null` (semântica do 404); CNPJ inválido → `InvalidCnpjException` (via `Cnpj::fromString`); sem rate limit.
- **Driver plugável**: `config/cnpj.php` ganhou `local`; `AppServiceProvider` resolve `LocalCnpjProvider` quando `CNPJ_DRIVER=local`. **Default segue `brasilapi`** — nada muda em produção até você trocar o env. `.env.example` documenta.

## Testes
`LocalCnpjProviderTest` (conexão `cnpj` apontada a sqlite, tabelas do dump recriadas): monta o `CompanyData` traduzindo códigos + resolvendo referências + sócios; **degrada com graça** em códigos fora do mapa e referências ausentes (porte `00`→null, situação desconhecida→'', natureza/qualificação→fallback); não encontrado → null; inválido → throw; e o container resolve o driver `local`.

## Achados
- **A1 (alta, go-live):** os joins dependem de `municipios`/`naturezas_juridicas`/`qualificacoes_socios` — um `leftJoin` em tabela/coluna inexistente é **erro fatal** no Postgres (o fallback só cobre linha ausente). As três tabelas foram **confirmadas via `\dt`** e `\d municipios` confirmou `codigo`/`descricao`. **Check antes de `CNPJ_DRIVER=local`:** conferir `\d naturezas_juridicas` e `\d qualificacoes_socios` (coluna `descricao`).
- **A2 (média):** ramos de borda sem teste → **corrigido** (teste de degradação adicionado).
- **A3 (baixa):** `logradouro`/`porte`/`natureza` viram strings próprias (ex.: `logradouro` prefixa `RUA/AV`), então a **1ª coleta** após trocar a fonte pode gerar `AddressChanged/PorteChanged` falsos. A **V2-F3** (re-coleta) absorve essa transição — anotado para lá.

## Deploy / ativação
Esta fatia **não muda produção** (default `brasilapi`). Para ativar a base local, depois do go-live check do A1: `CNPJ_DRIVER=local` no `src/.env` → `config:cache` → restart. A BrasilAPI segue como fallback (basta reverter o env).

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25 |
| Corretude & regra de negócio | 20 | 4 | 16 |
| Segurança & autorização | 15 | 5 | 15 |
| Arquitetura & responsabilidades | 12 | 5 | 12 |
| Banco & performance | 12 | 5 | 12 |
| Testes | 10 | 4→5 | 10 (A2 corrigido) |
| Legibilidade & manutenção | 6 | 5 | 6 |
| **Total** | **100** | | **94 → 96** (com A2 corrigido) |

## Próximo
**V2-F3** — re-coleta da carteira após cada dump (re-snapshot + diff em lote → alertas), absorvendo a transição de fonte (A3), + cron mensal encadeando reimport → re-coleta.
