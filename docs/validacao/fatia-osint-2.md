# Fatia OSINT-2 — API OSINT: empresas por endereço

Segundo endpoint do contrato OSINT (ver [fatia-osint-1](fatia-osint-1.md)).

## O que mudou

- **`GET /api/v1/vigilancia/empresas/por-endereco?cep=&numero=&situacao=&limite=`**
  — lista as empresas no mesmo **CEP + número** (com sócios), direto da base,
  no mesmo objeto `Empresa` do endpoint 1. Mesmo grupo de middlewares
  (`auth:sanctum` + `throttle:vigilancia-api` + auditoria + ability
  `vigilancia:osint`).
- **Match:** `cep` exato + `numero` **normalizado** (caixa alta, sem o prefixo
  "Nº"/"N°", sem espaços → "Nº 320" casa com "320"). **Complemento é ignorado**
  de propósito (salas diferentes do mesmo prédio contam).
- **`situacao`** opcional (ATIVA/BAIXADA/INAPTA/SUSPENSA/NULA → filtra pelo
  código na base); **`limite`** default 50, teto `vigilancia.por_endereco_max`
  (100). Ordena por `data_inicio_atividade` desc.
- **CEP "00000000"** (o workflow manda quando não tem endereço) → 200 com
  `data: []`, **sem tocar na base** (curto-circuito).
- Erros no padrão `{error:{code,message}}`: `cep_invalido` (≠ 8 dígitos),
  `numero_obrigatorio`, `situacao_invalida`, `base_indisponivel` (503).
- Sócios buscados **em uma query só** (`whereIn` por cnpj_basico) → sem N+1.

Arquivos: `app/Http/Controllers/Api/Vigilancia/PorEnderecoController.php`,
`app/Services/Vigilancia/EmpresaLookup.php` (novo `porEndereco()` + refatoração
`build()`/`sociosPorBasico()`), `app/Support/SituacaoCadastral.php` (novo
`code()` — inverso de `label()`), `routes/api.php`,
`tests/Feature/Api/VigilanciaPorEnderecoTest.php`.

## Índice necessário na base (rodar no VPS) ⚠️

O `estabelecimentos` **não tem** índice em `cep` (só PK + cnae/municipio/
situacao/uf). Sem ele, `WHERE cep = ?` vira seq scan em 73M → estoura o SLA.
Rodar **uma única vez**:

```sql
CREATE INDEX IF NOT EXISTS idx_estabelecimentos_cep ON estabelecimentos (cep);
```

> O reimport mensal usa `LOADING_STRATEGY=upsert` (não dropa a tabela), então o
> índice **persiste** — não precisa recriar todo mês. `cep` é coluna que o
> próprio pipeline preenche, então o índice fica sempre consistente.

Com o CEP indexado (altamente seletivo), o filtro de número roda no punhado de
linhas daquele CEP, em memória — sem precisar de coluna normalizada nem batch.

## Limite de varredura

`EmpresaLookup::CANDIDATE_SCAN = 2000`: teto de estabelecimentos lidos por CEP
antes do filtro de número. Protege contra um CEP "geral" patológico; como a
ordenação é por abertura desc, pegam-se os mais novos. Um CEP específico tem
poucas dezenas de linhas, então não afeta o caso real.

## Portões

Pint ok · PHPStan nível 8 (0 erros) · Pest 294 verdes (9 novos nesta fatia).

## Pendente — OSINT-3

`POST /empresas/por-socio` (busca por nome). Esta sim exige coluna `nome_norm`
em `socios` (28M) + índice e o tratamento do `unaccent` não-IMMUTABLE do
Postgres — o batch pesado que foi adiado.
