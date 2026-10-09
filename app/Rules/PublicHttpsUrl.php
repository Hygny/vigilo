<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida a URL de um webhook de saída: precisa ser **https** e apontar para um
 * host público — bloqueia localhost e literais de IP privado/reservado, que
 * seriam um vetor de SSRF (sondar a rede interna do VPS a partir de um tenant).
 *
 * Resíduo conhecido (débito): um hostname que RESOLVE para um IP privado, e
 * faixas IPv6 além do literal, não são barrados aqui (exigiria resolução de DNS
 * no momento do envio). Ver docs/debito-tecnico.md.
 */
final class PublicHttpsUrl implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return; // 'nullable' cuida do vazio
        }

        if (parse_url($value, PHP_URL_SCHEME) !== 'https') {
            $fail('A URL do webhook deve usar https.');

            return;
        }

        $host = parse_url($value, PHP_URL_HOST);

        if (! is_string($host) || ! self::isPublicHost($host)) {
            $fail('A URL deve apontar para um host público (sem localhost ou IP privado).');
        }
    }

    private static function isPublicHost(string $host): bool
    {
        $host = strtolower(trim($host, '[]')); // remove colchetes de IPv6

        if (in_array($host, ['localhost', '0.0.0.0'], true)) {
            return false;
        }

        if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
            // IP literal: barra faixas privadas/reservadas (loopback, link-local, etc.).
            return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
        }

        return true; // hostname comum
    }
}
