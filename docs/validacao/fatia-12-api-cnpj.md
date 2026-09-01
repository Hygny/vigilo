# Fatia 12 — API pública de leitura de CNPJ (Sanctum)

> Vigilo passa a **expor** `GET /api/cnpj/{cnpj}` para um consumidor externo (o `VigiloClient`).

- **Data:** 2026-08-19
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 98/100** (`acao: seguir`)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` ok (156 testes / 457 asserts)

## Resumo

Endpoint REST `GET /api/cnpj/{cnpj}` com **Bearer (Sanctum)** e **escopo por organização**:
o token herda a org do usuário e a API só devolve CNPJs que essa org monitora
(fora da carteira ou sem coleta → 404 = "pendente"; sem mod-11 no read, só 14 dígitos → 422).
Resposta plana no contrato acordado (`cnpj`, `situacao_cadastral`, `cnae_principal`,
`razao_social`, `historico[{data,campo,de,para}]`), com `historico` das mais recentes
primeiro e teto `CNPJ_API_HISTORY_LIMIT` (50). Rate limit `cnpj-api` (30/min por dono do token).
Token emitido via `php artisan vigilo:api-token {email} --name=`. 8 testes cobrindo aceite
e caminhos tristes (401, 404 não-monitorado, 404 sem-snapshot, 422, cross-tenant, ordenação+teto).
Escopo aplicado explicitamente (`whereHas('portfolio', withoutGlobalScopes()->where(org))`)
porque o `OrganizationScope` global é no-op sob `auth:sanctum`.

## Critério de aceite

Todos os 10 itens **atendidos** (rota+auth+contrato+escopo+erros+histórico+rate limit+comando+qualidade+sem regressão).

## Achados

- **A1** (média, banco/performance, confirmado) — o lookup por `cnpj` isolado não tinha índice
  utilizável (só o composto `unique(portfolio_id, cnpj)`, líder `portfolio_id`), causando
  varredura de tabela por request no hot-path. → **DT-6**. **RESOLVIDO neste ciclo**: migration
  `2026_08_19_140000_add_cnpj_index_to_monitored_companies_table` adiciona `index('cnpj')`;
  `EXPLAIN` confirma `type=ref`, `key=monitored_companies_cnpj_index`, `rows=1`.
- **A2** (baixa, informativo, sem desconto) — o rate limit é chaveado pelo **dono** do token
  (`user id`), não por token individual — conforme o aceite ("por dono/ip"), mas o contrato
  externo fala "por token". Dois tokens do mesmo usuário compartilham o balde de 30/min.
  Pendente de confirmação com o consumidor; se necessário, chavear por `accessToken()->id`.

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25,0 |
| Corretude & regra de negócio | 20 | 5 | 20,0 |
| Segurança & autorização | 15 | 5 | 15,0 |
| Arquitetura & responsabilidades | 12 | 5 | 12,0 |
| Banco & performance | 12 | 4 | 9,6 |
| Testes | 10 | 5 | 10,0 |
| Legibilidade & manutenção | 6 | 5 | 6,0 |
| **Total** | **100** | | **98** |

*(A1 já corrigido após o veredito; a nota 98 reflete o estado medido pelo Juiz.)*
