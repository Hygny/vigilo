<?php

declare(strict_types=1);

namespace App\Services\Dossie;

use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Services\Risco\RiscoAssessor;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Carbon;

/**
 * Monta o dossiê de due diligence (PDF) de uma empresa monitorada: cabeçalho +
 * semáforo de risco + dados cadastrais + quadro societário + sanções (CGU) +
 * Lista Suja. Usa o {@see RiscoAssessor} (mesma avaliação do painel) e o último
 * snapshot. Render via dompdf (PHP puro, sem navegador — roda no container).
 */
final class DossieGenerator
{
    public function __construct(private readonly RiscoAssessor $assessor) {}

    public function para(MonitoredCompany $company): DomPdf
    {
        /** @var CompanySnapshot|null $snapshot */
        $snapshot = $company->snapshots()
            ->with('partners')
            ->orderByDesc('captured_at')
            ->orderByDesc('id')
            ->first();

        return Pdf::loadView('dossie.pdf', [
            'company' => $company,
            'snapshot' => $snapshot,
            'assessment' => $this->assessor->para($company),
            'geradoEm' => Carbon::now(),
        ])->setPaper('a4');
    }
}
