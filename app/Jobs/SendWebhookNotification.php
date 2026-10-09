<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Organization;
use App\Services\Webhooks\WebhookSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\RequestException;
use Throwable;

/**
 * Entrega assinada (HMAC) do webhook de saída de uma organização. Reenfileira em
 * falha (rede ou status não-2xx) com backoff; o mesmo `event_id` viaja em todas
 * as tentativas, então o consumidor deduplica. Grava uma linha em
 * `webhook_deliveries` no resultado final (sucesso ou falha definitiva).
 */
class SendWebhookNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /** @var list<int> */
    public array $backoff = [10, 30, 120, 300];

    /**
     * @param  array<string, mixed>  $payload  corpo do webhook (inclui event_id, event, cnpj, mudancas)
     */
    public function __construct(public int $organizationId, public array $payload) {}

    public function handle(WebhookSender $sender): void
    {
        $organization = Organization::find($this->organizationId);

        // Config removida no meio do caminho → nada a entregar.
        if ($organization === null || ! $organization->hasWebhook()) {
            return;
        }

        $response = $sender->send(
            (string) $organization->webhook_url,
            (string) $organization->webhook_secret,
            $this->payload,
        );

        // Status não-2xx lança RequestException → reenfileira (ou cai em failed()).
        $response->throw();

        $this->recordDelivery($organization, 'success', $response->status(), null);
    }

    public function failed(?Throwable $e): void
    {
        $organization = Organization::find($this->organizationId);

        if ($organization === null) {
            return;
        }

        $status = $e instanceof RequestException ? $e->response->status() : null;
        $this->recordDelivery($organization, 'failed', $status, $e?->getMessage());
    }

    private function recordDelivery(Organization $organization, string $status, ?int $responseStatus, ?string $error): void
    {
        $mudancas = $this->payload['mudancas'] ?? null;

        $organization->webhookDeliveries()->create([
            'event_id' => $this->str('event_id'),
            'event_type' => $this->str('event') ?: 'company.changes',
            'cnpj' => $this->str('cnpj') ?: null,
            'changes_count' => is_array($mudancas) ? count($mudancas) : 0,
            'status' => $status,
            'response_status' => $responseStatus,
            'error' => $error === null ? null : mb_substr($error, 0, 480),
        ]);
    }

    private function str(string $key): string
    {
        $value = $this->payload[$key] ?? null;

        return is_string($value) ? $value : '';
    }
}
