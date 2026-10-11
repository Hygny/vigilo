<?php

declare(strict_types=1);

use App\Livewire\Companies\RiscoPanel;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

function riscoCompanyFor(Organization $org, string $cnpj = '11222333000181'): MonitoredCompany
{
    return MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => $cnpj]);
}

it('shows "nothing on record" when the lookup runs clean', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(fn () => Http::response([]));

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();

    Livewire::actingAs($user)
        ->test(RiscoPanel::class, ['company' => riscoCompanyFor($org)])
        ->assertSee('Nada consta')
        ->assertSee('Nenhuma sanção encontrada');
});

it('lists a sanction tied to the CNPJ', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(function (Request $request) {
        if (str_contains($request->url(), '/ceis')) {
            return Http::response([[
                'pessoa' => ['cnpjFormatado' => '11.222.333/0001-81'],
                'tipoSancao' => ['descricaoResumida' => 'Empresa inidônea'],
                'orgaoSancionador' => ['nome' => 'TCU'],
                'dataInicioSancao' => '01/03/2023',
            ]]);
        }

        return Http::response([]);
    });

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();

    Livewire::actingAs($user)
        ->test(RiscoPanel::class, ['company' => riscoCompanyFor($org)])
        ->assertSee('CEIS')
        ->assertSee('Empresa inidônea')
        ->assertSee('TCU');
});

it('degrades gracefully when the integration is not configured', function () {
    config()->set('risco.portal.api_key', null);
    Http::fake();

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();

    Livewire::actingAs($user)
        ->test(RiscoPanel::class, ['company' => riscoCompanyFor($org)])
        ->assertSee('não foi configurada');

    Http::assertNothingSent();
});

it('forbids viewing the risk of a company from another organization', function () {
    $intruder = User::factory()->for(Organization::factory())->create();
    $foreign = riscoCompanyFor(Organization::factory()->create());

    Livewire::actingAs($intruder)
        ->test(RiscoPanel::class, ['company' => $foreign])
        ->assertForbidden();
});
