<?php

declare(strict_types=1);

namespace App\Services\Dossie;

use App\DTO\Graph\OwnershipGraph;
use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Services\Graph\OwnershipGraphService;
use App\Services\Risco\RiscoAssessor;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * Monta o dossiê de due diligence (PDF) de uma empresa monitorada: cabeçalho +
 * semáforo de risco + dados cadastrais + quadro societário + sanções (CGU) +
 * Lista Suja + beneficiários finais + grupo econômico + filiais. Usa o
 * {@see RiscoAssessor} (mesma avaliação do painel) e o {@see OwnershipGraphService}
 * (mesmas consultas do grafo). Render via dompdf (PHP puro, sem navegador).
 */
final class DossieGenerator
{
    public function __construct(
        private readonly RiscoAssessor $assessor,
        private readonly OwnershipGraphService $graph,
    ) {}

    public function para(MonitoredCompany $company): DomPdf
    {
        /** @var CompanySnapshot|null $snapshot */
        $snapshot = $company->snapshots()
            ->with('partners')
            ->orderByDesc('captured_at')
            ->orderByDesc('id')
            ->first();

        // Seções do grafo vêm da base CNPJ (fonte externa). Se a base cair, o
        // dossiê sai sem elas — nunca falha a geração por indisponibilidade.
        $beneficiarios = [];
        $filiais = [];
        $grupo = [];

        try {
            $beneficiarios = $this->graph->beneficialOwners($company->cnpj);
            $filiais = $this->graph->branches($company->cnpj);
            $grupo = $this->grupoEconomico($this->graph->for($company->cnpj));
        } catch (Throwable) {
            // base CNPJ indisponível → seções do grafo omitidas
        }

        return Pdf::loadView('dossie.pdf', [
            'company' => $company,
            'snapshot' => $snapshot,
            'assessment' => $this->assessor->para($company),
            'beneficiarios' => $beneficiarios,
            'filiais' => $filiais,
            'grupo' => $grupo,
            'geradoEm' => Carbon::now(),
        ])->setPaper('a4');
    }

    /**
     * Empresas do grupo econômico = nós empresa do grafo que não são o centro
     * (ligadas por sócio em comum na consulta reversa).
     *
     * @return list<array{label: string, document: ?string, situacao: ?string}>
     */
    private function grupoEconomico(OwnershipGraph $graph): array
    {
        $empresas = [];

        foreach ($graph->nodes as $node) {
            if ($node->type === 'empresa' && $node->id !== $graph->center) {
                $empresas[] = ['label' => $node->label, 'document' => $node->document, 'situacao' => $node->situacao];
            }
        }

        return $empresas;
    }
}
