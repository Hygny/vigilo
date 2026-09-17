<?php

declare(strict_types=1);

use App\Enums\BillingStatus;
use App\Enums\Plan;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Http;

it('forbids a regular member from the billing screen', function () {
    $user = User::factory()->create(); // papel "user"

    $this->actingAs($user)->get(route('billing.index'))->assertForbidden();
});

it('shows the current plan to an organization admin', function () {
    $org = Organization::factory()->create(); // Free (default)
    $admin = User::factory()->for($org)->admin()->create();

    $this->actingAs($admin)->get(route('billing.index'))
        ->assertOk()
        ->assertSee('Assinatura')
        ->assertSee('Free');
});

it('shows the open invoice link when there is a pending charge', function () {
    config()->set('asaas.base_url', 'https://asaas.test/v3');
    config()->set('asaas.api_key', 'sk_test');

    Http::fake([
        '*/v3/subscriptions/sub_1/payments' => Http::response(['data' => [
            ['status' => 'PENDING', 'value' => 149, 'dueDate' => '2026-10-01', 'invoiceUrl' => 'https://pay.asaas.test/abc'],
        ]], 200),
        '*/v3/subscriptions/sub_1' => Http::response(['id' => 'sub_1', 'nextDueDate' => '2026-10-01'], 200),
    ]);

    $org = Organization::factory()->create();
    $org->forceFill([
        'plan' => Plan::Pro->value,
        'asaas_subscription_id' => 'sub_1',
        'billing_status' => BillingStatus::Active->value,
    ])->save();
    $admin = User::factory()->for($org)->admin()->create();

    $this->actingAs($admin)->get(route('billing.index'))
        ->assertOk()
        ->assertSee('Pagar fatura em aberto')
        ->assertSee('https://pay.asaas.test/abc');
});
