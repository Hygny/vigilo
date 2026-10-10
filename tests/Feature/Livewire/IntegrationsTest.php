<?php

declare(strict_types=1);

use App\Livewire\Integrations\Index;
use App\Models\Organization;
use App\Models\User;
use App\Models\WebhookDelivery;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

it('forbids a non-admin from the integrations screen', function () {
    $user = User::factory()->for(Organization::factory())->create();

    $this->actingAs($user)->get(route('integrations.index'))->assertForbidden();
});

it('lets an admin save the webhook config', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)->test(Index::class)
        ->set('webhookUrl', 'https://consumidor.test/hook')
        ->set('webhookSecret', 'segredo-super-secreto-123')
        ->call('save')
        ->assertHasNoErrors();

    $org = $admin->organization->fresh();
    expect($org->webhook_url)->toBe('https://consumidor.test/hook')
        ->and($org->webhook_secret)->toBe('segredo-super-secreto-123')
        ->and($org->hasWebhook())->toBeTrue();
});

it('requires a secret when setting a url', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)->test(Index::class)
        ->set('webhookUrl', 'https://consumidor.test/hook')
        ->set('webhookSecret', '')
        ->call('save')
        ->assertHasErrors('webhookSecret');

    expect($admin->organization->fresh()->webhook_url)->toBeNull();
});

it('keeps the existing secret when the field is left blank', function () {
    $admin = User::factory()->admin()->create();
    $admin->organization->forceFill(['webhook_url' => 'https://old.test/hook', 'webhook_secret' => 'antigo-super-secreto'])->save();

    Livewire::actingAs($admin)->test(Index::class)
        ->set('webhookUrl', 'https://new.test/hook')
        ->set('webhookSecret', '')
        ->call('save')
        ->assertHasNoErrors();

    $org = $admin->organization->fresh();
    expect($org->webhook_url)->toBe('https://new.test/hook')
        ->and($org->webhook_secret)->toBe('antigo-super-secreto');
});

it('disables the webhook when the url is cleared', function () {
    $admin = User::factory()->admin()->create();
    $admin->organization->forceFill(['webhook_url' => 'https://old.test/hook', 'webhook_secret' => 'antigo-super-secreto'])->save();

    Livewire::actingAs($admin)->test(Index::class)
        ->set('webhookUrl', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($admin->organization->fresh()->hasWebhook())->toBeFalse();
});

it('sends a signed test delivery', function () {
    Http::fake(['https://consumidor.test/*' => Http::response('', 200)]);
    $admin = User::factory()->admin()->create();
    $admin->organization->forceFill(['webhook_url' => 'https://consumidor.test/hook', 'webhook_secret' => 'segredo-super-secreto'])->save();

    Livewire::actingAs($admin)->test(Index::class)->call('sendTest')->assertHasNoErrors();

    Http::assertSent(fn ($request): bool => $request->url() === 'https://consumidor.test/hook' && $request->hasHeader('X-Vigilo-Event', 'test'));

    expect(WebhookDelivery::query()->where('organization_id', $admin->organization->id)->where('event_type', 'test')->where('status', 'success')->count())->toBe(1);
});

it('rate-limits the test delivery to curb flooding', function () {
    Http::fake(['https://consumidor.test/*' => Http::response('', 200)]);
    $admin = User::factory()->admin()->create();
    $admin->organization->forceFill(['webhook_url' => 'https://consumidor.test/hook', 'webhook_secret' => 'segredo-super-secreto'])->save();

    $component = Livewire::actingAs($admin)->test(Index::class);

    for ($i = 0; $i < 5; $i++) {
        $component->call('sendTest')->assertHasNoErrors();
    }

    $component->call('sendTest')->assertHasErrors('webhookUrl'); // 6ª bloqueada

    expect(WebhookDelivery::query()->where('organization_id', $admin->organization->id)->count())->toBe(5);
});

it('rejects a non-https webhook url', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)->test(Index::class)
        ->set('webhookUrl', 'http://consumidor.test/hook')
        ->set('webhookSecret', 'segredo-super-secreto-123')
        ->call('save')
        ->assertHasErrors('webhookUrl');

    expect($admin->organization->fresh()->webhook_url)->toBeNull();
});

it('rejects a loopback/private webhook url (SSRF guard)', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)->test(Index::class)
        ->set('webhookUrl', 'https://127.0.0.1/hook')
        ->set('webhookSecret', 'segredo-super-secreto-123')
        ->call('save')
        ->assertHasErrors('webhookUrl');

    expect($admin->organization->fresh()->webhook_url)->toBeNull();
});

it('generates a 40-char secret', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)->test(Index::class)
        ->call('generateSecret')
        ->assertSet('webhookSecret', fn ($value): bool => is_string($value) && strlen($value) === 40);
});
