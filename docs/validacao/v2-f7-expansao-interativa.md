# V2-F7 — Expansão interativa do grafo societário

> Torna o grafo navegável: clicar num nó (empresa do grupo ou sócio PJ) recentra o grafo naquele CNPJ — investigação no estilo Tianyancha, partindo sempre de uma empresa da carteira.

- **Data:** 2026-09-21
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 98/100** (`acao: seguir`, sem blockers; A1 corrigido)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **255/255** (772 asserts)

## O que entrou

- **Recentrar ao clicar** (`App\Livewire\Companies\Graph`): property `public string $focus` (CNPJ focado; vazio = a empresa monitorada); ação `focus($cnpj)` valida 14 dígitos (inválido é ignorado) e re-renderiza o grafo centrado nele; `resetFocus()` volta ao início. Breadcrumb "Focado em …" + botão **"Voltar à empresa monitorada"**.
- **Clicáveis só nós com CNPJ completo**: empresa do grupo econômico e sócio PJ. PF (CPF mascarado) e o próprio centro não recentram. Como a aresta reversa só traz o `cnpj_basico`, o serviço agora entrega o **CNPJ da matriz** do nó de grupo via o novo helper.
- **`App\Support\Cnpj::matrizFromBasico(string $basico): ?string`** — monta o CNPJ da matriz (ordem `0001` + DV mod-11 calculado, determinístico); null se o básico não tiver 8 dígitos.
- **Tela**: nós clicáveis (`wire:click="focus(...)"` + cursor), tooltip "clique para expandir", dica de interação, breadcrumb/voltar. A tela continua degradando ("Grafo indisponível") mesmo com foco ativo (o "voltar" fica fora do bloco de indisponível — o usuário nunca fica preso).

## Segurança (verificação adversarial)
- **Injeção no `wire:click`**: o valor injetado (`{!! !!}`) é **só dígitos** — `$node['cnpj']` só é preenchido quando `preg_match('/^\d{14}$/', document)` no componente. Impossível quebrar o atributo. Defesa em profundidade: revalidado na fronteira mesmo vindo de `matrizFromBasico`/`cnpj_cpf_do_socio`.
- **Escopo/tenant**: `focus()` só alimenta o `OwnershipGraphService` (conexão `cnpj`, base **pública** da Receita); nenhuma tabela do tenant é tocada; a página exige `authorize('view')` da empresa monitorada de origem. Recentrar em empresa conectada é a feature de investigação, não vazamento.

## Testes
- `CnpjTest`: `matrizFromBasico('11222333')` → `11222333000181` (CNPJ conhecido válido); null para tamanho errado.
- `CompanyGraphTest`: recentra ao focar outra empresa (novo centro + seus sócios) e volta com `resetFocus`; ignora CNPJ inválido; **markup** confirma que a empresa do grupo é clicável (`wire:click="focus('…')"`) e a PF (CPF mascarado) **não** é (A1).

## Deploy
Markup novo → **`./src/deploy/deploy.sh --assets`**. Sem migration.

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25 |
| Corretude & regra de negócio | 20 | 5 | 20 |
| Segurança & autorização | 15 | 5 | 15 |
| Arquitetura & responsabilidades | 12 | 5 | 12 |
| Banco & performance | 12 | 5 | 12 |
| Testes | 10 | 4→5 | 10 (A1 corrigido) |
| Legibilidade & manutenção | 6 | 5 | 6 |
| **Total** | **100** | | **98 → 100** (com A1 corrigido) |

## Estado do grafo
A V2 do plano está completa (F1→F6) e o grafo agora é **navegável** (F7). Enriquecimento futuro opcional: **% de participação** (fonte paga por CNPJ) para o beneficiário final no critério legal ≥25%.
