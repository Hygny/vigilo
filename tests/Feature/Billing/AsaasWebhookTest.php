<?php

declare(strict_types=1);

use App\Enums\BillingStatus;
use App\Enums\Plan;
use App\Models\Organization;

beforeEach(function () {
    config()->set('asaas.webhook_token', 'wh_secret');
});

/**
 * @param  array<string, mixed>  $payload
 */
function postAsaasWebhook(array $payload, ?string $token = 'wh_secret')
{
    $headers = $token === null ? [] : ['asaas-access-token' => $token];

    return test()->postJson('/webhooks/asaas', $payload, $headers);
}

function orgWithSubscription(string $subscriptionId = 'sub_1', array $attributes = []): Organization
{
    $org = Organization::factory()->create();

    $org->forceFill(array_merge([
        'asaas_subscription_id' => $subscriptionId,
        'billing_status' => BillingStatus::Active->value,
    ], $attributes))->save();

    return $org;
}

it('rejects a webhook without the shared token', function () {
    postAsaasWebhook(['event' => 'PAYMENT_CONFIRMED', 'payment' => ['subscription' => 'sub_1']], token: null)
        ->assertForbidden();
});

it('rejects a webhook with a wrong token', function () {
    postAsaasWebhook(['event' => 'PAYMENT_CONFIRMED', 'payment' => ['subscription' => 'sub_1']], token: 'errado')
        ->assertForbidden();
});

it('confirms payment: reactivates the organization and marks it active', function () {
    $org = orgWithSubscription('sub_1', [
        'billing_status' => BillingStatus::PastDue->value,
        'suspended_at' => now(),
    ]);

    postAsaasWebhook(['event' => 'PAYMENT_CONFIRMED', 'payment' => ['subscription' => 'sub_1']])
        ->assertNoContent();

    $fresh = $org->fresh();

    expect($fresh->billing_status)->toBe(BillingStatus::Active)
        ->and($fresh->suspended_at)->toBeNull();
});

it('overdue payment: suspends the organization and marks it past due', function () {
    $org = orgWithSubscription('sub_1');

    postAsaasWebhook(['event' => 'PAYMENT_OVERDUE', 'payment' => ['subscription' => 'sub_1']])
        ->assertNoContent();

    $fresh = $org->fresh();

    expect($fresh->billing_status)->toBe(BillingStatus::PastDue)
        ->and($fresh->isSuspended())->toBeTrue();
});

it('subscription deleted: downgrades to Free without calling Asaas back', function () {
    $org = orgWithSubscription('sub_1', ['plan' => Plan::Business->value]);

    postAsaasWebhook(['event' => 'SUBSCRIPTION_DELETED', 'subscription' => ['id' => 'sub_1']])
        ->assertNoContent();

    $fresh = $org->fresh();

    expect($fresh->plan)->toBe(Plan::Free)
        ->and($fresh->asaas_subscription_id)->toBeNull()
        ->and($fresh->billing_status)->toBe(BillingStatus::Canceled);
});

it('ignores an event for an unknown subscription', function () {
    postAsaasWebhook(['event' => 'PAYMENT_CONFIRMED', 'payment' => ['subscription' => 'desconhecida']])
        ->assertNoContent();

    // Nenhuma exceção, nenhuma mudança — só ignora.
    expect(Organization::query()->where('billing_status', BillingStatus::Active->value)->count())->toBe(0);
});
