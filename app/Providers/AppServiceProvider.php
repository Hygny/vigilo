<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\CnpjDataProvider;
use App\Providers\Cnpj\BrasilApiProvider;
use App\Providers\Cnpj\LocalCnpjProvider;
use App\Services\Admin\StatusRotinas;
use App\Services\Asaas\AsaasClient;
use App\Services\Contratos\ContratosPublicos;
use App\Services\Graph\OwnershipGraphService;
use App\Services\Risco\DirtyListLookup;
use App\Services\Risco\PgfnLookup;
use App\Services\Risco\PortalTransparenciaClient;
use App\Services\Risco\RiscoAoRedor;
use App\Services\Risco\RiscoService;
use App\Services\Risco\SancoesLocais;
use App\Services\Vigilancia\BuscaEmpresas;
use App\Services\Vigilancia\BuscaPessoas;
use App\Services\Vigilancia\DetalhesEmpresaLookup;
use App\Services\Vigilancia\EmpresaLookup;
use App\Services\Vigilancia\HistoricoLookup;
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
                'local' => new LocalCnpjProvider(
                    connection: (string) config('cnpj.providers.local.connection', 'cnpj'),
                ),
                default => throw new InvalidArgumentException("Driver de CNPJ não suportado: [{$driver}]."),
            };
        });

        $this->app->singleton(AsaasClient::class, function (): AsaasClient {
            $apiKey = config('asaas.api_key');

            return new AsaasClient(
                baseUrl: (string) config('asaas.base_url'),
                apiKey: is_string($apiKey) ? $apiKey : null,
                timeout: (int) config('asaas.timeout'),
            );
        });

        $this->app->singleton(OwnershipGraphService::class, fn (): OwnershipGraphService => new OwnershipGraphService(
            connection: (string) config('cnpj.providers.local.connection', 'cnpj'),
            reverseLimit: (int) config('cnpj.graph.reverse_limit', 25),
            maxDepth: (int) config('cnpj.graph.max_depth', 5),
            maxCompanies: (int) config('cnpj.graph.max_companies', 300),
            maxBranches: (int) config('cnpj.graph.branch_limit', 60),
            addressLimit: (int) config('cnpj.graph.address_limit', 25),
            addressCrowdedMax: (int) config('cnpj.graph.address_crowded_max', 6),
        ));

        $this->app->singleton(PgfnLookup::class, fn (): PgfnLookup => new PgfnLookup(
            connection: (string) config('cnpj.providers.local.connection', 'cnpj'),
        ));

        $this->app->singleton(BuscaEmpresas::class, fn (): BuscaEmpresas => new BuscaEmpresas(
            connection: (string) config('cnpj.providers.local.connection', 'cnpj'),
            candidatos: max(1, (int) config('cnpj.busca.candidatos', 200)),
            timeoutMs: (int) config('cnpj.busca.timeout_ms', 5000),
        ));

        $this->app->singleton(StatusRotinas::class, fn (): StatusRotinas => new StatusRotinas(
            sancoes: $this->app->make(SancoesLocais::class),
            connection: (string) config('cnpj.providers.local.connection', 'cnpj'),
        ));

        $this->app->singleton(ContratosPublicos::class, fn (): ContratosPublicos => new ContratosPublicos(
            portal: $this->app->make(PortalTransparenciaClient::class),
            maxPaginas: max(1, (int) config('risco.contratos.max_paginas', 40)),
            cacheMinutos: (int) config('risco.contratos.cache_minutos', 1440),
            falhaMinutos: (int) config('risco.contratos.falha_minutos', 5),
            prazoSegundos: (int) config('risco.contratos.prazo_segundos', 20),
        ));

        $this->app->singleton(RiscoAoRedor::class, fn (): RiscoAoRedor => new RiscoAoRedor(
            grafo: $this->app->make(OwnershipGraphService::class),
            pgfn: $this->app->make(PgfnLookup::class),
            sancoes: $this->app->make(SancoesLocais::class),
            listaSuja: $this->app->make(DirtyListLookup::class),
            connection: (string) config('cnpj.providers.local.connection', 'cnpj'),
            cacheMinutos: (int) config('risco.ao_redor.cache_minutos', 10),
        ));

        $this->app->singleton(DetalhesEmpresaLookup::class, fn (): DetalhesEmpresaLookup => new DetalhesEmpresaLookup(
            connection: (string) config('cnpj.providers.local.connection', 'cnpj'),
        ));

        $this->app->singleton(BuscaPessoas::class, fn (): BuscaPessoas => new BuscaPessoas(
            connection: (string) config('cnpj.providers.local.connection', 'cnpj'),
            candidatos: max(1, (int) config('cnpj.pessoas.candidatos', 200)),
            timeoutMs: (int) config('cnpj.pessoas.timeout_ms', 5000),
            maxEmpresas: max(1, (int) config('cnpj.pessoas.max_empresas', 200)),
        ));

        $this->app->singleton(HistoricoLookup::class, fn (): HistoricoLookup => new HistoricoLookup(
            connection: (string) config('cnpj.providers.local.connection', 'cnpj'),
        ));

        $this->app->singleton(EmpresaLookup::class, fn (): EmpresaLookup => new EmpresaLookup(
            connection: (string) config('cnpj.providers.local.connection', 'cnpj'),
            pgfn: $this->app->make(PgfnLookup::class),
        ));

        $this->app->singleton(PortalTransparenciaClient::class, function (): PortalTransparenciaClient {
            $apiKey = config('risco.portal.api_key');

            return new PortalTransparenciaClient(
                baseUrl: (string) config('risco.portal.base_url'),
                apiKey: is_string($apiKey) ? $apiKey : null,
                timeout: (int) config('risco.portal.timeout'),
            );
        });

        $this->app->singleton(RiscoService::class, fn (): RiscoService => new RiscoService(
            client: $this->app->make(PortalTransparenciaClient::class),
            cacheTtlMinutes: (int) config('risco.cache_ttl', 720),
        ));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Caps how many company refreshes may run per minute across all workers,
        // protecting an EXTERNAL CNPJ provider (BrasilAPI) from bursts. Com a base
        // local (CNPJ_DRIVER=local) o refresh só lê dado local em milissegundos e
        // não há upstream a proteger — o teto só segurava a fila (era o gargalo do
        // lote). A CGU, que é externa, saiu do refresh (varredura diária própria).
        // 0 desativa o teto também no modo externo.
        RateLimiter::for('cnpj-refresh', function (): Limit {
            if (config('cnpj.driver') === 'local') {
                return Limit::none();
            }

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

        // Per-consumer cap on the OSINT API (/api/v1/vigilancia), keyed by the
        // authenticated token owner. 0 desativa o teto.
        RateLimiter::for('vigilancia-api', function (Request $request): Limit {
            $perMinute = (int) config('vigilancia.rate_per_minute');
            $key = (string) ($request->user()?->getAuthIdentifier() ?? $request->ip());

            return $perMinute > 0 ? Limit::perMinute($perMinute)->by($key) : Limit::none();
        });
    }
}
