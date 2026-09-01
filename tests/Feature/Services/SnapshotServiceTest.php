<?php

declare(strict_types=1);

use App\DTO\CompanyData;
use App\DTO\PartnerData;
use App\Models\CompanyPartner;
use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Services\SnapshotService;
use Illuminate\Support\Carbon;

function sampleCompanyData(): CompanyData
{
    return new CompanyData(
        cnpj: '11222333000181',
        razaoSocial: 'EMPRESA EXEMPLO LTDA',
        nomeFantasia: 'EXEMPLO',
        situacaoCadastral: 'ATIVA',
        situacaoData: '2005-11-03',
        cnaePrincipal: '6201501',
        porte: 'DEMAIS',
        naturezaJuridica: '206-2 - Sociedade Empresária Limitada',
        logradouro: 'RUA DAS FLORES, 100',
        municipio: 'SAO PAULO',
        uf: 'SP',
        partners: [
            new PartnerData('FULANO DE TAL', '***123456**', 'Sócio-Administrador'),
            new PartnerData('BELTRANO DA SILVA', '***654321**', 'Sócio'),
        ],
        raw: ['cnpj' => '11222333000181', 'razao_social' => 'EMPRESA EXEMPLO LTDA'],
    );
}

it('persists a snapshot and its partners atomically', function () {
    $company = MonitoredCompany::factory()->create();

    $snapshot = (new SnapshotService)->store($company, sampleCompanyData());

    expect(CompanySnapshot::count())->toBe(1)
        ->and(CompanyPartner::count())->toBe(2)
        ->and($snapshot->monitored_company_id)->toBe($company->id)
        ->and($snapshot->razao_social)->toBe('EMPRESA EXEMPLO LTDA')
        ->and($snapshot->situacao_cadastral)->toBe('ATIVA')
        ->and($snapshot->uf)->toBe('SP')
        ->and($snapshot->raw_json)->toBe(['cnpj' => '11222333000181', 'razao_social' => 'EMPRESA EXEMPLO LTDA'])
        ->and($snapshot->partners)->toHaveCount(2)
        ->and($snapshot->partners->first()->nome)->toBe('FULANO DE TAL');

    $this->assertDatabaseHas('company_partners', [
        'company_snapshot_id' => $snapshot->id,
        'documento' => '***123456**',
        'qualificacao' => 'Sócio-Administrador',
    ]);
});

it('casts situacao_data to a date and raw_json to an array', function () {
    $company = MonitoredCompany::factory()->create();

    $snapshot = (new SnapshotService)->store($company, sampleCompanyData());
    $fresh = $snapshot->fresh();

    expect($fresh->situacao_data)->toBeInstanceOf(Carbon::class)
        ->and($fresh->situacao_data?->toDateString())->toBe('2005-11-03')
        ->and($fresh->raw_json)->toBeArray();
});

it('respects an explicit captured_at timestamp', function () {
    $company = MonitoredCompany::factory()->create();
    $when = Carbon::parse('2026-01-15 09:00:00');

    $snapshot = (new SnapshotService)->store($company, sampleCompanyData(), $when);

    expect($snapshot->captured_at->equalTo($when))->toBeTrue();
});
