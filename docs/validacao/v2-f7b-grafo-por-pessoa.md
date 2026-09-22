# V2-F7b — Grafo centrado na pessoa (foco em PF)

> Extensão da expansão interativa (F7): clicar numa **pessoa física** (sócio) recentra o grafo nela, mostrando **todas as empresas** em que aparece como sócia — o outro lado da investigação societária.

- **Data:** 2026-09-21
- **Ciclo:** 1
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **257/257** (782 asserts)
- **Validação:** o agente Juiz foi interrompido pelo limite de uso da sessão (duas vezes seguidas); ele alcançou a confirmação de que **não há referência órfã** ao antigo `focus(`. A verificação de segurança e de aceite foi concluída manualmente (abaixo), com os três portões verdes.

## O que entrou

- **`OwnershipGraphService::forPerson($document)`**: a pessoa no centro (nome vindo da base) + todas as empresas onde o documento aparece como sócio (consulta reversa em `socios`), **dedup** por `cnpj_basico` e **cap** `reverse_limit`; cada empresa carrega o CNPJ da matriz (clicável) via `Cnpj::matrizFromBasico`.
- **`Companies\Graph`**: property `$focusDocument` + ação `focusPerson($document)` (sanitiza para `[0-9*]`, ≤14; inválido ignorado). `focusOn` (empresa) e `focusPerson` (pessoa) limpam um ao outro; `resetFocus` limpa ambos. Flag `personMode` na render.
- **Clicabilidade por nó**: empresa/sócio PJ (CNPJ 14 díg) → `focusOn`; PF/estrangeiro (CPF mascarado, dígitos + `*`) → `focusPerson`; o centro não recentra.
- **Tela**: no modo pessoa, o painel "Beneficiários finais" fica **oculto** (conceito de empresa), o `beneficialOwners` não é chamado, e os textos/contadores se ajustam ("empresa(s) desta pessoa").

## Verificação de segurança (manual)
- **Injeção no `wire:click`**: `$node['person']` só é preenchido quando `preg_match('/^[0-9*]+$/', $doc)` e contém `*`; `$node['cnpj']` só com `^\d{14}$`. O valor injetado (`{!! !!}`) é sempre só dígitos (ou dígitos + `*`) → não quebra o atributo/JS. O **nome** (dado da Receita) só aparece via `{{ }}` (escapado) e via `<title>` (escapado), **nunca** em atributo. `focusPerson` revalida no servidor.
- **Alternância de foco**: `focusOn` zera `$focusDocument`; `focusPerson` zera `$focus`; `resetFocus` zera ambos.
- **Degradação**: `forPerson` está dentro do `try` da render → base fora ⇒ card "Grafo indisponível", sem prender o usuário (o "voltar" fica fora do bloco).
- **Rename**: `focus()`→`focusOn()` sem referência órfã (confirmado por grep).

## Ressalva conhecida (limitação da base)
O CPF é **mascarado** na base aberta da Receita (`***NNNNNN**`), então o grafo por pessoa pode, raramente, **fundir** duas pessoas distintas com os mesmos 6 dígitos centrais. Resolvível só com fonte que exponha o CPF completo (dado restrito) — fora de escopo.

## Testes
`CompanyGraphTest`: recentra na pessoa mostrando as empresas dela e volta com `resetFocus`; a PF é clicável no markup (`wire:click="focusPerson('***…')"`) e **não** clicável como empresa; documento inválido é ignorado; o painel de beneficiários fica oculto no modo pessoa.

## Deploy
Markup novo → **`./src/deploy/deploy.sh --assets`**. Sem migration.
