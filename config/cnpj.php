<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default CNPJ data provider
    |--------------------------------------------------------------------------
    |
    | The App\Contracts\CnpjDataProvider binding resolves to the driver named
    | here. Add new drivers by implementing the contract and wiring them in
    | App\Providers\AppServiceProvider.
    |
    */

    'driver' => env('CNPJ_DRIVER', 'brasilapi'),

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [

        'brasilapi' => [
            'base_url' => env('CNPJ_BRASILAPI_URL', 'https://brasilapi.com.br/api/cnpj/v1'),
            // Seconds before a single request times out.
            'timeout' => (int) env('CNPJ_TIMEOUT', 10),
            // Total attempts (1 = no retry) for connection errors / 429 / 5xx.
            'tries' => (int) env('CNPJ_TRIES', 3),
            // Base backoff between retries, in milliseconds (grows per attempt).
            'retry_backoff_ms' => (int) env('CNPJ_RETRY_BACKOFF_MS', 500),
        ],

        // Base CNPJ própria (V2) — lê da conexão PostgreSQL local, sem rate limit.
        // Ative com CNPJ_DRIVER=local (BrasilAPI segue como fallback opcional).
        'local' => [
            'connection' => env('CNPJ_LOCAL_CONNECTION', 'cnpj'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Throttle
    |--------------------------------------------------------------------------
    |
    | Client-side cap on how many provider requests may be made per minute, to
    | respect the upstream rate limit. Kept conservative by default. Set to 0
    | to disable throttling.
    |
    */

    'throttle' => [
        'requests_per_minute' => (int) env('CNPJ_THROTTLE_PER_MINUTE', 20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Public read API
    |--------------------------------------------------------------------------
    |
    | The GET /api/cnpj/{cnpj} endpoint that serves this app's known cadastral
    | data to authenticated integrations (Bearer token via Sanctum). The limit
    | is applied per token owner, per minute.
    |
    */

    'api' => [
        'rate_per_minute' => (int) env('CNPJ_API_RATE_PER_MINUTE', 30),
        // Máximo de mudanças retornadas em `historico` (as mais recentes primeiro).
        'history_limit' => (int) env('CNPJ_API_HISTORY_LIMIT', 50),
    ],

    /*
    |--------------------------------------------------------------------------
    | Grafo societário (V2)
    |--------------------------------------------------------------------------
    |
    | Máximo de empresas trazidas pela aresta reversa por sócio (grupo
    | econômico), para o grafo não explodir quando um sócio participa de muitas
    | empresas.
    |
    */

    'graph' => [
        'reverse_limit' => (int) env('CNPJ_GRAPH_REVERSE_LIMIT', 25),
    ],

];
