<?php

declare(strict_types=1);

namespace App\Services\Asaas;

use App\Services\Asaas\Exceptions\AsaasException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Cliente HTTP fino da API do Asaas (v3). Cuida só do transporte: autentica com
 * o access_token, faz a chamada e devolve o corpo decodificado — ou lança
 * AsaasException. A regra de negócio (o que criar/atualizar) fica no
 * SubscriptionService.
 *
 * @see https://docs.asaas.com/reference
 */
final class AsaasClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly ?string $apiKey,
        private readonly int $timeout,
    ) {}

    /**
     * A integração só funciona com a chave de API configurada. Permite à UI
     * degradar com elegância quando o .env ainda não tem a credencial.
     */
    public function isConfigured(): bool
    {
        return $this->apiKey !== null && $this->apiKey !== '';
    }

    /**
     * Cria um cliente (customer) no Asaas.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createCustomer(array $data): array
    {
        return $this->post('/customers', $data);
    }

    /**
     * Cria uma assinatura recorrente (subscription).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createSubscription(array $data): array
    {
        return $this->post('/subscriptions', $data);
    }

    /**
     * Busca uma assinatura pelo id. Retorna null se não existir (404).
     *
     * @return array<string, mixed>|null
     */
    public function getSubscription(string $subscriptionId): ?array
    {
        return $this->get("/subscriptions/{$subscriptionId}");
    }

    /**
     * Lista as cobranças (payments) geradas por uma assinatura, mais recentes
     * primeiro. Vazio se a assinatura não existir.
     *
     * @return list<array<string, mixed>>
     */
    public function listSubscriptionPayments(string $subscriptionId): array
    {
        $response = $this->get("/subscriptions/{$subscriptionId}/payments");

        $data = $response['data'] ?? [];

        if (! is_array($data)) {
            return [];
        }

        /** @var list<array<string, mixed>> $payments */
        $payments = [];

        foreach ($data as $item) {
            if (is_array($item)) {
                /** @var array<string, mixed> $item */
                $payments[] = $item;
            }
        }

        return $payments;
    }

    /**
     * Cancela (remove) uma assinatura no Asaas. Idempotente: um 404 (já removida)
     * não é tratado como erro.
     */
    public function cancelSubscription(string $subscriptionId): void
    {
        $endpoint = "/subscriptions/{$subscriptionId}";

        try {
            $response = $this->request()->delete($endpoint);
        } catch (ConnectionException $e) {
            throw AsaasException::unreachable($endpoint, $e->getMessage());
        }

        if ($response->status() === 404) {
            return;
        }

        if ($response->failed()) {
            throw AsaasException::requestFailed($endpoint, $response->status(), $response->body());
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function post(string $endpoint, array $data): array
    {
        try {
            $response = $this->request()->post($endpoint, $data);
        } catch (ConnectionException $e) {
            throw AsaasException::unreachable($endpoint, $e->getMessage());
        }

        if ($response->failed()) {
            throw AsaasException::requestFailed($endpoint, $response->status(), $response->body());
        }

        $decoded = $response->json();

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function get(string $endpoint): ?array
    {
        try {
            $response = $this->request()->get($endpoint);
        } catch (ConnectionException $e) {
            throw AsaasException::unreachable($endpoint, $e->getMessage());
        }

        if ($response->status() === 404) {
            return null;
        }

        if ($response->failed()) {
            throw AsaasException::requestFailed($endpoint, $response->status(), $response->body());
        }

        $decoded = $response->json();

        return is_array($decoded) ? $decoded : [];
    }

    private function request(): PendingRequest
    {
        if (! $this->isConfigured()) {
            throw AsaasException::notConfigured();
        }

        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->asJson()
            ->timeout($this->timeout)
            ->withHeaders(['access_token' => (string) $this->apiKey]);
    }
}
