<?php

declare(strict_types=1);

namespace App\Services\Dossie;

use App\DTO\Bens\BensReport;
use App\DTO\Esvaziamento\EsvaziamentoReport;
use App\DTO\Graph\OwnershipGraph;
use App\DTO\Graph\Participacao;
use App\DTO\Risco\PgfnDebtSummary;
use App\DTO\Risco\RiscoAoRedorReport;
use App\DTO\Vigilancia\DetalhesEmpresa;
use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Services\Bens\BensRecebiveis;
use App\Services\Bens\IndiciosEsvaziamento;
use App\Services\Graph\OwnershipGraphService;
use App\Services\Risco\PgfnLookup;
use App\Services\Risco\RiscoAoRedor;
use App\Services\Risco\RiscoAssessor;
use App\Services\Vigilancia\DetalhesEmpresaLookup;
use App\Services\Vigilancia\HistoricoLookup;
use App\Support\Tentar;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdf;
use Illuminate\Support\Carbon;
use Throwable;

/**
 * Monta o dossiê de due diligence (PDF) de uma empresa monitorada: cabeçalho +
 * semáforo de risco + dados cadastrais (com natureza, porte, capital e
 * Simples/MEI) + quadro societário + beneficiários finais + participações (lista
 * completa, com a dívida ativa de cada uma) + grupo econômico + risco ao redor +
 * filiais + histórico cadastral + Lista Suja + dívida ativa + sanções (CGU) +
 * contratos públicos + bens e recebíveis (aeronaves, marcas, participações,
 * créditos a receber) + indícios de esvaziamento e sucessão. Cada seção vem do
 * MESMO serviço da tela correspondente e degrada sozinha (o PDF nunca deixa de
 * sair por uma fonte fora). Render via dompdf (PHP puro, sem navegador).
 */
final class DossieGenerator
{
    /** Eventos do histórico cadastral no PDF (os mais recentes). */
    private const HISTORICO_LIMITE = 20;

    /** Participações listadas no PDF (lista completa até aqui; acima, "N de M"). */
    private const PARTICIPACOES_LIMITE = 300;

    public function __construct(
        private readonly RiscoAssessor $assessor,
        private readonly OwnershipGraphService $graph,
        private readonly PgfnLookup $pgfn,
        private readonly HistoricoLookup $historico,
        private readonly DetalhesEmpresaLookup $detalhes,
        private readonly RiscoAoRedor $aoRedor,
        private readonly BensRecebiveis $bens,
        private readonly IndiciosEsvaziamento $indicios,
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
        $participacoes = [];

        try {
            $beneficiarios = $this->graph->beneficialOwners($company->cnpj);
            $filiais = $this->graph->branches($company->cnpj);
            ['grupo' => $grupo, 'participacoes' => $participacoes] = $this->empresasLigadas($this->graph->for($company->cnpj));
        } catch (Throwable) {
            // base CNPJ indisponível → seções do grafo omitidas
        }

        try {
            $historico = $this->historico->para($company->cnpj, self::HISTORICO_LIMITE);
        } catch (Throwable) {
            $historico = null; // idem: a seção some, o PDF sai
        }

        // Lista completa das participações (o grafo traz só as primeiras), com a
        // dívida ativa de cada uma. Se a leitura falhar, o PDF usa a lista do grafo
        // (ou omite a seção, se a base inteira caiu).
        $participacoesCompletas = Tentar::ouNull(fn (): array => $this->participacoes($company->cnpj));

        return Pdf::loadView('dossie.pdf', [
            'company' => $company,
            'snapshot' => $snapshot,
            'assessment' => $this->assessor->para($company),
            'beneficiarios' => $beneficiarios,
            'filiais' => $filiais,
            'grupo' => $grupo,
            'participacoes' => $participacoesCompletas['lista'] ?? $participacoes,
            'participacoesTotal' => $participacoesCompletas['total'] ?? count($participacoes),
            'historico' => $historico,
            'detalhes' => Tentar::ouNull(fn (): ?DetalhesEmpresa => $this->detalhes->para($company->cnpj)),
            'aoRedor' => Tentar::ouNull(fn (): RiscoAoRedorReport => $this->aoRedor->para($company->cnpj)),
            'bens' => Tentar::ouNull(fn (): BensReport => $this->bens->daEmpresa($company->cnpj)),
            'indicios' => Tentar::ouNull(fn (): EsvaziamentoReport => $this->indicios->para($company->cnpj, $company->recuperacao_marco?->toDateString())),
            'geradoEm' => Carbon::now(),
        ])->setPaper('a4');
    }

    /**
     * Participações (empresas de que esta é sócia PJ) — lista completa até o
     * teto, ativas primeiro, com a dívida ativa de cada uma.
     *
     * @return array{lista: list<array{label: string, document: ?string, situacao: ?string, divida: ?PgfnDebtSummary, pgfn_consultada: bool}>, total: int}
     */
    private function participacoes(string $cnpj): array
    {
        $lista = $this->graph->participations($cnpj, self::PARTICIPACOES_LIMITE);
        $total = count($lista) < self::PARTICIPACOES_LIMITE ? count($lista) : $this->graph->participationCount($cnpj);

        try {
            $dividas = $this->pgfn->paraBasicos(array_map(fn (Participacao $p): string => substr($p->cnpj, 0, 8), $lista));
        } catch (Throwable) {
            $dividas = null;
        }

        return [
            'lista' => array_map(fn (Participacao $p): array => [
                'label' => $p->nome,
                'document' => $p->cnpj,
                'situacao' => $p->situacao,
                'divida' => $dividas[substr($p->cnpj, 0, 8)] ?? null,
                'pgfn_consultada' => $dividas !== null,
            ], $lista),
            'total' => $total,
        ];
    }

    /**
     * Empresas ligadas = nós empresa do grafo que não são o centro, separadas em
     * PARTICIPAÇÕES (o centro é sócio PJ delas — aresta `participacao`) e GRUPO
     * ECONÔMICO (ligadas por sócio em comum na consulta reversa), cada uma com a dívida
     * ativa (PGFN) pela raiz do CNPJ — consultada numa vez só. Se a PGFN não
     * responder, `pgfn_consultada` = false e o PDF imprime "não consultada"
     * (nunca "sem dívida" por falha).
     *
     * @return array{grupo: list<array{label: string, document: ?string, situacao: ?string, divida: ?PgfnDebtSummary, pgfn_consultada: bool}>, participacoes: list<array{label: string, document: ?string, situacao: ?string, divida: ?PgfnDebtSummary, pgfn_consultada: bool}>}
     */
    private function empresasLigadas(OwnershipGraph $graph): array
    {
        $participacao = [];
        foreach ($graph->edges as $edge) {
            if ($edge->relation === 'participacao') {
                $participacao[$edge->from] = true;
            }
        }

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

        $linha = fn ($node): array => [
            'label' => $node->label,
            'document' => $node->document,
            'situacao' => $node->situacao,
            'divida' => $node->document !== null ? ($dividas[substr($node->document, 0, 8)] ?? null) : null,
            'pgfn_consultada' => $dividas !== null,
        ];

        return [
            'grupo' => array_values(array_map($linha, array_filter($nodes, fn ($node): bool => ! isset($participacao[$node->id])))),
            'participacoes' => array_values(array_map($linha, array_filter($nodes, fn ($node): bool => isset($participacao[$node->id])))),
        ];
    }
}
