<?php

declare(strict_types=1);

namespace App\Services\Dossie;

use App\DTO\Graph\OwnershipGraph;
use App\DTO\Risco\PgfnDebtSummary;
use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Services\Graph\OwnershipGraphService;
use App\Services\Risco\PgfnLookup;
use App\Services\Risco\RiscoAssessor;
use App\Services\Vigilancia\HistoricoLookup;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * Monta o dossiê de due diligence (PDF) de uma empresa monitorada: cabeçalho +
 * semáforo de risco + dados cadastrais + quadro societário + sanções (CGU) +
 * Lista Suja + beneficiários finais + grupo econômico (com a dívida ativa de
 * cada empresa) + filiais + histórico cadastral na Receita. Usa o {@see RiscoAssessor} (mesma avaliação do
 * painel) e o {@see OwnershipGraphService} (mesmas consultas do grafo). Render
 * via dompdf (PHP puro, sem navegador).
 */
final class DossieGenerator
{
    /** Eventos do histórico cadastral no PDF (os mais recentes). */
    private const HISTORICO_LIMITE = 20;

    public function __construct(
        private readonly RiscoAssessor $assessor,
        private readonly OwnershipGraphService $graph,
        private readonly PgfnLookup $pgfn,
        private readonly HistoricoLookup $historico,
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

        try {
            $historico = $this->historico->para($company->cnpj, self::HISTORICO_LIMITE);
        } catch (Throwable) {
            $historico = null; // idem: a seção some, o PDF sai
        }

        return Pdf::loadView('dossie.pdf', [
            'company' => $company,
            'snapshot' => $snapshot,
            'assessment' => $this->assessor->para($company),
            'beneficiarios' => $beneficiarios,
            'filiais' => $filiais,
            'grupo' => $grupo,
            'historico' => $historico,
            'geradoEm' => Carbon::now(),
        ])->setPaper('a4');
    }

    /**
     * Empresas do grupo econômico = nós empresa do grafo que não são o centro
     * (ligadas por sócio em comum na consulta reversa), cada uma com a dívida
     * ativa (PGFN) pela raiz do CNPJ — consultada numa vez só. Se a PGFN não
     * responder, `pgfn_consultada` = false e o PDF imprime "não consultada"
     * (nunca "sem dívida" por falha).
     *
     * @return list<array{label: string, document: ?string, situacao: ?string, divida: ?PgfnDebtSummary, pgfn_consultada: bool}>
     */
    private function grupoEconomico(OwnershipGraph $graph): array
    {
        $nodes = array_values(array_filter(
            $graph->nodes,
            fn ($node): bool => $node->type === 'empresa' && $node->id !== $graph->center,
        ));

        $basicos = [];
        foreach ($nodes as $node) {
            if ($node->document !== null && preg_match('/^\d{14}$/', $node->document) === 1) {
                $basicos[] = substr($node->document, 0, 8);
            }
        }

        try {
            $dividas = $this->pgfn->paraBasicos($basicos);
        } catch (Throwable) {
            $dividas = null;
        }

        return array_map(fn ($node): array => [
            'label' => $node->label,
            'document' => $node->document,
            'situacao' => $node->situacao,
            'divida' => $node->document !== null ? ($dividas[substr($node->document, 0, 8)] ?? null) : null,
            'pgfn_consultada' => $dividas !== null,
        ], $nodes);
    }
}
