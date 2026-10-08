# Fatia OSINT-3 — API OSINT: empresas por sócio

Terceiro e último endpoint do contrato OSINT (ver [fatia-osint-1](fatia-osint-1.md),
[fatia-osint-2](fatia-osint-2.md)).

## O que mudou

- **`POST /api/v1/vigilancia/empresas/por-socio`** — body
  `{ "nomes": [...], "situacao": "ATIVA", "limite": 50 }`. Devolve as empresas
  (matriz) em que algum dos nomes aparece no QSA, cada uma com o QSA completo.
  Mesmo grupo de middlewares da OSINT-1/2.
- **É POST** de propósito: os nomes não vão para a URL nem para o log do nginx —
  só para a trilha de auditoria interna (`api_access_logs`), que é o ponto
  (LGPD). Testado.
- **Match por nome completo normalizado** (sem acento, caixa alta, espaços
  colapsados): "josé da silva" casa com "JOSÉ DA SILVA". A normalização é a
  função única `App\Support\NomeSocio::norm()` — usada na entrada da API **e**
  para popular a coluna `socios.nome_norm`; as duas pontas pela mesma função
  garantem o match. O consumidor (n8n) faz a checagem fina de homônimo/parente.
- `nomes` vazio → 200 `data:[]` (não é erro). `situacao` opcional, `limite`
  default 50 / teto `vigilancia.por_socio_max` (100). Ordena por abertura desc.
- Sócios de todas as empresas do resultado em UMA query (`whereIn`), sem N+1.
- Erros `{error:{code,message}}`: `situacao_invalida`, `base_indisponivel` (503).

Arquivos: `app/Http/Controllers/Api/Vigilancia/PorSocioController.php`,
`app/Services/Vigilancia/EmpresaLookup.php` (novo `porSocio()`),
`app/Support/NomeSocio.php` (novo), `app/Console/Commands/NormalizeSocios.php`
(novo), `routes/api.php`, `tests/Feature/Api/VigilanciaPorSocioTest.php`.

## Preparação da base (rodar no VPS) ⚠️

A busca depende da coluna `socios.nome_norm` + índice. O comando faz tudo
(ADD COLUMN + popular + CREATE INDEX). **É PESADO na 1ª vez (28M linhas)** —
rodar em horário de baixo uso:

```bash
cd ~/apps/vigilo && docker compose exec -T app php artisan vigilo:normalizar-socios
```

Enquanto o comando não roda, o endpoint responde (nome_norm = null → nenhum
match), então sobe sem quebrar; mas só começa a achar depois do populate.

**Após cada reimport mensal, re-rodar o comando.** O reimport usa
`LOADING_STRATEGY=upsert` (não dropa a tabela), então a **coluna e o índice
persistem** — o que precisa é **refrescar o dado**: sócios novos entram com
`nome_norm` vazio (o pipeline não conhece a coluna) e nomes alterados ficam
defasados. Re-rodar recalcula tudo (o `CREATE INDEX IF NOT EXISTS` vira no-op).
É só o `nome_norm` que envelhece; os índices de `cep`/documento não precisam de
nada (colunas que o pipeline preenche).

## Portões

Pint ok · PHPStan nível 8 (0 erros) · Pest 307 verdes (11 novos nesta fatia).

## Contrato OSINT completo

Com esta fatia, os três endpoints do contrato do n8n estão entregues:
`GET /cnpjs/{cnpj}` · `GET /empresas/por-endereco` · `POST /empresas/por-socio`.
