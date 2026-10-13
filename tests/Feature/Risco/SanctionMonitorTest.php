<?php

declare(strict_types=1);

use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Services\Risco\SanctionMonitor;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

function sanctionCompany(string $cnpj = '11222333000181'): MonitoredCompany
{
    $org = Organization::factory()->create();

    return MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => $cnpj]);
}

/**
 * @param  list<array<string, mixed>>  $ceis
 */
function fakeCeis(array $ceis): void
{
    Http::fake(function (Request $request) use ($ceis) {
        return str_contains($request->url(), '/ceis')
            ? Http::response($ceis)
            : Http::response([]);
    });
}

/**
 * @return array<string, mixed>
 */
function ceisItem(string $tipo, string $inicio = '01/03/2023'): array
{
    return [
        'pessoa' => ['cnpjFormatado' => '11.222.333/0001-81'],
        'tipoSancao' => ['descricaoResumida' => $tipo],
        'orgaoSancionador' => ['nome' => 'TCU'],
        'dataInicioSancao' => $inicio,
    ];
}

it('baselines on the first sync: stores the sanctions but returns none to alert', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    fakeCeis([ceisItem('Empresa inidônea')]);

    $company = sanctionCompany();
    $novas = app(SanctionMonitor::class)->sync($company);

    expect($novas)->toBe([])
        ->and($company->sanctions()->count())->toBe(1)
        ->and($company->fresh()->sanctions_synced_at)->not->toBeNull();
});

it('does not re-alert a sanction already known', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    fakeCeis([ceisItem('Empresa inidônea')]);

    $company = sanctionCompany();
    $monitor = app(SanctionMonitor::class);

    $monitor->sync($company); // baseline
    Cache::flush();           // o relatório de risco é cacheado 12h
    $novas = $monitor->sync($company); // mesma sanção

    expect($novas)->toBe([])
        ->and($company->sanctions()->count())->toBe(1); // não duplicou
});

it('returns a genuinely new sanction on a later sync', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake([
        '*portaldatransparencia*ceis*' => Http::sequence()
            ->push([ceisItem('Inidônea', '01/03/2023')], 200)                                  // baseline: A
            ->push([ceisItem('Inidônea', '01/03/2023'), ceisItem('Multa', '10/01/2024')], 200), // depois: A + B
        '*' => Http::response([], 200),
    ]);

    $company = sanctionCompany();
    $monitor = app(SanctionMonitor::class);

    expect($monitor->sync($company))->toBe([]); // A baselined
    Cache::flush();
    $novas = $monitor->sync($company);

    expect($novas)->toHaveCount(1)
        ->and($novas[0]->tipo)->toBe('Multa')
        ->and($company->sanctions()->count())->toBe(2);
});

it('does nothing when the source is unavailable (never infers from a failed lookup)', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(fn () => Http::response('erro', 500));

    $company = sanctionCompany();
    $novas = app(SanctionMonitor::class)->sync($company);

    expect($novas)->toBeNull() // null = "não deu para checar" (≠ [] = checado, nada novo)
        ->and($company->sanctions()->count())->toBe(0)
        ->and($company->fresh()->sanctions_synced_at)->toBeNull();
});

it('baselines silently when asked (rebaseline), even with sanctions present', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    fakeCeis([ceisItem('Empresa inidônea')]);

    $company = sanctionCompany();
    $novas = app(SanctionMonitor::class)->sync($company, silent: true);

    expect($novas)->toBe([])
        ->and($company->sanctions()->count())->toBe(1);
});
