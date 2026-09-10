# Fatia 15 — Correções de UX: tema na navegação SPA + fim do flash de ícones

> Bug do tema escuro que se perdia ao navegar (wire:navigate) e do texto-fonte das ligaturas (Material Symbols) aparecendo antes da fonte carregar. Inclui a investigação de mais 2 pontos reportados que **não** viraram código.

- **Data:** 2026-09-09
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 94/100** (`acao: seguir`, sem blockers)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **166/166** (484 asserts)

## Os 3 pontos reportados

### 1. "A API é sempre atualizada? 700 CNPJs não geraram nenhum alerta." — comportamento correto (validado)

- **Fonte de dados:** BrasilAPI (`config/cnpj.php` → driver `brasilapi`), que espelha o **dump aberto da Receita Federal** — atualizado ~mensalmente, **não em tempo real**. Uma mudança no registro pode levar semanas até aparecer.
- **Por que zero alertas é esperado:** o `CompanyDiffer::diff` compara com o **snapshot anterior**; quando `previous === null` (primeira coleta) retorna `[]` — a 1ª coleta é o *baseline*, sem alerta. Coletas seguintes só alertam quando a BrasilAPI retorna algo **diferente** do snapshot guardado — o que não acontece até a Receita publicar um novo dump com mudança para aquele CNPJ.
- **Prova do pipeline (rodada ao vivo):** `tests/Feature/Jobs/RefreshMonitoredCompanyJobTest.php` — cenário `ATIVA → BAIXADA` gera 1 `ChangeEvent` crítico + notifica a organização; e "primeira coleta = baseline sem evento". `tests/Unit/Services/CompanyDifferTest.php` cobre cada campo. **32 testes de diff/job/notificação verdes.** Conclusão: sem alerta = nada mudou na fonte desde o baseline, não é falha de detecção.

### 2. "Mesmo limpando os cookies o site não desloga." — não é bug de servidor (validado)

- Verificado no navegador: `GET /dashboard` **sem cookies** responde **302 → /login**; **com cookies** responde 200. A página autenticada já vem com `Cache-Control: no-store, no-cache, private` (bfcache/cache descartados).
- Causa: o cookie **"Lembrar-me"** (`remember_web_*`, HttpOnly, validade de ~5 anos). Ao logar com "Lembrar-me" marcado, o Laravel grava `remember_token` (60 chars) no usuário e emite esse cookie separado da sessão. Limpar só o cookie de sessão deixa o `remember_web`, que reautentica silenciosamente. Comportamento padrão do framework. Para deslogar de fato: botão **"Sair"** (que limpa tudo) ou limpar **todos** os cookies do site.

### 3. "No primeiro login os ícones aparecem como nome (ex.: folder_open)." — corrigido

- **Causa:** os ícones Material Symbols renderizam por **ligatura** (o texto `folder_open` vira o ícone). Antes da fonte carregar, o texto cru aparecia (FOUT). O link da fonte não tinha `font-display`.
- **Correção:** `&display=block` na URL do Google Fonts (a fonte fica invisível durante o carregamento, sem mostrar o texto) + `.msym` clampado à caixa quadrada de `width:1em` com `overflow:hidden` (clipa o texto largo caso apareça) e `flex:none` (impede o ícone ser encolhido/cortado em containers flex).
- **Prova:** com a fonte indisponível, o texto "folder_open" (106px) fica contido na caixa de 20px (1em) com overflow oculto — sem flash largo nem deslocamento de layout. Com a fonte carregada, todos os ícones renderizam em 1em, `scrollW == clientW` (nada cortado).

## Escopo do diff

Base `d8a918d`. Arquivos: `resources/css/app.css`, `resources/views/layouts/app.blade.php`, `resources/views/layouts/guest.blade.php`, `resources/views/livewire/layout/navigation.blade.php` (fix de tema, commit `571897e`, + fix de ícone).

## Achados (não-bloqueantes)

- **A1** (baixa) — script de bootstrap de tema duplicado *verbatim* entre `app.blade.php` e `guest.blade.php`. Anexado ao **DT-8**.
- **A2** (baixa) — sem cobertura automatizada da behavior de UI (stack sem camada JS/Dusk). Dívida de setup. Anexado ao **DT-9**.
- **A3** (baixa/cosmético) — no timeout do `font-display:block` (falha de rede >3s), o `text-align:center` exibiria o miolo clipado da ligatura. Aceito (degradação graciosa de caso extremo).

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25,0 |
| Corretude & regra de negócio | 20 | 5 | 20,0 |
| Segurança & autorização | 15 | 5 | 15,0 |
| Arquitetura & responsabilidades | 12 | 4 | 9,6 |
| Banco & performance | 12 | 5 | 12,0 |
| Testes | 10 | 3 | 6,0 |
| Legibilidade & manutenção | 6 | 5 | 6,0 |
| **Total** | **100** | | **94** |
