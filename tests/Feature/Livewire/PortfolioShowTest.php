<?php

declare(strict_types=1);

use App\Enums\RefreshStatus;
use App\Jobs\RefreshMonitoredCompanyJob;
use App\Livewire\Portfolios\Show;
use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\PortfolioScheduledRun;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

function ownedPortfolio(): array
{
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();

    return [$user, $portfolio];
}

it('adds a company by CNPJ', function () {
    [$user, $portfolio] = ownedPortfolio();

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->set('cnpj', '11.222.333/0001-81')
        ->set('label', 'Fornecedor X')
        ->call('addCompany')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('monitored_companies', [
        'portfolio_id' => $portfolio->id,
        'cnpj' => '11222333000181',
        'label' => 'Fornecedor X',
    ]);
});

it('rejects an invalid CNPJ when adding a company', function () {
    [$user, $portfolio] = ownedPortfolio();

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->set('cnpj', '123')
        ->call('addCompany')
        ->assertHasErrors('cnpj');

    expect($portfolio->monitoredCompanies()->count())->toBe(0);
});

it('imports companies from a CSV upload and reports the outcome', function () {
    [$user, $portfolio] = ownedPortfolio();

    $file = UploadedFile::fake()->createWithContent(
        'companies.csv',
        "cnpj,label\n11.222.333/0001-81,Cliente A\n123,Invalido\n",
    );

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->set('csv', $file)
        ->call('import')
        ->assertHasNoErrors()
        ->assertViewIs('livewire.portfolios.show');

    $this->assertDatabaseHas('monitored_companies', [
        'portfolio_id' => $portfolio->id,
        'cnpj' => '11222333000181',
    ]);

    expect($portfolio->monitoredCompanies()->count())->toBe(1);
});

it('queues a refresh for a single company', function () {
    Queue::fake();
    [$user, $portfolio] = ownedPortfolio();
    $company = $portfolio->monitoredCompanies()->create(['cnpj' => '11222333000181']);

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->call('queueRefresh', $company->id);

    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 1);
});

it('reprocesses only the companies with errors when that filter is active', function () {
    Queue::fake();
    [$user, $portfolio] = ownedPortfolio();

    $ok = $portfolio->monitoredCompanies()->create(['cnpj' => '11222333000181', 'last_refresh_status' => RefreshStatus::Ok]);
    $errored = $portfolio->monitoredCompanies()->create(['cnpj' => '11444777000161', 'last_refresh_status' => RefreshStatus::Error, 'last_refresh_error' => 'boom']);
    $notFound = $portfolio->monitoredCompanies()->create(['cnpj' => '01234567800043', 'last_refresh_status' => RefreshStatus::NotFound]);

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->call('setStatusFilter', 'erro')
        ->call('reprocessFiltered');

    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 1); // só o com erro

    expect($errored->fresh()->last_refresh_status)->toBeNull()
        ->and($errored->fresh()->last_refresh_error)->toBeNull()
        ->and($ok->fresh()->last_refresh_status)->toBe(RefreshStatus::Ok)
        ->and($notFound->fresh()->last_refresh_status)->toBe(RefreshStatus::NotFound);
});

it('reprocesses only the not-found companies when that filter is active', function () {
    Queue::fake();
    [$user, $portfolio] = ownedPortfolio();

    $errored = $portfolio->monitoredCompanies()->create(['cnpj' => '11444777000161', 'last_refresh_status' => RefreshStatus::Error]);
    $notFound = $portfolio->monitoredCompanies()->create(['cnpj' => '01234567800043', 'last_refresh_status' => RefreshStatus::NotFound]);

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->call('setStatusFilter', 'nao_encontrado')
        ->call('reprocessFiltered');

    Queue::assertPushed(RefreshMonitoredCompanyJob::class, 1);

    expect($notFound->fresh()->last_refresh_status)->toBeNull()
        ->and($errored->fresh()->last_refresh_status)->toBe(RefreshStatus::Error);
});

