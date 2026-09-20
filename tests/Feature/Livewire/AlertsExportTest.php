<?php

declare(strict_types=1);

use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Livewire\Alerts\Inbox;
use App\Models\ChangeEvent;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Services\Export\AlertsExcelExport;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\IOFactory;

it('downloads an Excel file of the alerts', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($user->organization))->create();
    ChangeEvent::factory()->for($company, 'monitoredCompany')->create();

    Livewire::actingAs($user)->test(Inbox::class)
        ->call('export')
        ->assertFileDownloaded();
});

it('builds a spreadsheet with a header row and one row per alert', function () {
    $company = MonitoredCompany::factory()->create(['cnpj' => '11222333000181', 'label' => 'Padaria Central']);
    $event = ChangeEvent::factory()->for($company, 'monitoredCompany')->create([
        'type' => ChangeType::SituacaoChanged,
        'field' => 'situacao_cadastral',
        'old_value' => 'ATIVA',
        'new_value' => 'BAIXADA',
        'severity' => Severity::Critical,
    ]);

    $path = app(AlertsExcelExport::class)->build(collect([$event->load('monitoredCompany')]));
    $sheet = IOFactory::load($path)->getActiveSheet();

    expect($sheet->getCell('A1')->getValue())->toBe('Empresa')
        ->and($sheet->getCell('B1')->getValue())->toBe('CNPJ')
        ->and($sheet->getCell('A2')->getValue())->toBe('Padaria Central')
        ->and($sheet->getCell('B2')->getValue())->toBe('11.222.333/0001-81')
        ->and($sheet->getCell('C2')->getValue())->toBe('Situação cadastral alterada')
        ->and($sheet->getCell('E2')->getValue())->toBe('ATIVA')
        ->and($sheet->getCell('F2')->getValue())->toBe('BAIXADA')
        ->and($sheet->getCell('G2')->getValue())->toBe('Crítico')
        ->and($sheet->getHighestRow())->toBe(2); // cabeçalho + 1 alerta

    @unlink($path);
});

it('exports a header-only sheet when there are no alerts', function () {
    $path = app(AlertsExcelExport::class)->build(collect());
    $sheet = IOFactory::load($path)->getActiveSheet();

    expect($sheet->getCell('A1')->getValue())->toBe('Empresa')
        ->and($sheet->getHighestRow())->toBe(1);

    @unlink($path);
});
