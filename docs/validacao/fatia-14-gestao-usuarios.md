# Fatia 14 — Gestão de usuários & papéis (admin por organização)

> Área de admin para incluir/excluir usuários e alterar papéis (Admin/Usuário), escopada por organização.

- **Data:** 2026-09-03
- **Ciclo:** 2 (ciclo 1 REPROVADO por portão Pint vermelho no `bootstrap/app.php`; corrigido com autofix)
- **Veredito:** ✅ **APROVADO — 98.8/100** (`acao: seguir`)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **166/166** (484 asserts)

## Resumo

Papéis **Admin/Usuário** via coluna `role` (enum `App\Enums\Role`, cast no `User`, `isAdmin()`). O dono da organização (quem cadastra) vira **Admin** no `register` e no seeder; demais usuários default `user`. Área **`/usuarios` só para admin** — middleware `admin` (`EnsureUserIsAdmin`, alias em `bootstrap/app.php`) na rota + `abort_unless($user->isAdmin(), 403)` no `mount()`, e o item de nav "Usuários" só aparece para admins. Componente `App\Livewire\Admin\Users`: criar usuário (nome/e-mail/senha/papel, validação com `unique`, `Password::defaults()`, `Rule::enum(Role)`), trocar papel (`setRole`) e excluir (`deleteUser`) — **cada método público re-checa `isAdmin()`** e opera só sobre usuários da **própria organização** (`orgUsers()->findOrFail`). Guardas: não excluir a própria conta; nunca deixar a org sem administrador. A tela traz a **legenda de permissões** (Admin vs Usuário). Decisões do usuário: admin por organização · papéis simples · senha definida na hora.

## Segurança (o risco central, validado adversarialmente)

- Sem vazamento cross-org: `setRole`/`deleteUser` usam `orgUsers()->findOrFail` (404 fora da org).
- Sem escalonamento por input comum: `role` só é definido no register (hardcoded `Role::Admin`) e no `createUser` (validado + admin-only); o perfil (`update-profile-information-form`) só altera nome/e-mail, nunca `role`.

## Achados (não-bloqueantes)

- **A1** (média) — `role` entrou no `#[Fillable]` do `User`: seguro hoje pelas call sites; risco latente da família do **DT-2** (mass-assignment em fluxo futuro de convite via `$request->all()`). Anexado ao DT-2.
- **A2** (cosmético) — campo senha `type="text"` no form de criação: **intencional** (o admin lê a senha para repassar na hora). Aceito.

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25,0 |
| Corretude & regra de negócio | 20 | 5 | 20,0 |
| Segurança & autorização | 15 | 5 | 15,0 |
| Arquitetura & responsabilidades | 12 | 4,5 | 10,8 |
| Banco & performance | 12 | 5 | 12,0 |
| Testes | 10 | 5 | 10,0 |
| Legibilidade & manutenção | 6 | 5 | 6,0 |
| **Total** | **100** | | **98,8** |
