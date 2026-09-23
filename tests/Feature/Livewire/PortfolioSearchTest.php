<?php

declare(strict_types=1);

use App\Livewire\Portfolios\Show;
use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use Livewire\Livewire;

/**
 * @return array{0: User, 1: Portfolio}
 */
function searchScenario(): array
{
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();

    MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '11222333000181', 'label' => 'Padaria Central']);
    MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '99888777000166', 'label' => 'Auto Pecas Norte']);

    return [$user, $portfolio];
}

it('filters companies by TAG (label)', function () {
    [$user, $portfolio] = searchScenario();

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->set('search', 'padaria') // LIKE é case-insensitive
        ->assertSee('Padaria Central')
        ->assertDontSee('Auto Pecas Norte');
});

it('filters companies by CNPJ digits, ignoring punctuation', function () {
    [$user, $portfolio] = searchScenario();

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->set('search', '11.222') // vira dígitos "11222"
        ->assertSee('Padaria Central')
        ->assertDontSee('Auto Pecas Norte');
});

it('shows all companies when the search is empty', function () {
    [$user, $portfolio] = searchScenario();

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->assertSee('Padaria Central')
        ->assertSee('Auto Pecas Norte');
});

it('shows a search-aware empty state when nothing matches', function () {
    [$user, $portfolio] = searchScenario();

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->set('search', 'inexistente-zzz')
        ->assertSee('Nenhuma empresa encontrada')
        ->assertDontSee('Padaria Central');
});

it('filters companies by situação of the latest snapshot', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();

    // Labels e razão social neutros (sem "ativa"/"baixada"): só a SITUAÇÃO pode
    // casar o termo buscado, isolando a feature.
    $ativa = MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '11222333000181', 'label' => 'Alfa Comercio']);
    CompanySnapshot::factory()->for($ativa)->create(['razao_social' => 'ALFA COMERCIO LTDA', 'situacao_cadastral' => 'ATIVA', 'captured_at' => now()]);

    $baixada = MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '99888777000166', 'label' => 'Beta Servicos']);
    CompanySnapshot::factory()->for($baixada)->create(['razao_social' => 'BETA SERVICOS LTDA', 'situacao_cadastral' => 'BAIXADA', 'captured_at' => now()]);

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->set('search', 'baixada') // LIKE case-insensitive; casa só via situação
        ->assertSee('Beta Servicos')
        ->assertDontSee('Alfa Comercio');
});

it('matches situação from the most recent snapshot, not historical ones', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();

    // Era ATIVA, virou BAIXADA: buscar "baixada" acha; "ativa" não (é histórico).
    // Label/razão neutros para que só a situação possa casar.
    $company = MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '11222333000181', 'label' => 'Empresa Que Mudou']);
    CompanySnapshot::factory()->for($company)->create(['razao_social' => 'EMPRESA QUE MUDOU LTDA', 'situacao_cadastral' => 'ATIVA', 'captured_at' => now()->subMonth()]);
    CompanySnapshot::factory()->for($company)->create(['razao_social' => 'EMPRESA QUE MUDOU LTDA', 'situacao_cadastral' => 'BAIXADA', 'captured_at' => now()]);

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->set('search', 'baixada')
        ->assertSee('Empresa Que Mudou');

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->set('search', 'ativa')
        ->assertDontSee('Empresa Que Mudou');
});
