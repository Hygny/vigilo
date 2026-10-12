<?php

declare(strict_types=1);

use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Jobs\RefreshMonitoredCompanyJob;
use App\Models\DirtyListEmployer;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Notifications\CompanyChangeDetected;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

/**
 * @return array<string, mixed>
 */
function dirtyReceitaPayload(): array
{
    return [
        'cnpj' => '11222333000181',
        'razao_social' => 'EMPRESA EXEMPLO LTDA',
        'descricao_situacao_cadastral' => 'ATIVA',
        'data_situacao_cadastral' => '2005-11-03',
        'cnae_fiscal' => 6201501,
        'porte' => 'DEMAIS',
        'logradouro' => 'RUA X',
        'numero' => '1',
        'municipio' => 'SAO PAULO',
        'uf' => 'SP',
        'qsa' => [],
    ];
}

function dirtyJobCompany(): MonitoredCompany
{
    $org = Organization::factory()->create();
    User::factory()->for($org)->create();
    $portfolio = Portfolio::factory()->for($org)->create();

    return MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '11222333000181']);
}

it('raises a critical alert and notifies when the company is on the dirty list', function () {
    Notification::fake();
    Http::fake(['https://brasilapi.com.br/*' => Http::response(dirtyReceitaPayload(), 200)]);
    DirtyListEmployer::query()->create(['documento' => '11222333000181', 'nome' => 'EMPRESA EXEMPLO LTDA', 'uf' => 'SP', 'ano' => '2024', 'imported_at' => now()]);

    $company = dirtyJobCompany();

    RefreshMonitoredCompanyJob::dispatchSync($company);

    $event = $company->changeEvents()->where('type', ChangeType::ListaSuja->value)->first();

    expect($event)->not->toBeNull()
        ->and($event->severity)->toBe(Severity::Critical)
        ->and($event->new_value)->toContain('EMPRESA EXEMPLO')
        ->and($company->fresh()->dirty_list_flagged_at)->not->toBeNull();

    Notification::assertSentTo($company->portfolio->organization->users->first(), CompanyChangeDetected::class);
});

it('does not alert again on a later refresh once already flagged', function () {
    Notification::fake();
    Http::fake(['https://brasilapi.com.br/*' => Http::response(dirtyReceitaPayload(), 200)]);
    DirtyListEmployer::query()->create(['documento' => '11222333000181', 'imported_at' => now()]);

    $company = dirtyJobCompany();

    RefreshMonitoredCompanyJob::dispatchSync($company); // entra → 1 alerta
    RefreshMonitoredCompanyJob::dispatchSync($company); // segue listada → sem novo alerta

    expect($company->changeEvents()->where('type', ChangeType::ListaSuja->value)->count())->toBe(1);
});
