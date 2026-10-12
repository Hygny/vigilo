<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\Models\DirtyListEmployer;
use App\Models\MonitoredCompany;

/**
 * Detecta quando uma empresa monitorada ENTRA na Lista Suja do trabalho escravo
 * (transição não-consta → consta entre refreshes), para o job de refresh virar
 * em alerta. O estado conhecido fica em `monitored_companies.dirty_list_flagged_at`.
 *
 * Diferente das sanções (histórico volumoso, baseline silencioso), a Lista Suja
 * é binária e rara — então, fora do rebaseline (`$silent`), uma empresa já listada
 * na 1ª checagem TAMBÉM alerta: é um sinal crítico que o cliente quer saber.
 */
final class DirtyListMonitor
{
    public function __construct(private readonly DirtyListLookup $lookup) {}

    /** Devolve a entrada quando a empresa ENTROU na lista agora (para alertar); senão null. */
    public function sync(MonitoredCompany $company, bool $silent = false): ?DirtyListEmployer
    {
        $entry = $this->lookup->consta($company->cnpj);
        $isListed = $entry !== null;
        $wasListed = $company->dirty_list_flagged_at !== null;

        if ($isListed && ! $wasListed) {
            $company->forceFill(['dirty_list_flagged_at' => now()])->save();

            return $silent ? null : $entry;
        }

        if (! $isListed && $wasListed) {
            $company->forceFill(['dirty_list_flagged_at' => null])->save(); // saiu da lista
        }

        return null;
    }
}
