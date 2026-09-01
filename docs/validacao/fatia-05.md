# Fatia 5 — CSV import + multi-tenant + policies + isolamento

- **Data:** 2026-08-14 (validação retroativa)
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 94/100** (`acao: seguir`)
- **Portões:** Pint ok · PHPStan nível 8 ok · Pest ok · Migrations reversíveis

## Resumo
`CompanyImporter` (normaliza/valida/deduplica CNPJ, relatório `ImportReport` de rejeitadas, header + `,`/`;`, streaming) + `ImportCompaniesCsvJob` (fila, notifica `CompaniesImported`, limpa upload). Tenancy via `BelongsToOrganization` + `OrganizationScope` (no-op sem auth) em `Portfolio`; policies (`PortfolioPolicy`, `MonitoredCompanyPolicy` lendo o org bypassando o scope). Teste de isolamento org A ≠ org B.

## Achados
- **A1** (média, banco) — `sniffDelimiter` lia o arquivo inteiro (`file_get_contents`). **Resolvido**: lê só a 1ª linha via `fgets`.
- **A2** (baixa, corretude) — off-by-one na numeração de linha no caminho de array. **Resolvido**: contador inicia em 0; teste-guarda `numbers array rows from 1`.
- **A3** (baixa, corretude) — `create()` não tratava violação de unique sob concorrência. **Resolvido**: `try/catch UniqueConstraintViolationException` → rejeita como duplicado.
- **A4** (baixa, suspeita) — import não transacional (import parcial em falha). **Decisão consciente**: para CSV, importar o válido e reportar o resto é o comportamento desejado (transação rolaria tudo por uma linha ruim).
