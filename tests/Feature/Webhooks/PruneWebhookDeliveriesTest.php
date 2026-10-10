<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\WebhookDelivery;

it('prunes webhook deliveries older than the retention window', function () {
    config()->set('webhook.deliveries_retention_days', 90);
    $org = Organization::factory()->create();

    $old = WebhookDelivery::query()->create([
        'organization_id' => $org->id, 'event_id' => 'old', 'event_type' => 'test',
        'changes_count' => 0, 'status' => 'success',
    ]);
    $old->forceFill(['created_at' => now()->subDays(100)])->save();

    $recent = WebhookDelivery::query()->create([
        'organization_id' => $org->id, 'event_id' => 'new', 'event_type' => 'test',
        'changes_count' => 0, 'status' => 'success',
    ]);

    $this->artisan('model:prune', ['--model' => [WebhookDelivery::class]])->assertSuccessful();

    expect(WebhookDelivery::query()->whereKey($old->id)->exists())->toBeFalse()
        ->and(WebhookDelivery::query()->whereKey($recent->id)->exists())->toBeTrue();
});
