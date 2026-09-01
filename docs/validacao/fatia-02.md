# Fatia 2 — Camada de domínio (Cnpj, DTOs, provider, SnapshotService)

- **Data:** 2026-08-14 (validação retroativa)
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 99/100** (`acao: seguir`)
- **Portões:** Pint ok · PHPStan nível 8 ok · Pest ok · Migrations reversíveis

## Resumo
`Cnpj` (VO imutável: normaliza/left-pad 14, valida mod-11, rejeita repetidos), DTOs `CompanyData`/`PartnerData` readonly, contrato `CnpjDataProvider` + `BrasilApiProvider` (timeout, retry, 429/404→null, throttle, driver via `config/cnpj.php`, bind no container), `SnapshotService` atômico (`DB::transaction`). Testes de Cnpj, provider (`Http::fake`) e SnapshotService.

## Achados
- **A1** (baixa, legibilidade) — o comentário do `config/cnpj.php` dizia "backoff cresce por tentativa", mas o `int` gerava delay fixo. **Resolvido**: o `retry()` passou a usar closure `fn ($attempt) => $attempt * base` (backoff crescente de verdade), tornando o comentário verdadeiro. Testes do provider seguem verdes.
- **A2** (observação, sem desconto) — `cnaePrincipal` guarda o código (`cnae_fiscal`), não a descrição — escolha de design válida (identificador canônico p/ diff); `raw_json` preserva o payload.
