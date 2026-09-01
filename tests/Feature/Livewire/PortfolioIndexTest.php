<?php

declare(strict_types=1);

use App\Enums\RefreshStatus;
use App\Jobs\RefreshMonitoredCompanyJob;
use App\Livewire\Portfolios\Index;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

it('creates a portfolio owned by the current organization', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();

    Livewire::actingAs($user)->test(Index::class)
        ->set('name', 'Fornecedores')
        ->call('create')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('portfolios', ['name' => 'Fornecedores', 'organization_id' => $org->id]);
});

it('validates the portfolio name', function () {
    $user = User::factory()->for(Organization::factory())->create();

    Livewire::actingAs($user)->test(Index::class)
        ->set('name', 'x')
        ->call('create')
        ->assertHasErrors('name');
});

it('lists only portfolios of the current organization', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    Portfolio::factory()->for($org)->create(['name' => 'Minha carteira']);
    Portfolio::factory()->create(['name' => 'Carteira alheia']);

    Livewire::actingAs($user)->test(Index::class)
        ->assertSee('Minha carteira')
        ->assertDontSee('Carteira alheia');
});

it('deletes a portfolio', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();

    Livewire::actingAs($user)->test(Index::class)->call('delete', $portfolio->id);

    $this->assertDatabaseMissing('portfolios', ['id' => $portfolio->id]);
});

it('resets status to pending and queues a refresh per company (from the list)', function () {
    Queue::fake();

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();
    $companies = MonitoredCompany::factory()->count(2)->for($portfolio)->create([
        'last_refresh_status' => RefreshStatus::Ok,
    ]);

    Livewire::actingAs($user)->test(Index::class)->call('queueRefresh', $portfolio->id);

    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 2);

    // O bug: atualizar pela lista não deixava as empresas "pendentes".
    $companies->each(fn ($c) => expect($c->fresh()->last_refresh_status)->toBeNull());
});
