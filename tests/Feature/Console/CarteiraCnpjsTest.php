<?php

declare(strict_types=1);

use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Artisan;

/** @return list<string> linhas não-vazias da saída do comando */
function carteiraLines(): array
{
    Artisan::call('vigilo:carteira-cnpjs');

    return array_values(array_filter(
        explode("\n", str_replace("\r", '', trim(Artisan::output()))),
        fn (string $l): bool => $l !== '',
    ));
}

it('prints the monitored CNPJs as digits, deduped and sorted', function () {
    $org = Organization::factory()->create();
    $portfolio = Portfolio::factory()->for($org)->create();
    // Mesmo CNPJ nas duas formas (formatado + cru) → tem de sair UMA vez só.
    MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '44.555.666/0001-99']);
    MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '44555666000199']);
    MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '11222333000181']);

    // Ordenado pelos dígitos (112... antes de 445...) e com a duplicata colapsada.
    expect(carteiraLines())->toBe(['11222333000181', '44555666000199']);
});

it('ignores non-CNPJ (CPF-length) documents', function () {
    $org = Organization::factory()->create();
    MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '12345678901']); // 11 díg

    expect(carteiraLines())->toBe([]);
});
