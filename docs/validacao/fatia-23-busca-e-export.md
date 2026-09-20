# Fatia 23 — Busca no portfólio (CNPJ/TAG) + Exportar alertas em Excel

> Duas melhorias de operação do dia a dia: achar um CNPJ/unidade rápido dentro de um portfólio grande, e levar os alertas para o Excel.

- **Data:** 2026-09-20
- **Ciclo:** 1
- **Veredito:** ✅ **APROVADO — 100/100** (`acao: seguir`, sem blockers)
- **Portões:** Pint ok · PHPStan nível 8 ok (0 erros) · `php artisan test` **224/224** (652 asserts) · `composer audit` limpo

## O que entrou

### Busca no portfólio
Campo único na tela do portfólio (`Portfolios\Show`) que filtra a lista **por CNPJ** (ignorando pontuação — casa pelos dígitos) **e por TAG** (o `label` da empresa), case-insensitive. Property `#[Url] public string $search` (compartilhável na URL) com `wire:model.live.debounce.400ms`; ao digitar volta para a 1ª página (`updatedSearch → resetPage`); o empty-state avisa "Nenhuma empresa encontrada para …". A busca filtra **só a lista visível** — os cards de resumo e as contagens por status seguem refletindo o portfólio inteiro (decisão de UX: o termo não deve "sumir" com os totais). Multi-tenant intacto: a query já parte de `where('portfolio_id', …)`, e o portfólio é escopado por organização.

> Substring (`%dígitos%`) é proposital: permite buscar por um trecho do CNPJ, não só pelo começo.

### Exportar alertas em Excel
Botão **"Exportar Excel"** no inbox (`Alerts\Inbox::export`) que gera um **`.xlsx` real** (PhpSpreadsheet) com **todos os alertas do filtro atual** — sem o teto de 100 da listagem —, respeitando o filtro de severidade e o escopo da organização (reusa exatamente `openFilteredEvents()`). Colunas: Empresa, CNPJ, Tipo de mudança, Campo, De, Para, Severidade, Detectado em, Reconhecido em. Download com nome datado (`alertas-AAAA-MM-DD-HHMMSS.xlsx`), `Content-Type` de xlsx, e o arquivo temporário é apagado após o envio (`deleteFileAfterSend`). A geração fica isolada num serviço `App\Services\Export\AlertsExcelExport` (sem I/O de rede nem autorização — o componente já escopou os alertas).

### Dependência
`phpoffice/phpspreadsheet ^5.10` adicionado em `require` (produção — export é feature de produção). Todas as extensões que ele exige (zip/gd/intl/mbstring + as padrão do `php:8.4-fpm-alpine`) **já estão** no `deploy/Dockerfile` → **sem rebuild de imagem**. Aproveitei para corrigir um advisory alto do transitivo `league/commonmark` (bump para 2.10.1) — `composer audit` agora limpo.

## Testes
- `PortfolioSearchTest`: filtra por TAG; filtra por CNPJ com pontuação; busca vazia mostra tudo; empty-state ciente da busca.
- `AlertsExportTest`: `export` baixa um arquivo (`assertFileDownloaded`); a planilha tem cabeçalho + 1 linha por alerta com os valores certos; caso vazio gera só o cabeçalho.
- Filtro de severidade e escopo por organização do export são cobertos transitivamente por `AlertsInboxTest` (mesma query `openFilteredEvents`).

## Deploy
Precisa de assets (classes Tailwind novas do campo de busca/botão) e o `composer install` puxa o PhpSpreadsheet (roda no deploy). **Sem migration, sem rebuild de imagem:**

```
./src/deploy/deploy.sh --assets
```

## Nota

| Dimensão | Peso | Score | Pontos |
|---|---|---|---|
| Conformidade com o aceite | 25 | 5 | 25 |
| Corretude & regra de negócio | 20 | 5 | 20 |
| Segurança & autorização | 15 | 5 | 15 |
| Arquitetura & responsabilidades | 12 | 5 | 12 |
| Banco & performance | 12 | 5 | 12 |
| Testes | 10 | 5 | 10 |
| Legibilidade & manutenção | 6 | 5 | 6 |
| **Total** | **100** | | **100** |
