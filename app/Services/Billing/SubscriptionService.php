<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Enums\BillingStatus;
use App\Enums\Plan;
use App\Enums\Role;
use App\Models\Organization;
use App\Services\Asaas\AsaasClient;
use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;

/**
 * Regra de negócio da cobrança recorrente. Reconcilia o estado da organização
 * (plano/quota, já resolvidos na fatia 20) com a assinatura no Asaas e reage aos
 * webhooks de pagamento. Toda escrita em coluna sensível (billing_status,
 * asaas_*, suspended_at, plan) passa por forceFill aqui — nunca por mass
 * assignment.
 */
final class SubscriptionService
{
    public function __construct(private readonly AsaasClient $asaas) {}

    /**
     * Alinha a assinatura no Asaas ao plano atual da organização. Chamado depois
     * de o super-admin trocar o plano. Free (ou preço zero) cancela a cobrança;
     * um plano pago cria/renova a assinatura recorrente mensal.
     */
    public function syncForPlan(Organization $organization, Plan $plan): void
    {
        if ($plan === Plan::Free || $plan->monthlyPrice() <= 0.0) {
            $this->cancel($organization);

            return;
        }

        $customerId = $this->ensureCustomer($organization);

        // Recomeça limpo: um plano diferente muda o valor da recorrência.
        if ($organization->asaas_subscription_id !== null) {
            $this->asaas->cancelSubscription($organization->asaas_subscription_id);
        }

        $trialDays = max(0, (int) config('asaas.trial_days', 0));
        $firstDueDate = CarbonImmutable::now()->addDays($trialDays)->toDateString();

        $subscription = $this->asaas->createSubscription([
            'customer' => $customerId,
            'billingType' => (string) config('asaas.billing_type', 'UNDEFINED'),
            'value' => $plan->monthlyPrice(),
            'cycle' => 'MONTHLY',
            'nextDueDate' => $firstDueDate,
            'description' => "Vigilo — plano {$plan->label()}",
            'externalReference' => (string) $organization->getKey(),
        ]);

        $subscriptionId = $subscription['id'] ?? null;

        if (! is_string($subscriptionId) || $subscriptionId === '') {
            return;
        }

        // Acesso liberado na hora (otimista): o webhook PAYMENT_OVERDUE suspende
        // se a primeira fatura não for paga.
        $this->persist($organization, [
            'asaas_subscription_id' => $subscriptionId,
            'billing_status' => ($trialDays > 0 ? BillingStatus::Trialing : BillingStatus::Active)->value,
            'suspended_at' => null,
        ]);
    }

    /**
     * Processa um evento de webhook do Asaas. Localiza a org pela assinatura e
     * aplica a transição de status. Idempotente — reentregas do mesmo evento
     * chegam ao mesmo estado final.
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleWebhook(string $event, array $payload): void
    {
        $subscriptionId = $this->subscriptionIdFromPayload($payload);

        if ($subscriptionId === null) {
            return;
        }

        $organization = Organization::query()
            ->where('asaas_subscription_id', $subscriptionId)
            ->first();

        if ($organization === null) {
            return;
        }

        match ($event) {
            'PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED' => $this->markPaid($organization),
            'PAYMENT_OVERDUE' => $this->markOverdue($organization),
            // O Asaas já removeu a assinatura — só aplicamos o efeito local, sem
            // chamar a API de volta.
            'SUBSCRIPTION_DELETED' => $this->applyCancellation($organization),
            default => null,
        };
    }

    /**
     * Link de pagamento da fatura em aberto (para a tela de cobrança). Retorna a
     * invoiceUrl da cobrança pendente/vencida mais recente, ou null.
     */
    public function currentInvoiceUrl(Organization $organization): ?string
    {
        $subscriptionId = $organization->asaas_subscription_id;

        if ($subscriptionId === null) {
            return null;
        }

        return $this->pendingInvoiceUrl($this->asaas->listSubscriptionPayments($subscriptionId));
    }

    /**
     * URL da fatura em aberto (pendente/vencida) mais recente a partir de uma
     * lista de cobranças já carregada. Puro (sem I/O), para a tela de billing
     * derivar o link e o histórico de uma única busca.
     *
     * @param  list<array<string, mixed>>  $payments
     */
    public function pendingInvoiceUrl(array $payments): ?string
    {
        foreach ($payments as $payment) {
            $status = is_string($payment['status'] ?? null) ? $payment['status'] : '';

            if (in_array($status, ['PENDING', 'OVERDUE'], true)) {
                $url = $payment['invoiceUrl'] ?? null;

                return is_string($url) ? $url : null;
            }
        }

        return null;
    }

    private function markPaid(Organization $organization): void
    {
        $this->persist($organization, [
            'billing_status' => BillingStatus::Active->value,
            'suspended_at' => null,
        ]);
    }

    private function markOverdue(Organization $organization): void
    {
        $this->persist($organization, [
            'billing_status' => BillingStatus::PastDue->value,
            'suspended_at' => $organization->suspended_at ?? Carbon::now(),
        ]);
    }

    /**
     * Cancela a assinatura no Asaas (quando o downgrade parte de nós) e aplica o
     * efeito local. Um cancelamento originado no Asaas (webhook) chama só
     * applyCancellation, sem bater de volta na API.
     */
    private function cancel(Organization $organization): void
    {
        if ($organization->asaas_subscription_id !== null && $this->asaas->isConfigured()) {
            $this->asaas->cancelSubscription($organization->asaas_subscription_id);
        }

        $this->applyCancellation($organization);
    }

    /**
     * Efeito local do cancelamento: some com o vínculo, rebaixa para Free e
     * devolve o acesso (org não fica suspensa por cobrança inexistente).
     */
    private function applyCancellation(Organization $organization): void
    {
        $wasPaid = $organization->asaas_subscription_id !== null;

        $this->persist($organization, [
            'asaas_subscription_id' => null,
            'billing_status' => ($wasPaid ? BillingStatus::Canceled : BillingStatus::None)->value,
            'plan' => Plan::Free->value,
            'suspended_at' => $organization->isPastDue() ? null : $organization->suspended_at,
        ]);
    }

    /**
     * Garante um customer no Asaas para a organização, criando na primeira vez.
     */
    private function ensureCustomer(Organization $organization): string
    {
        if ($organization->asaas_customer_id !== null) {
            return $organization->asaas_customer_id;
        }

        $customer = $this->asaas->createCustomer([
            'name' => $organization->name,
            'email' => $this->billingEmail($organization),
            'externalReference' => (string) $organization->getKey(),
        ]);

        $customerId = $customer['id'] ?? null;

        if (! is_string($customerId) || $customerId === '') {
            return '';
        }

        $this->persist($organization, ['asaas_customer_id' => $customerId]);

        return $customerId;
    }

    private function billingEmail(Organization $organization): string
    {
        $email = $organization->users()->where('role', Role::Admin->value)->value('email')
            ?? $organization->users()->value('email');

        return is_string($email) && $email !== ''
            ? $email
            : "org-{$organization->getKey()}@vigilo.local";
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function subscriptionIdFromPayload(array $payload): ?string
    {
        $id = data_get($payload, 'payment.subscription')
            ?? data_get($payload, 'subscription.id')
            ?? data_get($payload, 'subscription');

        return is_string($id) && $id !== '' ? $id : null;
    }

    /**
     * Escrita das colunas sensíveis de cobrança — sempre via forceFill.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function persist(Organization $organization, array $attributes): void
    {
        $organization->forceFill($attributes)->save();
    }
}
