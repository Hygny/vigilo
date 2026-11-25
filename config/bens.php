<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Bens e recebíveis (recuperação de crédito)
    |--------------------------------------------------------------------------
    |
    | Aeronaves: cópia local do Registro Aeronáutico Brasileiro (ANAC, dados
    | abertos, atualizado todo dia, ~24 MB / ~35 mil aeronaves) — carregada pelo
    | `vigilo:aeronaves-anac` (agendado). Imóveis, veículos e contas não têm fonte
    | aberta (ONR/cartórios pagos, Detran por convênio, RENAJUD/SISBAJUD só juízo).
    |
    */

    'aeronaves' => [
        'url' => env('ANAC_RAB_URL', 'https://sistemas.anac.gov.br/dadosabertos/Aeronaves/RAB/dados_aeronaves.csv'),

        'timeout' => (int) env('ANAC_RAB_TIMEOUT', 180),

        // Piso de aeronaves lidas: abaixo, o arquivo está torto e a cópia boa fica.
        'min_aeronaves' => (int) env('ANAC_RAB_MIN_AERONAVES', 10000),

        // Queda máxima de aeronaves em relação à carga anterior (0,2 = aceita até
        // 20% a menos). Acima disso o arquivo é tido como parcial e a cópia boa fica.
        'queda_maxima' => (float) env('ANAC_RAB_QUEDA_MAXIMA', 0.2),

    ],

    /*
    | Indícios de esvaziamento e sucessão: o que mudou em volta da devedora
    | depois do marco da dívida (1ª inscrição na PGFN ou data informada).
    */
    'esvaziamento' => [
        // statement_timeout das varreduras na base CNPJ (endereço, empresas dos sócios)
        'timeout_ms' => (int) env('ESVAZIAMENTO_TIMEOUT_MS', 8000),

        // Cache do relatório completo (a base muda por mês; 0 desliga)
        'cache_minutos' => (int) env('ESVAZIAMENTO_CACHE_MINUTOS', 60),
    ],

];
