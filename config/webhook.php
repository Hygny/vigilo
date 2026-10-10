<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Webhook de saída
    |--------------------------------------------------------------------------
    */

    // Resolve o host do webhook e barra destinos que resolvem para IP
    // privado/reservado no momento do ENVIO (mitiga DNS→privado, além do
    // PublicHttpsUrl que já barra IP literal na validação). Desligável para
    // ambientes sem DNS (ex.: testes isolados).
    'verify_destination_ip' => (bool) env('WEBHOOK_VERIFY_DESTINATION_IP', true),

    // Dias de retenção do log de entregas (webhook_deliveries) antes da poda
    // diária (model:prune). 0 ou negativo é tratado como 1.
    'deliveries_retention_days' => (int) env('WEBHOOK_DELIVERIES_RETENTION_DAYS', 90),

];
