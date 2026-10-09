<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Assinatura HMAC-SHA256 do corpo do webhook. O consumidor recomputa sobre os
 * bytes crus recebidos com o mesmo segredo e compara (hash_equals). Formato do
 * header `X-Vigilo-Signature`: "sha256=<hex>".
 */
final class WebhookSignature
{
    public static function for(string $body, string $secret): string
    {
        return 'sha256='.hash_hmac('sha256', $body, $secret);
    }
}
