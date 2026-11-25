<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Fontes de risco
    |--------------------------------------------------------------------------
    |
    | Enriquecimento de risco por CNPJ a partir de fontes públicas. A 1ª fonte
    | é o Portal da Transparência (CGU): cadastros de sanção CEIS, CNEP e CEPIM.
    | A chave de API é um segredo — vive só no .env. Sem ela a integração fica
    | inativa e o painel de risco degrada com elegância (igual ao Asaas).
    |
    | Cadastro da chave (grátis): https://portaldatransparencia.gov.br/api-de-dados/cadastrar-email
    |
    */

    'portal' => [

        // Base da API de Dados da CGU. O token vai no header `chave-api-dados`.
        'base_url' => env('PORTAL_TRANSPARENCIA_BASE_URL', 'https://api.portaldatransparencia.gov.br/api-de-dados'),

        // Chave de API (header chave-api-dados). Sem ela a consulta de risco
        // fica inativa e o painel mostra "integração não configurada".
        'api_key' => env('PORTAL_TRANSPARENCIA_API_KEY'),

        // Segundos antes de uma requisição ao Portal expirar.
        'timeout' => (int) env('PORTAL_TRANSPARENCIA_TIMEOUT', 10),
    ],

    // Minutos que o resultado de risco de um CNPJ fica em cache. Os cadastros de
    // sanção mudam devagar (dias), e a API tem teto de req/min — 12h evita
    // martelar o Portal a cada visita da página da empresa.
    'cache_ttl' => (int) env('RISCO_CACHE_TTL', 720),

    /*
    |--------------------------------------------------------------------------
    | Varredura diária de sanções (vigilo:varrer-sancoes)
    |--------------------------------------------------------------------------
    |
    | Gera o alerta de sanção nova para a carteira inteira, fora do refresh da
    | Receita. Cada empresa custa até 3 chamadas à CGU (CEIS/CNEP/CEPIM); o teto
    | da API é 400 req/min (700 entre 00h–06h de Brasília). 120 empresas/min ≈
    | 360 req/min, abaixo do teto mesmo se a varredura escorregar para o horário
    | comercial. Medido em prod (2026-10-14): 813 empresas em 6min48s, ou seja, o
    | ritmo é quem limita (a latência das 3 chamadas cabe nos 0,5 s por empresa).
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Cópia local das sanções da CGU (vigilo:sancoes-cgu)
    |--------------------------------------------------------------------------
    |
    | O "download de dados" do Portal da Transparência publica um zip por fonte
    | (CEIS, CNEP, CEPIM), em dias próprios de cada uma — só o mais recente fica
    | no ar (dias sem arquivo respondem 403). Não precisa de chave. A cópia
    | alimenta o risco de QUALQUER empresa (perfil público, empresas ligadas)
    | sem uma chamada à API por empresa. `max_idade_dias`: acima disso a tela
    | avisa que a cópia está desatualizada (agendador parado).
    |
    */

    'cgu_download' => [
        'base_url' => env('CGU_DOWNLOAD_BASE_URL', 'https://portaldatransparencia.gov.br/download-de-dados'),

        'dias' => (int) env('CGU_DOWNLOAD_DIAS', 10),

        'timeout' => (int) env('CGU_DOWNLOAD_TIMEOUT', 180),

        'max_idade_dias' => (int) env('CGU_DOWNLOAD_MAX_IDADE_DIAS', 7),
    ],

    /*
    |--------------------------------------------------------------------------
    | Contratos públicos (empresa como fornecedora do governo federal)
    |--------------------------------------------------------------------------
    |
    | Consultados na hora pela mesma chave da API do Portal da Transparência
    | (`contratos/cpf-cnpj`, 15 por página, SEM ordem por data e sem total): lê
    | em lotes paralelos de 5 até a última página ou `max_paginas` (40 = 600
    | contratos; empresa comum = 1–2 páginas). Cacheado por `cache_minutos` (só
    | resultado bom) — o teto da API é 400 req/min, dividido com a varredura.
    |
    */

    'contratos' => [
        'max_paginas' => (int) env('CONTRATOS_MAX_PAGINAS', 40),

        'cache_minutos' => (int) env('CONTRATOS_CACHE_MINUTOS', 1440),

        // Depois de uma falha, quanto esperar antes de reler (não martelar a API).
        'falha_minutos' => (int) env('CONTRATOS_FALHA_MINUTOS', 5),

        // Prazo total da leitura das páginas (o card é lazy: não pode travar).
        'prazo_segundos' => (int) env('CONTRATOS_PRAZO_SEGUNDOS', 20),
    ],

    'sweep' => [
        // Fuso da agenda e do "dia" da varredura (a empresa checada hoje é pulada
        // na 2ª passada). A app roda em UTC; a janela folgada da CGU é em Brasília.
        'timezone' => env('RISCO_SWEEP_TIMEZONE', 'America/Sao_Paulo'),

        // Teto de empresas por minuto (0 = sem pausa — só para testes).
        'per_minute' => (int) env('RISCO_SWEEP_PER_MINUTE', 120),

        // Disjuntor: aborta a varredura após N empresas SEGUIDAS com falha (CGU
        // indisponível ou erro inesperado, ex.: Redis/DB fora) — evita esperar o
        // timeout (3 × timeout) em toda a carteira quando a API está fora. A
        // passada seguinte (04:00, ou a do dia seguinte) retoma de onde parou.
        'max_consecutive_failures' => (int) env('RISCO_SWEEP_MAX_FAILURES', 5),
    ],

];
