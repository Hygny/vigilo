<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Gateway de cobrança Asaas
    |--------------------------------------------------------------------------
    |
    | Integração de cobrança recorrente (assinaturas) da plataforma. A chave de
    | API e o token do webhook são segredos — vivem só no .env, nunca no código.
    | Em sandbox use a URL de sandbox; em produção troque ASAAS_BASE_URL e a
    | ASAAS_API_KEY para as de produção.
    |
    */

    // Sandbox: https://api-sandbox.asaas.com/v3 · Produção: https://api.asaas.com/v3
    'base_url' => env('ASAAS_BASE_URL', 'https://api-sandbox.asaas.com/v3'),

    // Chave de API (header access_token). Sem ela a integração fica inativa.
    'api_key' => env('ASAAS_API_KEY'),

    // Token que o Asaas envia no header asaas-access-token de cada webhook.
    // Validado em tempo constante antes de processar qualquer evento.
    'webhook_token' => env('ASAAS_WEBHOOK_TOKEN'),

    // Segundos antes de uma requisição ao Asaas expirar.
    'timeout' => (int) env('ASAAS_TIMEOUT', 15),

    // Dias de teste grátis antes da primeira cobrança da assinatura paga.
    // 0 = sem trial (o plano Free já é a porta de entrada).
    'trial_days' => (int) env('ASAAS_TRIAL_DAYS', 0),

    // Forma de pagamento da assinatura. UNDEFINED deixa o cliente escolher
    // (PIX/boleto/cartão) na tela de checkout do Asaas.
    'billing_type' => env('ASAAS_BILLING_TYPE', 'UNDEFINED'),

];
