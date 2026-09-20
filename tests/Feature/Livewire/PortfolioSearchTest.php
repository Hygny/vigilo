<?php

declare(strict_types=1);

use App\Livewire\Portfolios\Show;
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
