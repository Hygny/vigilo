<?php

declare(strict_types=1);

use App\Enums\NivelRisco;
use App\Models\DirtyListEmployer;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Services\Risco\RiscoAssessor;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

function assessorCompany(string $cnpj = '11222333000181'): MonitoredCompany
{
    $org = Organization::factory()->create();

    return MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => $cnpj]);
}

it('assesses high risk from an active sanction', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(function (Request $request) {
        if (str_contains($request->url(), '/ceis')) {
            return Http::response([[
                'pessoa' => ['cnpjFormatado' => '11.222.333/0001-81'],
                'tipoSancao' => ['descricaoResumida' => 'Inidônea'],
                'dataInicioSancao' => '01/03/2024',
            ]]);
        }

        return Http::response([]);
    });

    $assessment = app(RiscoAssessor::class)->para(assessorCompany());

    expect($assessment->nivel)->toBe(NivelRisco::Alto)
        ->and($assessment->motivo)->toContain('sanção vigente');
});

it('assesses high risk from the dirty list even without the CGU key', function () {
    config()->set('risco.portal.api_key', null);
    Http::fake();

    $company = assessorCompany();
    DirtyListEmployer::query()->create(['documento' => '11222333000181', 'imported_at' => now()]);

    $assessment = app(RiscoAssessor::class)->para($company);

    expect($assessment->nivel)->toBe(NivelRisco::Alto)
        ->and($assessment->listaSuja)->not->toBeNull()
        ->and($assessment->motivo)->toContain('Lista Suja');

    Http::assertNothingSent();
});

it('assesses medium risk from active PGFN debt, even without the CGU key', function () {
    config()->set('risco.portal.api_key', null);
    Http::fake();

    useCnpjSqlite();
    createPgfnTables();
    $company = assessorCompany();
    seedPgfnDivida('11222333000181', 12345.67, ajuizado: true);

    $assessment = app(RiscoAssessor::class)->para($company);

    expect($assessment->nivel)->toBe(NivelRisco::Medio)
        ->and($assessment->pgfn)->not->toBeNull()
        ->and($assessment->pgfn->total)->toEqualWithDelta(12345.67, 0.001)
        ->and($assessment->pgfn->ajuizadas)->toBe(1)
        ->and($assessment->motivo)->toContain('dívida ativa');

    Http::assertNothingSent();
});

it('does not claim "no PGFN debt" when the PGFN base is unavailable', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(fn () => Http::response([])); // CGU consultada e limpa
    // Sem useCnpjSqlite(): a base CNPJ (onde mora a PGFN) não responde.

    $assessment = app(RiscoAssessor::class)->para(assessorCompany());

    expect($assessment->nivel)->toBe(NivelRisco::Baixo)
        ->and($assessment->pgfn)->toBeNull()
        ->and($assessment->pgfnDisponivel)->toBeFalse()
        ->and($assessment->motivo)->toContain('dívida ativa indisponível')
        ->and($assessment->motivo)->not->toContain('nem dívida ativa');
});

it('says "sem dívida ativa" only when the PGFN was actually checked', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(fn () => Http::response([]));
    useCnpjSqlite();
    createPgfnTables();
    seedPgfnDivida('99888777000166', 50.0); // outra empresa

    $assessment = app(RiscoAssessor::class)->para(assessorCompany());

    expect($assessment->nivel)->toBe(NivelRisco::Baixo)
        ->and($assessment->pgfnDisponivel)->toBeTrue()
        ->and($assessment->motivo)->toContain('sem sanções nem dívida ativa');
});

it('leaves the semaforo unassessed when nothing reliable was checked', function () {
    config()->set('risco.portal.api_key', null);
    Http::fake();

    $assessment = app(RiscoAssessor::class)->para(assessorCompany());

    expect($assessment->avaliado())->toBeFalse()
        ->and($assessment->nivel)->toBeNull();
});
