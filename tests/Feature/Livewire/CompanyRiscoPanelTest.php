<?php

declare(strict_types=1);

use App\Livewire\Companies\RiscoPanel;
use App\Models\CompanySnapshot;
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
        ->assertSee('Risco Baixo') // semáforo: ativa (sem snapshot negativo) e sem sanções
        ->assertSee('Nenhuma sanção encontrada');
});

it('flags high risk when an active sanction exists', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(function (Request $request) {
        if (str_contains($request->url(), '/ceis')) {
            return Http::response([[
                'pessoa' => ['cnpjFormatado' => '11.222.333/0001-81'],
                'tipoSancao' => ['descricaoResumida' => 'Empresa inidônea'],
                'dataInicioSancao' => '01/03/2023', // sem data de fim → vigente
            ]]);
        }

        return Http::response([]);
    });

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();

    Livewire::actingAs($user)
        ->test(RiscoPanel::class, ['company' => riscoCompanyFor($org)])
        ->assertSee('Risco Alto')
        ->assertSee('sanção vigente');
});

it('flags medium risk when the company has a negative registration status and nothing on record', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(fn () => Http::response([]));

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = riscoCompanyFor($org);
    CompanySnapshot::factory()->create([
        'monitored_company_id' => $company->id,
        'situacao_cadastral' => 'BAIXADA',
    ]);

    Livewire::actingAs($user)
        ->test(RiscoPanel::class, ['company' => $company])
        ->assertSee('Risco Médio')
        ->assertSee('BAIXADA');
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
                'fundamentacao' => [['descricao' => 'Art. 46 da Lei 8.443/92']],
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
        ->assertSee('impedida de contratar') // explicação em linguagem simples
        ->assertSee('Aplicada por')          // órgão sancionador
        ->assertSee('TCU')
        ->assertSee('Base legal');            // juridiquês fica colapsado no "ver mais"
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
