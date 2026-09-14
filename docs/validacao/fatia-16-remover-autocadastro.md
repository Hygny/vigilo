# Fatia 16 — Remover autocadastro público (/register)

> Fechamento de vetor de segurança: qualquer pessoa podia acessar `/register`, criar uma organização nova e virar **admin** dela.

- **Data:** 2026-09-13
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 100/100** (`acao: seguir`, sem blockers)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **164/164** (474 asserts)

## O que mudou e por quê

O Vigilo é multi-tenant B2B. O `/register` (Breeze) criava uma organização e atribuía `role = Role::Admin` ao autor — ou seja, autocadastro público = qualquer um vira admin de um tenant novo. Removido:

- **`routes/auth.php`** — rota `register` retirada do grupo `guest`, com comentário explicando a decisão.
- **`resources/views/livewire/pages/auth/register.blade.php`** — componente Volt deletado (dead code após a rota sair).
- **`tests/Feature/Auth/RegistrationTest.php`** — reescrito: asserta que `Route::has('register')` é `false` e que `GET /register` responde 404.
- **`docs/debito-tecnico.md`** — DT-2 atualizado (um call-site de mass-assignment de `role` a menos).

## Onboarding depois da remoção

Nenhum caminho de bootstrap depende do `/register`:

- **Organização nova / primeiro admin:** seeder (`DatabaseSeeder` cria org + admin) ou tinker no VPS.
- **Novos usuários numa org existente:** tela **Usuários** (`/usuarios`, admin-only) — o admin define nome, e-mail, papel e senha, com a org fixada na dele.

Para B2B pré-venda isso é o esperado: clientes são onboardados manualmente, não por autocadastro aberto.

## Achados

- Nenhum bloqueador; nenhum achado com dedução.
- **O-1** (observação, sem impacto) — `resources/views/livewire/welcome/navigation.blade.php` (scaffolding Breeze da `welcome`, que **não está roteada** — a raiz redireciona pra `/login`) ainda tem um `route('register')` guardado por `@if (Route::has('register'))`. Já era inalcançável e degrada graciosamente. Removível numa faxina futura de scaffolding não usado; não é dívida desta fatia.

## Nota

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
