<?php

declare(strict_types=1);

use App\Services\Webhooks\Exceptions\BlockedWebhookDestination;
use App\Services\Webhooks\WebhookSender;
use Illuminate\Support\Facades\Http;

function sendTo(string $url): void
{
    (new WebhookSender)->send($url, 'segredo-super-secreto', ['event_id' => 'e1', 'event' => 'test']);
}

it('blocks a destination that is a private/reserved IP (anti-SSRF at send)', function (string $url) {
    config()->set('webhook.verify_destination_ip', true);
    Http::fake(); // não deve nem chegar a enviar

    expect(fn () => sendTo($url))->toThrow(BlockedWebhookDestination::class);

    Http::assertNothingSent();
})->with([
    'https://127.0.0.1/hook',
    'https://10.0.0.5/hook',
    'https://169.254.169.254/hook', // metadata link-local
]);

it('allows a public IP destination', function () {
    config()->set('webhook.verify_destination_ip', true);
    Http::fake(['https://8.8.8.8/*' => Http::response('', 200)]);

    sendTo('https://8.8.8.8/hook');

    Http::assertSent(fn ($request): bool => $request->url() === 'https://8.8.8.8/hook');
});

it('skips the destination check when disabled by config', function () {
    config()->set('webhook.verify_destination_ip', false);
    Http::fake(['https://127.0.0.1/*' => Http::response('', 200)]);

    sendTo('https://127.0.0.1/hook'); // não lança — checagem desligada

    Http::assertSent(fn ($request): bool => $request->url() === 'https://127.0.0.1/hook');
});

it('signs the body and sets the event headers', function () {
    config()->set('webhook.verify_destination_ip', false);
    Http::fake(['https://8.8.8.8/*' => Http::response('', 200)]);

    sendTo('https://8.8.8.8/hook');

    Http::assertSent(fn ($request): bool => $request->hasHeader('X-Vigilo-Signature')
        && $request->hasHeader('X-Vigilo-Event-Id', 'e1')
        && $request->hasHeader('X-Vigilo-Event', 'test'));
});
