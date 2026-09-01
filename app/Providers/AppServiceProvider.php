<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\CnpjDataProvider;
use App\Providers\Cnpj\BrasilApiProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CnpjDataProvider::class, function (): CnpjDataProvider {
            /** @var string $driver */
            $driver = config('cnpj.driver');

            return match ($driver) {
                'brasilapi' => new BrasilApiProvider(
                    baseUrl: (string) config('cnpj.providers.brasilapi.base_url'),
                    timeout: (int) config('cnpj.providers.brasilapi.timeout'),
                    tries: (int) config('cnpj.providers.brasilapi.tries'),
                    retryBackoffMs: (int) config('cnpj.providers.brasilapi.retry_backoff_ms'),
                    throttlePerMinute: (int) config('cnpj.throttle.requests_per_minute'),
                ),
                default => throw new InvalidArgumentException("Driver de CNPJ não suportado: [{$driver}]."),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Caps how many company refreshes may run per minute across all workers,
        // protecting the upstream CNPJ provider from bursts. 0 desativa o teto.
        RateLimiter::for('cnpj-refresh', function (): Limit {
            $perMinute = (int) config('cnpj.throttle.requests_per_minute');

            return $perMinute > 0 ? Limit::perMinute($perMinute) : Limit::none();
        });

        // Per-consumer cap on the public read API (GET /api/cnpj/{cnpj}),
        // keyed by the authenticated token owner.
        RateLimiter::for('cnpj-api', function (Request $request): Limit {
            $perMinute = (int) config('cnpj.api.rate_per_minute');
            $key = (string) ($request->user()?->getAuthIdentifier() ?? $request->ip());

            return $perMinute > 0 ? Limit::perMinute($perMinute)->by($key) : Limit::none();
        });
    }
}
