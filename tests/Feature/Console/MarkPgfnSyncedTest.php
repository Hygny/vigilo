<?php

declare(strict_types=1);

use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;

it('marks every monitored company as synced with the PGFN', function () {
    $org = Organization::factory()->create();
    $portfolio = Portfolio::factory()->for($org)->create();
    MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '11222333000181']);
    MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '44555666000199']);

    expect(MonitoredCompany::query()->whereNull('pgfn_synced_at')->count())->toBe(2);

    $this->artisan('vigilo:pgfn-marcar-sincronizado')->assertSuccessful();

    expect(MonitoredCompany::query()->whereNull('pgfn_synced_at')->count())->toBe(0);
});

it('--from marks only the snapshot CNPJs (a company added mid-import stays pending)', function () {
    $org = Organization::factory()->create();
    $portfolio = Portfolio::factory()->for($org)->create();
    $noSnapshot = MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '11.222.333/0001-81']); // formatado de propósito
    $addedMidImport = MonitoredCompany::factory()->for($portfolio)->create(['cnpj' => '44555666000199']);

    // Snapshot que o import usou — só o primeiro CNPJ (dígitos crus).
    $file = sys_get_temp_dir().'/snap_'.uniqid().'.txt';
    file_put_contents($file, "11222333000181\n");

    $this->artisan('vigilo:pgfn-marcar-sincronizado', ['--from' => $file])->assertSuccessful();

    expect($noSnapshot->fresh()->pgfn_synced_at)->not->toBeNull()       // casou mesmo formatado
        ->and($addedMidImport->fresh()->pgfn_synced_at)->toBeNull();    // fora do snapshot → segue pendente

    @unlink($file);
});

it('--from fails when the file does not exist', function () {
    $this->artisan('vigilo:pgfn-marcar-sincronizado', ['--from' => sys_get_temp_dir().'/nope_'.uniqid().'.txt'])->assertFailed();
});
