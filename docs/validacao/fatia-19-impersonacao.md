# Fatia 19 — Impersonação de usuários pelo super-admin (com auditoria)

> 3ª feature da Prioridade 1 (parte 2/2). O super-admin "loga como" um usuário para suporte/depuração, com trilha de auditoria e retorno seguro.

- **Data:** 2026-09-14
- **Ciclo:** 2 (ciclo 1 APROVADO 96/100 com 2 achados de teste; hardening aplicado)
- **Veredito:** ✅ **APROVADO — 100/100** (`acao: seguir`, sem blockers)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **191/191** (545 asserts) · migration reversível

## O que entrega

- **Iniciar (`Admin\Organizations\Show::impersonate`):** re-checa `isSuperAdmin()` no corpo (o `/livewire/update` não reaplica o middleware da rota — este é o gate real), resolve o alvo por `findOrFail` na relação da org (isolamento cross-org), **bloqueia personificar outro super-admin ou a si mesmo**, grava `ImpersonationLog`, guarda `impersonator_id` na sessão e troca a identidade (`Auth::login`), com redirect completo para `/dashboard`.
- **Auditoria (`impersonation_logs`):** ids + **e-mails denormalizados** + organization_id + created_at. Migration **sem FK** de propósito — o log sobrevive à exclusão dos usuários.
- **Retorno:** banner âmbar no `layouts.app` quando `session('impersonator_id')` está setado ("Você está personificando X · Voltar ao admin"); o botão faz POST em `impersonate.stop` (`StopImpersonationController`), que restaura o super-admin e volta ao `/admin`.
- **Suspensão:** `EnsureOrganizationActive` isenta a sessão de personificação (o operador precisa inspecionar orgs suspensas); usuários normais continuam sendo cortados.

## Segurança (o risco central, validado adversarialmente)

- **Gate real no endpoint Livewire:** `abort_unless(isSuperAdmin(), 403)` no corpo de `impersonate` (não só no `mount`). Coberto por teste que remove o privilégio após o mount.
- **Sem escalonamento:** `impersonator_id` só é gravado por `Show::impersonate` (super-admin) — `grep` confirma nenhum outro ponto de escrita. O `stop` só faz `Auth::login` do id guardado na própria sessão.
- **Isolamento:** alvo resolvido via `->users()->findOrFail()` da org → `userId` de outra org dá 404 (testado).
- **Auditoria durável:** escrita server-side, e-mails denormalizados, sem FK.

## Achados (ciclo 1 → resolvidos no ciclo 2)

- **A1** (testes, média) — gate in-body de `impersonate` sem teste. **Resolvido:** teste do gate pós-mount (`assertForbidden` + identidade inalterada + 0 logs) e do gate do mount.
- **A2** (testes, baixa) — isolamento cross-org sem asserção. **Resolvido:** teste cross-org espera `ModelNotFoundException` + 0 logs.

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
