# Fatia 18 — Super-admin e back-office de organizações

> 3ª feature da Prioridade 1 (parte 1/2). Painel `/admin` do dono da plataforma, fora do escopo de tenant. Impersonação ficou para a fatia 19.

- **Data:** 2026-09-14
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 96/100** (`acao: seguir`, sem blockers)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **183/183** (526 asserts) · migration reversível

## O que entrega

- **Super-admin:** coluna `users.is_super_admin` (**fora do `#[Fillable]`** — não mass-assignable; só via `forceFill`/seeder), `User::isSuperAdmin()` null-safe + default em memória. Login manda o super-admin para `/admin` (demais para `/dashboard`).
- **Suspensão de org:** coluna `organizations.suspended_at` (fora do Fillable), `Organization::isSuspended()`. `LoginForm` bloqueia login de org suspensa; `EnsureOrganizationActive` (append no grupo `web`) desloga mid-sessão. Super-admin (sem org) e guest são isentos.
- **Painel `/admin`** (middleware `super-admin` + re-check `isSuperAdmin()` em cada método público de Livewire), layout próprio (`layouts.admin`) e nav dedicada:
  - **Index:** lista todas as organizações com uso (usuários/portfólios/empresas, cross-tenant) e **onboarda org nova + 1º admin** (substitui o `/register` removido na fatia 16).
  - **Show:** detalhe (usuários, portfólios, uso), **suspender/reativar** e **promover/rebaixar** papel em qualquer org (guarda "nunca sem admin").
- **Seeder:** super-admin `super@vigilo.test` / `password` (sem org).

## Correções fora do "super-admin" estrito (aceitas pelo Juiz)

- **Bug latente do `email_verified_at`:** por estar fora do `#[Fillable]`, `User::create([... 'email_verified_at' => now() ...])` o **descartava** — usuários criados (onboard e `Admin\Users` da fatia 14) nasciam não-verificados e ficavam presos no middleware `verified`. Corrigido com `forceFill` nos dois pontos; testes assertam `hasVerifiedEmail()`.
- **DT-8 resolvido:** script de bootstrap de tema extraído para `resources/views/partials/theme-bootstrap.blade.php` (fonte única nos 3 layouts) — evitou triplicar ao criar o layout admin.

## Achados (não-bloqueantes)

- **A1** (corretude, baixa, *suspeita*) — a guarda "nunca deixar a org sem admin" (`Show::setRole`) é **TOCTOU** (lê a contagem e depois grava, sem lock). Dois `setRole('user')` concorrentes sobre 2 admins distintos poderiam zerar os admins. Prob. baixíssima (operador único, Livewire serializa requests do componente). Mesma característica existe no `Admin\Users` (fatia 14). Anexado ao **DT-10**.
- **A2** (segurança/UX, baixa) — senha do onboard em `type="text"` (visível). **Intencional** (o super-admin lê a senha para repassar; senha descartável), consistente com a decisão aceita na fatia 14. Aceito.

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25,0 |
| Corretude & regra de negócio | 20 | 4 | 16,0 |
| Segurança & autorização | 15 | 5 | 15,0 |
| Arquitetura & responsabilidades | 12 | 5 | 12,0 |
| Banco & performance | 12 | 5 | 12,0 |
| Testes | 10 | 5 | 10,0 |
| Legibilidade & manutenção | 6 | 5 | 6,0 |
| **Total** | **100** | | **96** |
