<?php

declare(strict_types=1);

namespace App\Livewire\Billing;

use App\Models\User;
use App\Services\Asaas\AsaasClient;
use App\Services\Asaas\Exceptions\AsaasException;
use App\Services\Billing\SubscriptionService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Tela de cobrança da organização (só o admin do tenant). Mostra o plano atual,
 * o status da assinatura, o link para pagar a fatura em aberto e o histórico de
 * cobranças — buscados ao vivo no Asaas, degradando com elegância se a
 * integração estiver fora do ar ou não configurada.
 */
#[Layout('layouts.app')]
class Index extends Component
{
    public function mount(): void
    {
        abort_unless($this->currentUser()->isAdmin(), 403);
    }

    public function render(AsaasClient $asaas, SubscriptionService $subscriptions): View
    {
        $organization = $this->currentUser()->organization;

        abort_if($organization === null, 404);

        $subscriptionId = $organization->asaas_subscription_id;
        $invoiceUrl = null;
        $nextDueDate = null;
        $payments = [];
        $integrationAvailable = $asaas->isConfigured();
        $integrationDown = false;

        if ($subscriptionId !== null && $integrationAvailable) {
            try {
                // Uma única busca de cobranças serve tanto o link da fatura em
                // aberto quanto o histórico exibido abaixo.
                $rawPayments = $asaas->listSubscriptionPayments($subscriptionId);
                $invoiceUrl = $subscriptions->pendingInvoiceUrl($rawPayments);
                $subscription = $asaas->getSubscription($subscriptionId);
                $nextDueDate = $this->stringValue($subscription, 'nextDueDate');
                $payments = $this->mapPayments($rawPayments);
            } catch (AsaasException $e) {
                report($e);
                $integrationDown = true;
            }
        }

        return view('livewire.billing.index', [
            'organization' => $organization,
            'plan' => $organization->plan,
            'billingStatus' => $organization->billing_status,
            'invoiceUrl' => $invoiceUrl,
            'nextDueDate' => $nextDueDate,
            'payments' => $payments,
            'integrationAvailable' => $integrationAvailable,
            'integrationDown' => $integrationDown,
        ]);
    }

    /**
     * Normaliza a lista de cobranças do Asaas para o que a view precisa.
     *
     * @param  list<array<string, mixed>>  $payments
     * @return list<array{value: float, status: string, dueDate: ?string, invoiceUrl: ?string}>
     */
    private function mapPayments(array $payments): array
    {
        return array_map(fn (array $payment): array => [
            'value' => is_numeric($payment['value'] ?? null) ? (float) $payment['value'] : 0.0,
            'status' => $this->stringValue($payment, 'status') ?? '—',
            'dueDate' => $this->stringValue($payment, 'dueDate'),
            'invoiceUrl' => $this->stringValue($payment, 'invoiceUrl'),
        ], array_slice($payments, 0, 12));
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    private function stringValue(?array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }

    private function currentUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}
