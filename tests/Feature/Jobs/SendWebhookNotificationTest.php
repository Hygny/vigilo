<?php

declare(strict_types=1);

use App\Jobs\SendWebhookNotification;
use App\Models\Organization;
use App\Models\WebhookDelivery;
use App\Services\Webhooks\WebhookSender;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Support\Facades\Http;

function webhookOrg(string $secret = 'segredo-super-secreto-123'): Organization
{
    $org = Organization::factory()->create();
    $org->forceFill(['webhook_url' => 'https://consumidor.test/hook', 'webhook_secret' => $secret])->save();

    return $org;
}

function samplePayload(): array
{
    return [
        'event_id' => 'evt-1',
        'event' => 'company.changes',
        'cnpj' => '11222333000181',
        'razao_social' => 'EMPRESA EXEMPLO',
        'mudancas' => [['tipo' => 'situacao_changed', 'campo' => 'situacao_cadastral', 'de' => 'ATIVA', 'para' => 'BAIXADA', 'severidade' => 'critical']],
    ];
}

it('delivers a signed payload and records success', function () {
    Http::fake(['https://consumidor.test/*' => Http::response('', 200)]);
    $org = webhookOrg();

    (new SendWebhookNotification($org->id, samplePayload()))->handle(app(WebhookSender::class));

    Http::assertSent(function (Request $request) {
        $expected = 'sha256='.hash_hmac('sha256', $request->body(), 'segredo-super-secreto-123');

        return $request->url() === 'https://consumidor.test/hook'
            && $request->hasHeader('X-Vigilo-Signature', $expected)
            && $request->hasHeader('X-Vigilo-Event-Id', 'evt-1');
    });

    $delivery = WebhookDelivery::query()->where('organization_id', $org->id)->sole();
    expect($delivery->status)->toBe('success')
        ->and($delivery->response_status)->toBe(200)
        ->and($delivery->cnpj)->toBe('11222333000181')
        ->and($delivery->changes_count)->toBe(1);
});

it('does nothing when the organization has no webhook', function () {
    Http::fake();
    $org = Organization::factory()->create();

    (new SendWebhookNotification($org->id, samplePayload()))->handle(app(WebhookSender::class));

    Http::assertNothingSent();
    expect(WebhookDelivery::query()->count())->toBe(0);
});

it('throws on a non-2xx response so the job retries', function () {
    Http::fake(['https://consumidor.test/*' => Http::response('erro', 500)]);
    $org = webhookOrg();

    expect(fn () => (new SendWebhookNotification($org->id, samplePayload()))->handle(app(WebhookSender::class)))
        ->toThrow(RequestException::class);
});

it('fails permanently (no retry) when the destination resolves to a private IP', function () {
    config()->set('webhook.verify_destination_ip', true);
    Http::fake();
    $org = Organization::factory()->create();
    $org->forceFill(['webhook_url' => 'https://127.0.0.1/hook', 'webhook_secret' => 'segredo-super-secreto-123'])->save();

    // Não propaga a exceção (erro permanente → fail(), sem retry) nem chega a enviar.
    (new SendWebhookNotification($org->id, samplePayload()))->handle(app(WebhookSender::class));

    Http::assertNothingSent();
});

it('records a failed delivery on final failure', function () {
    $org = webhookOrg();
    $exception = new RequestException(new ClientResponse(new PsrResponse(500, [], 'erro')));

    (new SendWebhookNotification($org->id, samplePayload()))->failed($exception);

    $delivery = WebhookDelivery::query()->where('organization_id', $org->id)->sole();
    expect($delivery->status)->toBe('failed')
        ->and($delivery->response_status)->toBe(500);
});
