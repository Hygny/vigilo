<?php

declare(strict_types=1);

namespace App\Providers\Cnpj;

use App\Contracts\CnpjDataProvider;
use App\DTO\CompanyData;
use App\DTO\PartnerData;
use App\Providers\Cnpj\Exceptions\CnpjProviderException;
use App\Support\Cnpj;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Sleep;
use Throwable;

/**
 * CNPJ data provider backed by BrasilAPI (https://brasilapi.com.br).
 *
 * Endpoint: GET /api/cnpj/v1/{cnpj}
 */
final class BrasilApiProvider implements CnpjDataProvider
{
    /** HTTP statuses worth retrying with backoff. */
    private const RETRYABLE_STATUSES = [429, 500, 502, 503, 504];

    private const THROTTLE_KEY = 'cnpj-provider:brasilapi';

    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeout,
        private readonly int $tries,
        private readonly int $retryBackoffMs,
        private readonly int $throttlePerMinute,
    ) {}

    public function fetch(string $cnpj): ?CompanyData
    {
        // Fail fast on malformed input — never conflated with "not found".
        $normalized = Cnpj::fromString($cnpj)->value;

        $this->throttle();

        try {
            $response = Http::baseUrl($this->baseUrl)
                ->acceptJson()
                ->timeout($this->timeout)
                ->retry($this->tries, fn (int $attempt): int => $attempt * $this->retryBackoffMs, function (Throwable $e): bool {
                    if ($e instanceof ConnectionException) {
                        return true;
                    }

                    return $e instanceof RequestException
                        && in_array($e->response->status(), self::RETRYABLE_STATUSES, true);
                }, throw: false)
                ->get('/'.$normalized);
        } catch (ConnectionException $e) {
            throw CnpjProviderException::unreachable($normalized, $e->getMessage());
        }

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw CnpjProviderException::requestFailed($normalized, $response->status());
        }

        $decoded = $response->json();

        return $this->map($normalized, is_array($decoded) ? $decoded : []);
    }

    /**
     * Client-side throttle: cap requests to the configured req/min. When the
     * limit is reached, sleep once until the window frees before recording the
     * hit. A soft cap (never an unbounded spin), backing up the job-level rate
     * limiting that also guards the upstream service.
     */
    private function throttle(): void
    {
        if ($this->throttlePerMinute <= 0) {
            return;
        }

        if (RateLimiter::tooManyAttempts(self::THROTTLE_KEY, $this->throttlePerMinute)) {
            Sleep::for(max(RateLimiter::availableIn(self::THROTTLE_KEY), 1))->seconds();
        }

        RateLimiter::hit(self::THROTTLE_KEY, 60);
    }

    /**
     * @param  array<array-key, mixed>  $p
     */
    private function map(string $cnpj, array $p): CompanyData
    {
        return new CompanyData(
            cnpj: $cnpj,
            razaoSocial: $this->stringOrNull($p, 'razao_social') ?? '',
            nomeFantasia: $this->stringOrNull($p, 'nome_fantasia'),
            situacaoCadastral: mb_strtoupper(
                $this->stringOrNull($p, 'descricao_situacao_cadastral')
                    ?? $this->stringOrNull($p, 'situacao_cadastral')
                    ?? ''
            ),
            situacaoData: $this->stringOrNull($p, 'data_situacao_cadastral'),
            cnaePrincipal: $this->stringOrNull($p, 'cnae_fiscal'),
            porte: $this->stringOrNull($p, 'porte'),
            naturezaJuridica: $this->stringOrNull($p, 'natureza_juridica'),
            logradouro: $this->buildLogradouro($p),
            municipio: $this->stringOrNull($p, 'municipio'),
            uf: $this->stringOrNull($p, 'uf'),
            partners: $this->mapPartners($p),
            raw: $p,
        );
    }

    /**
     * @param  array<array-key, mixed>  $p
     * @return list<PartnerData>
     */
    private function mapPartners(array $p): array
    {
        $qsa = $p['qsa'] ?? [];

        if (! is_array($qsa)) {
            return [];
        }

        $partners = [];

        foreach ($qsa as $socio) {
            if (! is_array($socio)) {
                continue;
            }

            $partners[] = new PartnerData(
                nome: $this->stringOrNull($socio, 'nome_socio') ?? '',
                documento: $this->stringOrNull($socio, 'cnpj_cpf_do_socio'),
                qualificacao: $this->stringOrNull($socio, 'qualificacao_socio'),
            );
        }

        return $partners;
    }

    /**
     * @param  array<array-key, mixed>  $p
     */
    private function buildLogradouro(array $p): ?string
    {
        $logradouro = $this->stringOrNull($p, 'logradouro');
        $numero = $this->stringOrNull($p, 'numero');

        if ($logradouro === null) {
            return $numero;
        }

        return $numero === null ? $logradouro : "{$logradouro}, {$numero}";
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private function stringOrNull(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        if (! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
