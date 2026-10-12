<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\MonitoredCompany;
use App\Services\Dossie\DossieGenerator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;

/**
 * Baixa o dossiê de due diligence (PDF) de uma empresa monitorada. Empresa não
 * tem escopo global, então a policy `view` é a guarda de tenant (404/403 para
 * empresa de outra organização).
 */
final class CompanyDossieController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(MonitoredCompany $company, DossieGenerator $generator): Response
    {
        $this->authorize('view', $company);

        $cnpj = preg_replace('/\D/', '', $company->cnpj) ?? $company->cnpj;

        return $generator->para($company)->download("dossie-{$cnpj}.pdf");
    }
}
