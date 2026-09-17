<?php

declare(strict_types=1);

use App\Enums\BillingStatus;
use App\Enums\Plan;
use App\Models\Organization;
use App\Models\User;
use App\Services\Asaas\Exceptions\AsaasException;
use App\Services\Billing\SubscriptionService;
use Illuminate\Support\Facades\Http;

/**
 * Configura a integração Asaas (com host de teste) e devolve o serviço já
 * resolvido do container, para os testes que precisam falar com a "API".
 */
function billingService(): SubscriptionService
{
    config()->set('asaas.base_url', 'https://asaas.test/v3');
    config()->set('asaas.api_key', 'sk_test');
    config()->set('asaas.timeout', 5);

    return app(SubscriptionService::class);
}

it('creates a customer and a monthly subscription when activating a paid plan', function () {
    config()->set('asaas.trial_days', 0);
    config()->set('plans.pro.price', 149);

    Http::fake([
        '*/v3/customers' => Http::response(['id' => 'cus_1'], 200),
        '*/v3/subscriptions' => Http::response(['id' => 'sub_1'], 200),
    ]);

    $org = Organization::factory()->create();
    User::factory()->for($org)->admin()->create(['email' => 'dono@org.com']);

    billingService()->syncForPlan($org, Plan::Pro);

    $fresh = $org->fresh();

    expect($fresh->asaas_customer_id)->toBe('cus_1')
        ->and($fresh->asaas_subscription_id)->toBe('sub_1')
        ->and($fresh->billing_status)->toBe(BillingStatus::Active);

    Http::assertSent(fn ($request) => str_ends_with($request->url(), '/v3/subscriptions')
        && $request['value'] === 149.0
        && $request['cycle'] === 'MONTHLY'
        && $request['customer'] === 'cus_1');
});

it('marks the subscription as trialing when a trial is configured', function () {
    config()->set('asaas.trial_days', 7);
    config()->set('plans.pro.price', 149);

    Http::fake([
        '*/v3/customers' => Http::response(['id' => 'cus_1'], 200),
        '*/v3/subscriptions' => Http::response(['id' => 'sub_1'], 200),
    ]);

    $org = Organization::factory()->create();
    User::factory()->for($org)->admin()->create();

    billingService()->syncForPlan($org, Plan::Pro);

    expect($org->fresh()->billing_status)->toBe(BillingStatus::Trialing);
});

it('cancels the Asaas subscription and downgrades to Free when moving to the Free plan', function () {
    Http::fake([
        '*/v3/subscriptions/*' => Http::response([], 200),
    ]);

    $org = Organization::factory()->create();
    $org->forceFill([
        'plan' => Plan::Pro->value,
        'asaas_customer_id' => 'cus_1',
        'asaas_subscription_id' => 'sub_1',
        'billing_status' => BillingStatus::Active->value,
    ])->save();

    billingService()->syncForPlan($org, Plan::Free);

    $fresh = $org->fresh();

    expect($fresh->asaas_subscription_id)->toBeNull()
        ->and($fresh->billing_status)->toBe(BillingStatus::Canceled)
        ->and($fresh->plan)->toBe(Plan::Free);

    Http::assertSent(fn ($request) => $request->method() === 'DELETE'
        && str_ends_with($request->url(), '/v3/subscriptions/sub_1'));
});

it('throws when activating a paid plan without the Asaas credential configured', function () {
    config()->set('asaas.api_key', null);
    config()->set('plans.pro.price', 149);

    $service = app(SubscriptionService::class);
    $org = Organization::factory()->create();
    User::factory()->for($org)->admin()->create();

    expect(fn () => $service->syncForPlan($org, Plan::Pro))
        ->toThrow(AsaasException::class);
});
