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
    | da API é 400 req/min (700 entre 00h–06h). 120 empresas/min ≈ 360 req/min,
    | abaixo do teto mesmo se a varredura escorregar para o horário comercial.
    |
    */

    'sweep' => [
        // Teto de empresas por minuto (0 = sem pausa — só para testes).
        'per_minute' => (int) env('RISCO_SWEEP_PER_MINUTE', 120),

        // Disjuntor: aborta a varredura após N empresas SEGUIDAS com a CGU
        // indisponível — evita esperar o timeout (3 × timeout) em toda a carteira
        // quando a API está fora. Tenta de novo no dia seguinte.
        'max_consecutive_failures' => (int) env('RISCO_SWEEP_MAX_FAILURES', 5),
    ],

];
