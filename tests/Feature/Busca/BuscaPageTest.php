<?php

declare(strict_types=1);

use App\Livewire\Empresas\Busca;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

function buscaUser(): User
{
    return User::factory()->for(Organization::factory())->create();
}

beforeEach(function (): void {
    seedBuscaBase([
        ['11222333000181', 'PADARIA AURORA LTDA', 'PAO DA AURORA', '02', 'SP'],
        ['44555666000107', 'MARIA SOUZA 12345678901', null, '08', 'RJ'],
    ]);
});

it('shows the search page from the navigation', function () {
    $this->actingAs(buscaUser())
        ->get(route('empresas.busca'))
        ->assertOk()
        ->assertSee('Busca de empresas');
});

it('searches by name and links each result to the company profile', function () {
    Livewire::actingAs(buscaUser())->test(Busca::class)
        ->set('termo', 'aurora')
        ->call('buscar')
        ->assertSet('consulta', 'aurora')
        ->assertSee('PADARIA AURORA LTDA')
        ->assertSee('11.222.333/0001-81')
        ->assertSee(route('empresas.perfil', '11222333000181'), false);
});

it('applies the UF and situação filters', function () {
    Livewire::actingAs(buscaUser())->test(Busca::class)
        ->set('termo', 'maria souza')
        ->call('buscar')
        ->assertSee('MARIA SOUZA ***456789**') // CPF do MEI mascarado
        ->assertDontSee('12345678901')
        ->set('situacao', 'ATIVA')
        ->assertDontSee('MARIA SOUZA')
        ->set('situacao', '')
        ->set('uf', 'SP')
        ->assertDontSee('MARIA SOUZA');
});

it('goes straight to the profile when a CNPJ is typed', function () {
    Livewire::actingAs(buscaUser())->test(Busca::class)
        ->set('termo', '11.222.333/0001-81')
        ->call('buscar')
        ->assertRedirect(route('empresas.perfil', '11222333000181'));
});

it('refuses to search by CPF', function () {
    Livewire::actingAs(buscaUser())->test(Busca::class)
        ->set('termo', '123.456.789-01')
        ->call('buscar')
        ->assertSee('Busca por CPF não é permitida')
        ->assertDontSee('MARIA SOUZA');
});

it('asks for a longer term instead of searching', function () {
    Livewire::actingAs(buscaUser())->test(Busca::class)
        ->set('termo', 'ab')
        ->call('buscar')
        ->assertSee('ao menos uma palavra com 3 letras');
});

it('rate-limits searches per user', function () {
    config()->set('cnpj.busca.per_minute', 2);
    $user = buscaUser();
    RateLimiter::clear('busca-empresas:'.$user->getKey());

    $page = Livewire::actingAs($user)->test(Busca::class)->set('termo', 'aurora');
    $page->call('buscar')->assertSee('PADARIA AURORA LTDA');
    $page->set('uf', 'SP')->assertSee('PADARIA AURORA LTDA'); // 2ª busca (filtro)
    $page->set('uf', '')->assertSee('Muitas buscas em pouco tempo')->assertDontSee('PADARIA AURORA LTDA');
});

it('degrades with a notice when the CNPJ base is down', function () {
    useCnpjSqlite(); // base sem tabelas → QueryException

    Livewire::actingAs(buscaUser())->test(Busca::class)
        ->set('termo', 'aurora')
        ->call('buscar')
        ->assertSee('Base CNPJ indisponível');
});

it('requires an organization and a login', function () {
    $this->get(route('empresas.busca'))->assertRedirect(route('login'));

    $semOrg = User::factory()->superAdmin()->create();
    $this->actingAs($semOrg)->get(route('empresas.busca'))->assertForbidden();
});
