<?php

declare(strict_types=1);

namespace App\Services\Webhooks;

use App\Support\WebhookSignature;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Envio assinado de um webhook (uma tentativa). Centraliza headers, assinatura
 * HMAC sobre o corpo exato enviado, timeout e `withoutRedirecting()` (não segue
 * redirect — evita pivô de SSRF). O retry fica a cargo de quem chama (job).
 */
final class WebhookSender
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function send(string $url, string $secret, array $payload): Response
    {
        $body = (string) json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return Http::withHeaders([
            'X-Vigilo-Event-Id' => $this->str($payload, 'event_id'),
            'X-Vigilo-Event' => $this->str($payload, 'event') ?: 'company.changes',
            'X-Vigilo-Signature' => WebhookSignature::for($body, $secret),
        ])->withoutRedirecting()->timeout(10)->withBody($body, 'application/json')->post($url);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function str(array $payload, string $key): string
    {
        $value = $payload[$key] ?? null;

        return is_string($value) ? $value : '';
    }
}
