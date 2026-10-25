<?php

declare(strict_types=1);

namespace App\Actions;

use App\Actions\Exceptions\CompanyNotAdded;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Support\Cnpj;
use Illuminate\Support\Facades\DB;

/**
 * Põe um CNPJ para monitorar num portfólio respeitando a quota do plano. Fonte
 * única do "adicionar empresa" (tela do portfólio e perfil da empresa vindo da
 * busca). Autorização é de quem chama.
 */
final class AddCompanyToPortfolio
{
    /**
     * @throws CompanyNotAdded já no portfólio ou sem vaga no plano
     */
    public function handle(Portfolio $portfolio, Cnpj $cnpj, ?string $label = null): MonitoredCompany
    {
        if ($portfolio->monitoredCompanies()->where('cnpj', $cnpj->value)->exists()) {
            throw new CompanyNotAdded('Este CNPJ já está no portfólio.');
        }

        $organization = $portfolio->organization;

        // Quota do plano com transação + lock na org (DT-11): a checagem e a
        // criação são atômicas, então duas abas adicionando o "último" CNPJ ao
        // mesmo tempo serializam aqui e a segunda vê a vaga já preenchida.
        $company = DB::transaction(function () use ($organization, $portfolio, $cnpj, $label): ?MonitoredCompany {
            $locked = Organization::query()
                ->whereKey($organization->getKey())
                ->lockForUpdate()
                ->first();

            if ($locked === null || $locked->remainingCompanySlots() < 1) {
                return null;
            }

            return $portfolio->monitoredCompanies()->create([
                'cnpj' => $cnpj->value,
                'label' => ($label !== null && $label !== '') ? $label : null,
            ]);
        });

        if ($company === null) {
            throw new CompanyNotAdded("Limite do plano {$organization->plan->label()} atingido ({$organization->maxMonitoredCompanies()} CNPJs). Faça upgrade para adicionar mais.");
        }

        return $company;
    }
}
