<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Planos de assinatura
    |--------------------------------------------------------------------------
    |
    | Limites e preços por plano (App\Enums\Plan). Ajuste livremente os números
    | — são a calibragem do negócio. `max_companies` é o teto de CNPJs
    | monitorados por organização (a quota). `price` é o valor mensal em reais.
    | Valores iniciais são sugestões; troque conforme sua precificação.
    |
    */

    'free' => [
        'max_companies' => (int) env('PLAN_FREE_MAX_COMPANIES', 20),
        'price' => (float) env('PLAN_FREE_PRICE', 0),
    ],

    'pro' => [
        'max_companies' => (int) env('PLAN_PRO_MAX_COMPANIES', 200),
        'price' => (float) env('PLAN_PRO_PRICE', 149),
    ],

    'business' => [
        'max_companies' => (int) env('PLAN_BUSINESS_MAX_COMPANIES', 2000),
        'price' => (float) env('PLAN_BUSINESS_PRICE', 499),
    ],

];
