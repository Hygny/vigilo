<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\MonitoredCompany;
use App\Services\Bens\DevedorMonitor;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Linha de base da devedora logo que ela é marcada: grava o que ela já tem
 * (aeronaves, participações, contratos, possíveis sucessoras) sem alertar, para
 * a varredura diária só avisar do que aparecer DEPOIS. Tipo cuja fonte não
 * respondeu agora fica para a 1ª rodada em que responder (também sem alerta).
 */
class SincronizarDevedorJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $uniqueFor = 900;

    public function __construct(public int $monitoredCompanyId) {}

    public function uniqueId(): string
    {
        return (string) $this->monitoredCompanyId;
    }

    public function handle(DevedorMonitor $monitor): void
    {
        $company = MonitoredCompany::query()->find($this->monitoredCompanyId);

        if ($company === null || ! $company->emRecuperacao()) {
            return;
        }

        // Só tipos sem linha de base: grava sem alertar e não conta como checagem do dia.
        $monitor->aplicar($company, $monitor->consultar($company), soLinhaDeBase: true);
    }
}
