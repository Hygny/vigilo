# Fatia 20 — Planos (Free/Pro/Business) e quota de CNPJs monitorados

> 4ª feature da Prioridade 1 (parte 1/2). Camada de planos + trava de quota, **sem gateway** — a integração de pagamento (Asaas) é a fatia 21.

- **Data:** 2026-09-14
- **Ciclo:** 2 (ciclo 1 APROVADO 94/100 com 3 achados; A2/A3 corrigidos, A1→DT-11)
- **Veredito:** ✅ **APROVADO — 100/100** (`acao: seguir`, sem blockers)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **198/198** (564 asserts) · migration reversível

## O que entrega

- **Planos configuráveis:** `App\Enums\Plan` (Free/Pro/Business) com `label()`, `maxCompanies()`, `monthlyPrice()` lendo `config/plans.php` — limites e preços ajustáveis por env (calibragem de negócio sem tocar código). Padrões iniciais: Free 20 CNPJs (R$ 0) · Pro 200 (R$ 149) · Business 2000 (R$ 499).
- **Plano na organização:** `organizations.plan` (default `free`, **fora do `$fillable`**, default em memória p/ nunca ser null, cast `Plan`). `Organization::maxMonitoredCompanies()`, `monitoredCompaniesCount()` (org-wide, somando todos os portfólios e **ignorando o OrganizationScope**) e `remainingCompanySlots()`.
- **Trava de quota (enforcement):**
  - `Portfolios\Show::addCompany` bloqueia ao atingir o teto (erro no campo `cnpj`, não cria).
  - `Portfolios\Show::import` passa as vagas restantes ao `CompanyImporter::importFromFile(..., $limit)`, que importa até o teto e rejeita o excedente com `REASON_PLAN_LIMIT` (import parcial + aviso no flash).
  - Barra de uso "Plano X · usado/máx CNPJs" na tela do portfólio.
- **Gestão pelo super-admin:** `Admin\Organizations\Show::setPlan` (re-check `isSuperAdmin`, `Plan::tryFrom`, `forceFill`) + card de plano com seletor e aviso quando a org já excede o novo teto; badge de plano na listagem; "usado/limite" no detalhe.

## Cobertura da quota (validada adversarialmente)

Os **únicos** dois pontos que criam `MonitoredCompany` (`Portfolios\Show::addCompany` e `CompanyImporter`) estão ambos atrás da trava. Refresh/snapshot não criam empresa; o onboard do super-admin cria org+admin (não empresas). A contagem é org-wide, sem cross-tenant nem subcontagem.

## Achados

- **A1** (corretude, baixa) — quota é **check-then-create não-atômico** (TOCTOU): sob concorrência do mesmo tenant pode estourar o teto por ~1. Limite de negócio, não segurança. Registrado como **DT-11** (resolver na fatia de cobrança, com `DB::transaction` + lock).
- **A2** (testes, baixa) — faltava teste negativo de autorização do `setPlan`. **Resolvido** (gate in-body coberto).
- **A3** (legibilidade, baixa) — `@error('plan')` morto. **Resolvido** (removido).

## Nota (ciclo 2)

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25,0 |
| Corretude & regra de negócio | 20 | 5 | 20,0 |
| Segurança & autorização | 15 | 5 | 15,0 |
| Arquitetura & responsabilidades | 12 | 5 | 12,0 |
| Banco & performance | 12 | 5 | 12,0 |
| Testes | 10 | 5 | 10,0 |
| Legibilidade & manutenção | 6 | 5 | 6,0 |
| **Total** | **100** | | **100** |
