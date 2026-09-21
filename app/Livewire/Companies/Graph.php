<?php

declare(strict_types=1);

namespace App\Livewire\Companies;

use App\DTO\Graph\GraphNode;
use App\DTO\Graph\OwnershipGraph;
use App\Models\MonitoredCompany;
use App\Services\Graph\OwnershipGraphService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

/**
 * Tela do grafo societário (Camada 1) de uma empresa da carteira: a empresa no
 * centro, seus sócios num anel e o grupo econômico (empresas ligadas por sócio
 * em comum) num anel externo. Renderizado como SVG server-side (radial) — sem
 * dependência JS. Degrada com aviso se a base CNPJ estiver indisponível.
 */
#[Layout('layouts.app')]
class Graph extends Component
{
    use AuthorizesRequests;

    public MonitoredCompany $company;

    public function mount(MonitoredCompany $company): void
    {
        $this->authorize('view', $company);
        $this->company = $company;
    }

    public function render(OwnershipGraphService $graphs): View
    {
        $available = true;
        $layout = null;
        $partnerCount = 0;
        $groupCount = 0;
        $beneficiaries = [];

        try {
            $graph = $graphs->for($this->company->cnpj);
            $layout = $this->buildLayout($graph);
            $partnerCount = count(array_filter($graph->nodes, fn (GraphNode $n): bool => str_starts_with($n->type, 'socio')));
            $groupCount = count(array_filter($graph->nodes, fn (GraphNode $n): bool => $n->type === 'empresa' && $n->id !== $graph->center));
            $beneficiaries = $graphs->beneficialOwners($this->company->cnpj);
        } catch (Throwable $e) {
            report($e);
            $available = false;
        }

        return view('livewire.companies.graph', [
            'available' => $available,
            'layout' => $layout,
            'partnerCount' => $partnerCount,
            'groupCount' => $groupCount,
            'beneficiaries' => $beneficiaries,
        ]);
    }

    /**
     * Posiciona os nós em anéis concêntricos (empresa-centro no meio, sócios no
     * anel interno, grupo econômico no externo) e converte as arestas em
     * coordenadas para o SVG.
     *
     * @return array{width: int, height: int, nodes: list<array<string, mixed>>, edges: list<array{x1: float, y1: float, x2: float, y2: float}>}
     */
    private function buildLayout(OwnershipGraph $graph): array
    {
        $width = 760;
        $height = 520;
        $cx = $width / 2;
        $cy = $height / 2;

        /** @var list<GraphNode> $socios */
        $socios = [];
        /** @var list<GraphNode> $empresas */
        $empresas = [];

        foreach ($graph->nodes as $node) {
            if ($node->id === $graph->center) {
                continue;
            }
            if (str_starts_with($node->type, 'socio')) {
                $socios[] = $node;
            } else {
                $empresas[] = $node;
            }
        }

        /** @var array<string, array{float, float}> $pos */
        $pos = [];
        $pos[$graph->center] = [$cx, $cy];
        $this->placeRing($socios, $cx, $cy, 140.0, $pos);
        $this->placeRing($empresas, $cx, $cy, 235.0, $pos);

        $nodes = [];
        foreach ($graph->nodes as $node) {
            [$x, $y] = $pos[$node->id] ?? [$cx, $cy];
            $isCenter = $node->id === $graph->center;
            $isSocio = str_starts_with($node->type, 'socio');

            $nodes[] = [
                'x' => $x,
                'y' => $y,
                'r' => $isCenter ? 34 : ($isSocio ? 21 : 26),
                'label' => $this->truncate($node->label),
                'title' => $node->label.($node->document !== null ? ' · '.$node->document : ''),
                'fill' => $this->nodeFill($isCenter, $isSocio, $node->situacao),
                'kind' => $isCenter ? 'center' : ($isSocio ? 'socio' : 'empresa'),
            ];
        }

        $edges = [];
        foreach ($graph->edges as $edge) {
            $from = $pos[$edge->from] ?? null;
            $to = $pos[$edge->to] ?? null;

            if ($from === null || $to === null) {
                continue;
            }

            $edges[] = ['x1' => $from[0], 'y1' => $from[1], 'x2' => $to[0], 'y2' => $to[1]];
        }

        return ['width' => $width, 'height' => $height, 'nodes' => $nodes, 'edges' => $edges];
    }

    /**
     * @param  list<GraphNode>  $items
     * @param  array<string, array{float, float}>  $pos
     */
    private function placeRing(array $items, float $cx, float $cy, float $radius, array &$pos): void
    {
        $count = count($items);

        if ($count === 0) {
            return;
        }

        $step = 2 * M_PI / $count;

        foreach ($items as $i => $node) {
            $angle = -M_PI / 2 + $i * $step;
            $pos[$node->id] = [$cx + $radius * cos($angle), $cy + $radius * sin($angle)];
        }
    }

    private function nodeFill(bool $isCenter, bool $isSocio, ?string $situacao): string
    {
        if ($isCenter) {
            // Situação negativa destaca em crítico; caso contrário usa a cor da
            // "empresa consultada" da legenda (var(--primary)).
            return match ($situacao) {
                'BAIXADA', 'INAPTA', 'SUSPENSA', 'NULA' => 'var(--crit)',
                default => 'var(--primary)',
            };
        }

        return $isSocio ? 'var(--high)' : 'var(--surface-3)';
    }

    private function truncate(string $value, int $limit = 22): string
    {
        return mb_strlen($value) > $limit ? mb_substr($value, 0, $limit - 1).'…' : $value;
    }
}
