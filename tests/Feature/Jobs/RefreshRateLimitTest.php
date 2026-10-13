<?php

declare(strict_types=1);

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Cache\RateLimiting\Unlimited;
use Illuminate\Support\Facades\RateLimiter;

it('does not throttle the refresh when the CNPJ base is local (no upstream to protect)', function () {
    config()->set('cnpj.driver', 'local');
    config()->set('cnpj.throttle.requests_per_minute', 20);

    expect(RateLimiter::limiter('cnpj-refresh')())->toBeInstanceOf(Unlimited::class);
});

it('throttles the refresh when the CNPJ provider is external', function () {
    config()->set('cnpj.driver', 'brasilapi');
    config()->set('cnpj.throttle.requests_per_minute', 20);

    $limit = RateLimiter::limiter('cnpj-refresh')();

    expect($limit)->toBeInstanceOf(Limit::class)
        ->not->toBeInstanceOf(Unlimited::class)
        ->and($limit->maxAttempts)->toBe(20);
});
