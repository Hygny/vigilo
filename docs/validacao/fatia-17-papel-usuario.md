# Fatia 17 — Restringir papel Usuário (ações destrutivas de carteira = admin-only)

> Primeira fatia da Prioridade 1 (pré-venda). Define o que o papel **Usuário** pode fazer no app: operar sim, administrar/excluir não.

- **Data:** 2026-09-14
- **Ciclo:** 2 (ciclo 1 APROVADO 98/100 com 2 achados; autofix aplicado)
- **Veredito:** ✅ **APROVADO — 100/100** (`acao: seguir`, sem blockers)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **169/169** (483 asserts)

## Decisão do produto

O app tem 2 papéis por organização (`App\Enums\Role`: Admin/Usuário). Decisão do usuário: o **Usuário opera** (cria, importa, atualiza, agenda portfólios e empresas; vê e reconhece alertas) mas **não exclui carteira**. Excluir portfólio e remover empresa do monitoramento passam a ser **admin-only**. (Gestão de usuários já era admin-only desde a fatia 14.)

## Implementação (fonte única na policy)

Os componentes Livewire já autorizavam via policy, então a restrição foi concentrada nelas:

- `PortfolioPolicy::delete` e `MonitoredCompanyPolicy::delete` agora exigem `isAdmin()` **além** da posse (`owns`). Como `Portfolios\Index::delete` e `Portfolios\Show::removeCompany` já chamavam `authorize('delete', ...)`, a restrição valeu sem tocar nos componentes.
- Blade: o botão de excluir portfólio (`index`) e o de remover empresa (`show`) só aparecem para quem pode — `@can('delete', $portfolio)` no Index e, no Show, um booleano `$canRemoveCompanies` calculado **uma vez** no `render()` (evita N+1 do `@can` por linha na listagem paginada).
- `create`/`update`/`view` **não** mudaram → o Usuário continua operando normalmente.

## Matriz de permissões (resultante)

| Ação | Admin | Usuário |
|---|:---:|:---:|
| Ver painel/portfólios/empresas/alertas | ✅ | ✅ |
| Criar portfólio · importar CSV · adicionar empresa | ✅ | ✅ |
| Atualizar (refresh) · agendar · reconhecer alertas | ✅ | ✅ |
| **Excluir portfólio** | ✅ | ❌ (403) |
| **Remover empresa do monitoramento** | ✅ | ❌ (403) |
| Gerenciar usuários (`/usuarios`) | ✅ | ❌ |

## Achados (ciclo 1 → resolvidos no ciclo 2)

- **A1** (banco, baixa) — `@can('delete', $company)` rodava por linha (até 20 lookups `owns()` por render, admin). **Resolvido:** permissão calculada 1x em `Show::render` (`$canRemoveCompanies`), Blade com `@if`. Policy segue como fonte da verdade; gate real (`authorize('delete', ...)` em `removeCompany`) intacto.
- **A2** (testes, baixa) — faltava teste de render-hide no Show. **Resolvido:** teste adicionado, espelhando o do Index.

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
