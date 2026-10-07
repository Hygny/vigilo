<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | API OSINT de vigilância (/api/v1/vigilancia)
    |--------------------------------------------------------------------------
    |
    | API de LEITURA consumida por um workflow externo (n8n). Diferente da
    | /api/cnpj (travada à carteira do token), esta consulta a base CNPJ inteira
    | — por isso exige um token com a ability `vigilancia:osint` (emitido por
    | `vigilo:osint-token`) e grava trilha de auditoria de cada chamada.
    |
    */

    // Teto de chamadas por minuto, por dono do token. 0 desativa o limite.
    'rate_per_minute' => (int) env('VIGILANCIA_API_RATE_PER_MINUTE', 60),

    // Mês da base da Receita carregada (YYYY-MM). O cron de reimport mensal
    // atualiza este env; vai em `meta.base_referencia` de toda resposta. Null
    // quando não informado — o consumidor trata como "sem data".
    'base_referencia' => env('VIGILANCIA_BASE_REFERENCIA'),

    // Tetos de lista dos endpoints de busca (Fatias OSINT-2/OSINT-3).
    'por_endereco_max' => (int) env('VIGILANCIA_POR_ENDERECO_MAX', 100),
    'por_socio_max' => (int) env('VIGILANCIA_POR_SOCIO_MAX', 100),

];