it('filters the companies list by status', function () {
    [$user, $portfolio] = ownedPortfolio();
    $portfolio->monitoredCompanies()->create(['cnpj' => '11222333000181', 'last_refresh_status' => RefreshStatus::Ok, 'label' => 'EMPRESA-OK']);
    $portfolio->monitoredCompanies()->create(['cnpj' => '11444777000161', 'last_refresh_status' => RefreshStatus::Error, 'label' => 'EMPRESA-ERRO']);

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->call('setStatusFilter', 'erro')
        ->assertViewHas('companies', fn ($companies) => $companies->count() === 1 && $companies->first()->label === 'EMPRESA-ERRO')
        ->assertSee('EMPRESA-ERRO')
        ->assertDontSee('EMPRESA-OK');
});

it('ignores an unknown status filter', function () {
    [$user, $portfolio] = ownedPortfolio();

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->call('setStatusFilter', 'xpto')
        ->assertSet('statusFilter', 'all');
});

it('saves the monthly schedule days (sorted, unique)', function () {
    [$user, $portfolio] = ownedPortfolio();

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->call('toggleScheduleDay', 20)
        ->call('toggleScheduleDay', 5)
        ->call('saveSchedule')
        ->assertHasNoErrors();

    expect($portfolio->fresh()->schedule_days)->toBe([5, 20]);
});

it('toggling a selected day removes it, and clearing all removes the schedule', function () {
    [$user, $portfolio] = ownedPortfolio();
    $portfolio->update(['schedule_days' => [5, 20]]);

    // Mesma instância do componente (o estado persiste entre chamadas).
    $component = Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->assertSet('scheduleDays', [5, 20])
        ->call('toggleScheduleDay', 5) // remove o 5
        ->call('saveSchedule');

    expect($portfolio->fresh()->schedule_days)->toBe([20]);

    $component->call('toggleScheduleDay', 20) // remove o último
        ->call('saveSchedule');

    expect($portfolio->fresh()->schedule_days)->toBeNull();
});

it('applies a schedule preset (not persisted until saved)', function () {
    [$user, $portfolio] = ownedPortfolio();

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->call('applyPreset', 'biweekly')
        ->assertSet('scheduleDays', [1, 15])
        ->assertSee('Alterações não salvas') // dirty: presets não salvam sozinhos
        ->call('saveSchedule');

    expect($portfolio->fresh()->schedule_days)->toBe([1, 15]);
});

it('discards unsaved schedule changes, reverting to the persisted days', function () {
    [$user, $portfolio] = ownedPortfolio();
    $portfolio->update(['schedule_days' => [10]]);

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->call('toggleScheduleDay', 25)
        ->assertSet('scheduleDays', [10, 25])
        ->call('discardScheduleChanges')
        ->assertSet('scheduleDays', [10]);

    // Nada foi persistido pela edição descartada.
    expect($portfolio->fresh()->schedule_days)->toBe([10]);
});

it('keeps the company label visible alongside the official name after data is fetched', function () {
    [$user, $portfolio] = ownedPortfolio();
    $company = MonitoredCompany::factory()->for($portfolio)->create(['label' => 'LOJA-042']);
    CompanySnapshot::factory()->create([
        'monitored_company_id' => $company->id,
        'razao_social' => 'REDE VAREJO SA',
    ]);

    // A razão social oficial aparece E o código do sistema (label) continua visível.
    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->assertSee('REDE VAREJO SA')
        ->assertSee('LOJA-042');
});

it('shows the scheduled run history', function () {
    [$user, $portfolio] = ownedPortfolio();
    PortfolioScheduledRun::factory()->for($portfolio)->create([
        'scheduled_day' => 15,
        'ran_on' => '2026-03-15',
        'companies_count' => 10,
        'dispatched_count' => 8,
    ]);

    Livewire::actingAs($user)->test(Show::class, ['portfolio' => $portfolio])
        ->assertSee('15/03/2026')
        ->assertSee('agendado p/ dia 15')
        ->assertSee('10 empresa(s)');
});
