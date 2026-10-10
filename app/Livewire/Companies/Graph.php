<?php

declare(strict_types=1);

namespace App\Livewire\Companies;

use App\DTO\Graph\CompanyBranch;
use App\DTO\Graph\GraphConnection;
use App\DTO\Graph\GraphNode;
use App\DTO\Graph\OwnershipGraph;
use App\Models\MonitoredCompany;
use App\Services\Export\GraphConnectionsExport;
use App\Services\Graph\OwnershipGraphService;
use App\Support\Cnpj;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

    /**
     * Incluir no grupo econômico as **conexões por sócio PF** (CPF mascarado —
     * menor certeza). Off por padrão: o grafo mostra só o certo (sócios diretos +
     * grupo via sócio PJ) e as filiais ficam no painel. Reduz falso positivo.
     */
    public bool $showProbable = false;

    /**
     * Mostrar no grafo as **empresas no mesmo endereço** (CEP + número) do centro
     * — ligadas por aresta de endereço. Off por padrão; só no modo empresa.
     */
    public bool $showAddress = false;

    /**
     * Exibir no canvas as empresas com **situação cadastral negativa**
     * (baixada/inapta/suspensa/nula). On por padrão — quando desligado, o canvas
     * remove esses nós (menos o centro); é filtro só visual, não muda os dados
     * do grafo nem o relatório de ligações.
     */
    public bool $showNegative = true;

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

    /**
     * Liga/desliga as ligações prováveis (só no modo empresa — o modo pessoa já
     * filtra por nome exato) e reemite o grafo.
     */
    public function toggleProbable(OwnershipGraphService $graphs): void
    {
        $this->showProbable = ! $this->showProbable;
        $this->emitGraph($graphs);
    }

    /**
     * Liga/desliga as empresas no mesmo endereço (só no modo empresa) e reemite.
     */
    public function toggleAddress(OwnershipGraphService $graphs): void
    {
        $this->showAddress = ! $this->showAddress;
        $this->emitGraph($graphs);
    }

    /**
     * Liga/desliga a exibição das empresas com situação negativa: avisa o canvas
     * por evento, que remove/recoloca esses nós (filtro client-side). Não
     * rebuilda o grafo nem reconsulta a base.
     */
    public function toggleNegative(): void
    {
        $this->showNegative = ! $this->showNegative;
        $this->dispatch('grifo-negative', on: $this->showNegative);
    }

    public function render(OwnershipGraphService $graphs): View
    {
        return view('livewire.companies.graph', $this->graphData($graphs));
    }

    /**
     * Exporta em Excel as ligações do grafo ATUAL (respeita foco + toggles de
     * sócio PF / endereço, pois esses são propriedades do componente). Reusa o
     * mesmo cálculo da tabela/Copiar via graphData().
     */
    public function exportConnections(OwnershipGraphService $graphs, GraphConnectionsExport $export): BinaryFileResponse
    {
        $data = $this->graphData($graphs);

        $path = $export->build($data['connections']);
        $filename = 'ligacoes-'.Carbon::now()->format('Y-m-d-His').'.xlsx';

        return response()->download($path, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend();
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
        $addressCount = 0;
        $beneficiaries = [];
        $branches = [];
        $connections = [];
        $connectionsText = '';
        $focusLabel = null;

        try {
            if ($personMode) {
                $graph = $graphs->forPerson($this->focusDocument, $this->focusName);
            } else {
                $graph = $graphs->for($cnpj, $this->showProbable, $this->showAddress);
                $beneficiaries = $graphs->beneficialOwners($cnpj);
                $branches = $graphs->branches($cnpj);
            }

            $cyto = $this->toCytoscape($graph);

            // Vizinhos de endereço (alvos de aresta 'endereco') contam à parte;
            // o "grupo econômico" exclui esses para não inflar a contagem.
            $addressIds = [];
            foreach ($graph->edges as $edge) {
                if ($edge->relation === 'endereco') {
                    $addressIds[$edge->to] = true;
                }
            }
            $addressCount = count($addressIds);

            $partnerCount = count(array_filter($graph->nodes, fn (GraphNode $n): bool => str_starts_with($n->type, 'socio') && $n->id !== $graph->center));
            $groupCount = count(array_filter($graph->nodes, fn (GraphNode $n): bool => $n->type === 'empresa' && $n->id !== $graph->center && ! isset($addressIds[$n->id])));

            $center = array_values(array_filter($graph->nodes, fn (GraphNode $n): bool => $n->id === $graph->center));
            $focusLabel = $center !== [] ? $center[0]->label : null;

            $connections = $this->buildConnections($graph, $branches);
            $connectionsText = $this->connectionsText($connections, $focusLabel, $personMode ? $this->focusDocument : $cnpj);
        } catch (Throwable $e) {
            report($e);
            $available = false;
        }

        return $this->graphCache = [
            'available' => $available,
            'cyto' => $cyto,
            'partnerCount' => $partnerCount,
            'groupCount' => $groupCount,
            'addressCount' => $addressCount,
            'beneficiaries' => $beneficiaries,
            'branches' => $branches,
            'connections' => $connections,
            'connectionsText' => $connectionsText,
            'focused' => $focused,
            'focusLabel' => $focusLabel,
            'personMode' => $personMode,
            'showProbable' => $this->showProbable,
            'showAddress' => $this->showAddress,
            'showNegative' => $this->showNegative,
        ];
    }

    /**
     * Monta as linhas do relatório de ligações a partir do grafo: cada sócio,
     * empresa do grupo e vizinha de endereço vira uma linha; as filiais entram
     * do painel (relação certa por CNPJ). Classifica o tipo pela posição no
     * grafo (aresta de sócio × de endereço) — fonte única da tabela, do Copiar
     * e do Excel.
     *
     * @param  list<CompanyBranch>  $branches
     * @return list<GraphConnection>
     */
    private function buildConnections(OwnershipGraph $graph, array $branches): array
    {
        $center = $graph->center;
        $personMode = str_starts_with($center, 'socio:');

        // Empresas ligadas por sócio (grupo) vs. só por endereço.
        $groupIds = [];
        $addressIds = [];
        foreach ($graph->edges as $edge) {
            if ($edge->relation === 'endereco') {
                $addressIds[$edge->to] = true;

                continue;
            }

            foreach ([$edge->from, $edge->to] as $end) {
                if ($end !== $center && str_starts_with($end, 'empresa:')) {
                    $groupIds[$end] = true;
                }
            }
        }

        $rows = [];

        foreach ($graph->nodes as $node) {
            if ($node->id === $center) {
                continue;
            }

            if (str_starts_with($node->type, 'socio')) {
                $tipo = 'Sócio';
            } elseif ($personMode) {
                $tipo = 'Empresa do sócio';
            } elseif (isset($groupIds[$node->id])) {
                $tipo = 'Grupo econômico';
            } elseif (isset($addressIds[$node->id])) {
                $tipo = 'Mesmo endereço';
            } else {
                $tipo = 'Grupo econômico';
            }

            $rows[] = new GraphConnection(
                nome: $node->label,
                documento: $this->formatDocument($node->document),
                tipo: $tipo,
                situacao: $node->situacao,
            );
        }

        foreach ($branches as $branch) {
            $rows[] = new GraphConnection(
                nome: $branch->nomeFantasia ?? ($branch->isMatriz ? 'Matriz' : 'Filial'),
                documento: Cnpj::tryFrom($branch->cnpj)?->formatted() ?? $branch->cnpj,
                tipo: 'Filial',
                situacao: $branch->situacao,
            );
        }

        return $rows;
    }

    /** Formata o documento do nó: CNPJ (14 díg) com máscara; CPF mascarado como está. */
    private function formatDocument(?string $document): ?string
    {
        $cnpj = $this->cnpjDigits($document);

        if ($cnpj !== null) {
            return Cnpj::tryFrom($cnpj)?->formatted() ?? $cnpj;
        }

        return $document;
    }

    /** O documento, se for um CNPJ de 14 dígitos (não CPF mascarado); senão null. */
    private function cnpjDigits(?string $document): ?string
    {
        return $document !== null && preg_match('/^\d{14}$/', $document) === 1 ? $document : null;
    }

    /**
     * Texto do botão "Copiar" — as ligações agrupadas por tipo, em pt-BR, pronto
     * para colar num e-mail.
     *
     * @param  list<GraphConnection>  $connections
     */
    private function connectionsText(array $connections, ?string $centerLabel, string $centerDocument): string
    {
        $doc = $this->formatDocument($centerDocument) ?? $centerDocument;
        $header = 'Ligações societárias — '.($centerLabel ?? 'empresa');
        $lines = [$header, $doc, 'Gerado em '.Carbon::now()->format('d/m/Y H:i'), ''];

        // Ordem fixa das seções; só sai a que tiver linhas.
        foreach (['Sócio', 'Grupo econômico', 'Empresa do sócio', 'Mesmo endereço', 'Filial'] as $tipo) {
            $doTipo = array_values(array_filter($connections, fn (GraphConnection $c): bool => $c->tipo === $tipo));

            if ($doTipo === []) {
                continue;
            }

            $lines[] = $tipo.':';

            foreach ($doTipo as $c) {
                $parte = '- '.$c->nome;
                if ($c->documento !== null) {
                    $parte .= ' ('.$c->documento.')';
                }
                if ($c->situacao !== null) {
                    $parte .= ' — '.$c->situacao;
                }
                $lines[] = $parte;
            }

            $lines[] = '';
        }

        return rtrim(implode("\n", $lines))."\n";
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
                'title' => $node->label,
                'role' => $isPerson ? 'person' : 'company',
                'isCenter' => $isCenter,
                'situacao' => $node->situacao,
                'cnpj' => $isPerson ? null : $this->cnpjDigits($doc),
                'doc' => ($isPerson && $doc !== '' && preg_match('/^[0-9*]+$/', $doc) === 1 && str_contains($doc, '*')) ? $doc : null,
                'name' => $isPerson ? $node->label : null,
            ]];
        }

        $edges = [];
        foreach ($graph->edges as $i => $edge) {
            $edges[] = ['data' => ['id' => 'e'.$i, 'source' => $edge->from, 'target' => $edge->to, 'probable' => $edge->probable, 'type' => $edge->relation]];
        }

        return ['nodes' => $nodes, 'edges' => $edges, 'center' => $graph->center];
    }

    private function truncate(string $value, int $limit = 34): string
    {
        return mb_strlen($value) > $limit ? mb_substr($value, 0, $limit - 1).'…' : $value;
    }
}
