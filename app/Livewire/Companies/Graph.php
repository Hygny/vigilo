<?php

declare(strict_types=1);

namespace App\Livewire\Companies;

use App\DTO\Graph\GraphNode;
use App\DTO\Graph\OwnershipGraph;
use App\Models\MonitoredCompany;
use App\Services\Graph\OwnershipGraphService;
use App\Support\Cnpj;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

/**
 * Tela do grafo societário ("grifo") de uma empresa da carteira, renderizado no
 * cliente com Cytoscape (força, zoom/pan, rótulo na bolha). A empresa/pessoa no
 * centro; clicar num nó recentra o grafo naquele CNPJ (empresa) ou pessoa (PF).
 * O componente só produz os dados (nós/arestas) + os beneficiários finais; a
 * pintura e a interação ficam no componente Alpine `grifo`.
 */
#[Layout('layouts.app')]
class Graph extends Component
{
    use AuthorizesRequests;

    public MonitoredCompany $company;

    /** CNPJ (14 díg) atualmente no centro do grafo. Vazio = a empresa monitorada. */
    public string $focus = '';

    /** Documento (CPF mascarado) de uma PESSOA no centro. Vazio = modo empresa. */
    public string $focusDocument = '';

    /** Nome da pessoa focada — junto com o documento identifica melhor o sócio. */
    public string $focusName = '';

    /** @var array<string, mixed>|null Cache do grafo por request (ação + render). */
    private ?array $graphCache = null;

    public function mount(MonitoredCompany $company): void
    {
        $this->authorize('view', $company);
        $this->company = $company;
    }

    /**
     * Recentra o grafo numa empresa (nó com CNPJ completo). Lê dado público da
     * Receita — a página já é autorizada pela empresa monitorada de origem.
     *
     * Nome `focusOn` (não `focus`): há a propriedade pública `$focus`, e no cliente
     * `$wire.focus` resolveria para a propriedade, não para o método.
     */
    public function focusOn(string $cnpj, OwnershipGraphService $graphs): void
    {
        $digits = preg_replace('/\D/', '', $cnpj) ?? '';

        if (strlen($digits) === Cnpj::LENGTH) {
            $this->focus = $digits;
            $this->focusDocument = '';
            $this->focusName = '';
            $this->emitGraph($graphs);
        }
    }

    /**
     * Recentra numa PESSOA, mostrando todas as empresas dela. Casa por documento
     * (CPF mascarado, só dígitos e `*`) **e** nome — mais preciso que só o
     * mascarado. Entrada inválida é ignorada.
     */
    public function focusPerson(string $document, string $name, OwnershipGraphService $graphs): void
    {
        $doc = preg_replace('/[^0-9*]/', '', $document) ?? '';

        if ($doc !== '' && mb_strlen($doc) <= 14) {
            $this->focusDocument = $doc;
            $this->focusName = mb_substr(trim($name), 0, 150);
            $this->focus = '';
            $this->emitGraph($graphs);
        }
    }

    public function resetFocus(OwnershipGraphService $graphs): void
    {
        $this->focus = '';
        $this->focusDocument = '';
        $this->focusName = '';
        $this->emitGraph($graphs);
    }

    public function render(OwnershipGraphService $graphs): View
    {
        return view('livewire.companies.graph', $this->graphData($graphs));
    }

    /**
     * Empurra o grafo novo para o canvas (Cytoscape via Alpine) sem re-morphar o
     * DOM do canvas (wire:ignore) — o painel de beneficiários atualiza no render.
     */
    private function emitGraph(OwnershipGraphService $graphs): void
    {
        $data = $this->graphData($graphs);
        $this->dispatch('grifo-update', graph: $data['cyto']);
    }

    /**
     * Monta (uma vez por request) tudo que a view precisa: os elementos do
     * Cytoscape, os beneficiários finais e os metadados de cabeçalho.
     *
     * @return array<string, mixed>
     */
    private function graphData(OwnershipGraphService $graphs): array
    {
        if ($this->graphCache !== null) {
            return $this->graphCache;
        }

        $personMode = $this->focusDocument !== '';
        $cnpj = $this->focus !== '' ? $this->focus : $this->company->cnpj;
        $focused = $personMode || ($this->focus !== '' && $this->focus !== Cnpj::normalize($this->company->cnpj));

        $available = true;
        $cyto = ['nodes' => [], 'edges' => [], 'center' => ''];
        $partnerCount = 0;
        $groupCount = 0;
        $beneficiaries = [];
        $focusLabel = null;

        try {
            if ($personMode) {
                $graph = $graphs->forPerson($this->focusDocument, $this->focusName);
            } else {
                $graph = $graphs->for($cnpj);
                $beneficiaries = $graphs->beneficialOwners($cnpj);
            }

            $cyto = $this->toCytoscape($graph);
            $partnerCount = count(array_filter($graph->nodes, fn (GraphNode $n): bool => str_starts_with($n->type, 'socio') && $n->id !== $graph->center));
            $groupCount = count(array_filter($graph->nodes, fn (GraphNode $n): bool => $n->type === 'empresa' && $n->id !== $graph->center));

            $center = array_values(array_filter($graph->nodes, fn (GraphNode $n): bool => $n->id === $graph->center));
            $focusLabel = $center !== [] ? $center[0]->label : null;
        } catch (Throwable $e) {
            report($e);
            $available = false;
        }

        return $this->graphCache = [
            'available' => $available,
            'cyto' => $cyto,
            'partnerCount' => $partnerCount,
            'groupCount' => $groupCount,
            'beneficiaries' => $beneficiaries,
            'focused' => $focused,
            'focusLabel' => $focusLabel,
            'personMode' => $personMode,
        ];
    }

    /**
     * Converte o grafo de domínio nos elementos do Cytoscape (nós/arestas),
     * marcando papel (empresa × pessoa), o que é clicável e a situação.
     *
     * @return array{nodes: list<array{data: array<string, mixed>}>, edges: list<array{data: array<string, mixed>}>, center: string}
     */
    private function toCytoscape(OwnershipGraph $graph): array
    {
        $nodes = [];

        foreach ($graph->nodes as $node) {
            $isCenter = $node->id === $graph->center;
            $isPerson = $node->type === 'socio_pf' || $node->type === 'socio_ext';
            $doc = (string) ($node->document ?? '');

            $nodes[] = ['data' => [
                'id' => $node->id,
                'label' => $this->truncate($node->label),
                'role' => $isPerson ? 'person' : 'company',
                'isCenter' => $isCenter,
                'situacao' => $node->situacao,
                'cnpj' => (! $isPerson && preg_match('/^\d{14}$/', $doc) === 1) ? $doc : null,
                'doc' => ($isPerson && $doc !== '' && preg_match('/^[0-9*]+$/', $doc) === 1 && str_contains($doc, '*')) ? $doc : null,
                'name' => $isPerson ? $node->label : null,
            ]];
        }

        $edges = [];
        foreach ($graph->edges as $i => $edge) {
            $edges[] = ['data' => ['id' => 'e'.$i, 'source' => $edge->from, 'target' => $edge->to]];
        }

        return ['nodes' => $nodes, 'edges' => $edges, 'center' => $graph->center];
    }

    private function truncate(string $value, int $limit = 34): string
    {
        return mb_strlen($value) > $limit ? mb_substr($value, 0, $limit - 1).'…' : $value;
    }
}
