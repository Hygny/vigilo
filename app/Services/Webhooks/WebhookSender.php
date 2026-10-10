<?php

declare(strict_types=1);

namespace App\Services\Webhooks;

use App\Services\Webhooks\Exceptions\BlockedWebhookDestination;
use App\Support\WebhookSignature;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Envio assinado de um webhook (uma tentativa). Centraliza headers, assinatura
 * HMAC sobre o corpo exato enviado, timeouts, `withoutRedirecting()` (não segue
 * redirect — evita pivô de SSRF) e a checagem de destino (host que resolve para
 * IP privado/reservado é barrado). O retry fica a cargo de quem chama (job).
 */
final class WebhookSender
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function send(string $url, string $secret, array $payload): Response
    {
        $this->assertPublicDestination($url);

        $body = (string) json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return Http::withHeaders([
            'X-Vigilo-Event-Id' => $this->str($payload, 'event_id'),
            'X-Vigilo-Event' => $this->str($payload, 'event') ?: 'company.changes',
            'X-Vigilo-Signature' => WebhookSignature::for($body, $secret),
        ])->withoutRedirecting()->connectTimeout(5)->timeout(10)->withBody($body, 'application/json')->post($url);
    }

    /**
     * Barra o envio quando o host do webhook resolve para um IP privado/reservado
     * (DNS→privado — além do PublicHttpsUrl que já cobre IP literal na validação).
     * Resíduo conhecido (DT-15): não impede DNS rebinding entre esta checagem e a
     * conexão do cURL. Desligável por config (ambientes sem DNS).
     */
    private function assertPublicDestination(string $url): void
    {
        if (! (bool) config('webhook.verify_destination_ip', true)) {
            return;
        }

        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            throw new BlockedWebhookDestination('URL de webhook inválida.');
        }

        foreach ($this->resolveIps(trim($host, '[]')) as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                throw new BlockedWebhookDestination('O destino do webhook resolve para um IP privado ou reservado.');
            }
        }
    }

    /**
     * IPs (v4 + v6) para onde o host resolve. IP literal volta como está (o
     * PublicHttpsUrl já barra privados na validação). Falha de resolução → lista
     * vazia (a própria requisição HTTP falhará naturalmente).
     *
     * @return list<string>
     */
    private function resolveIps(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            return [$host];
        }

        $ipv4 = @gethostbynamel($host);
        $ipv4 = $ipv4 === false ? [] : $ipv4;

        $records = @dns_get_record($host, DNS_AAAA);
        $ipv6 = $records === false ? [] : array_values(array_filter(array_map(
            fn (array $record): ?string => isset($record['ipv6']) && is_string($record['ipv6']) ? $record['ipv6'] : null,
            $records,
        )));

        return array_merge($ipv4, $ipv6);
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
