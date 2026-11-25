<?php

declare(strict_types=1);

namespace App\Livewire\Graph;

use App\DTO\Graph\CompanyBranch;
use App\DTO\Graph\GraphConnection;
use App\DTO\Graph\GraphNode;
use App\DTO\Graph\OwnershipGraph;
use App\DTO\Risco\PgfnDebtSummary;
use App\Services\Export\GraphConnectionsExport;
use App\Services\Graph\OwnershipGraphService;
use App\Services\Risco\PgfnLookup;
use App\Support\Cnpj;
use App\Support\SituacaoCadastral;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * Tela do grafo societário ("grifo"), renderizado no cliente com Cytoscape
 * (força, zoom/pan, rótulo na bolha). A empresa/pessoa no centro; clicar num nó
 * recentra o grafo naquele CNPJ (empresa) ou pessoa (PF). O componente só
 * produz os dados (nós/arestas) + beneficiários/filiais/ligações; a pintura e a
 * interação ficam no componente Alpine `grifo`.
 *
 * Base das duas telas: a da empresa da CARTEIRA (Companies\Graph) e a de
 * QUALQUER CNPJ da base (Empresas\Grafo). Cada uma diz qual CNPJ é a raiz e o
 * cabeçalho/voltar; o resto é igual.
 */
abstract class GraphPage extends Component
{
    /*
     * O foco é #[Locked]: só muda pelas ações (focusOn/focusPerson/resetFocus),
     * que passam pelo teto do pivô. Sem a trava, o cliente mandaria
     * `updates: {focus: …}` direto e cada render montaria o grafo de outro CNPJ
     * sem contar em teto nenhum — o atalho de raspagem que o teto existe para fechar.
     */

    /** CNPJ (14 díg) atualmente no centro do grafo. Vazio = a empresa raiz da tela. */
    #[Locked]
    public string $focus = '';

    /** Documento (CPF mascarado) de uma PESSOA no centro. Vazio = modo empresa. */
    #[Locked]
    public string $focusDocument = '';

    /** Nome da pessoa focada — junto com o documento identifica melhor o sócio. */
    #[Locked]
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

    /**
     * Sinaliza que o último pivô foi bloqueado pelo rate-limit — aviso transiente
     * (vale só para o render da ação que o disparou; zerado a cada ação nova).
     */
    public bool $focusLimited = false;

    /** @var array<string, mixed>|null Cache do grafo por request (ação + render). */
    private ?array $graphCache = null;

    /** CNPJ (14 díg) da empresa raiz da tela — o centro sem foco. */
    abstract protected function rootCnpj(): string;

    /**
     * Cabeçalho da tela: o nome exibido, o link de voltar e o texto do botão que
     * desfaz o foco.
     *
     * @return array{title: ?string, backUrl: string, backLabel: string, resetLabel: string}
     */
    abstract protected function pageContext(): array;

    /** True = a tela não deve consultar a base (ex.: teto anti-raspagem estourado). */
    protected function blocked(): bool
    {
        return false;
    }

    /**
     * Raiz PESSOA (grafo aberto pela página da pessoa): o grafo nasce centrado
     * nela e "voltar" volta para ela. Null (padrão) = a raiz é a empresa de
     * {@see rootCnpj()}.
     *
     * @return array{documento: string, nome: string}|null
     */
    protected function rootPerson(): ?array
    {
        return null;
    }

    /** Zera o aviso transiente a cada nova ação (não "gruda" no próximo request). */
    public function hydrate(): void
    {
        $this->focusLimited = false;
    }

    /**
     * Recentra o grafo numa empresa (nó com CNPJ completo). Lê só dado público da
     * Receita; o abuso é contido pelo teto do pivô (`cnpj.graph.focus_per_minute`).
     *
     * Nome `focusOn` (não `focus`): há a propriedade pública `$focus`, e no cliente
     * `$wire.focus` resolveria para a propriedade, não para o método.
     */
    public function focusOn(string $cnpj, OwnershipGraphService $graphs): void
    {
        if ($this->tooManyFocusAttempts()) {
            return;
        }

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
        if ($this->tooManyFocusAttempts()) {
            return;
        }

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
     * O grafo está fora da raiz (mostra "voltar")? Raiz-empresa: em foco uma
     * pessoa ou outra empresa. Raiz-pessoa: em foco uma empresa ou outra pessoa.
     *
     * @param  array{documento: string, nome: string}|null  $rootPerson
     */
    private function foraDaRaiz(?array $rootPerson, string $root, bool $personMode): bool
    {
        if ($rootPerson === null) {
            return $personMode || ($this->focus !== '' && $this->focus !== $root);
        }

        if ($this->focus !== '') {
            return true;
        }

        return $this->focusDocument !== ''
            && ($this->focusDocument !== $rootPerson['documento'] || $this->focusName !== $rootPerson['nome']);
    }

    /**
     * Limita quantos pivôs (recentragens) o usuário pode fazer por minuto. Como o
     * pivô navega a base CNPJ inteira (due diligence), um teto por usuário evita
     * que a tela vire ferramenta de raspagem em massa. Voltar à empresa monitorada
     * (resetFocus) não conta. Retorna true e sinaliza na tela quando estoura.
     */
    private function tooManyFocusAttempts(): bool
    {
        $max = (int) config('cnpj.graph.focus_per_minute', 30);

        if ($max <= 0) {
            return false; // limite desligado por configuração
        }

        $key = 'grifo-focus:'.(string) (auth()->id() ?? request()->ip());

        if (RateLimiter::tooManyAttempts($key, $max)) {
            $this->focusLimited = true;

            return true;
        }

        RateLimiter::hit($key, 60); // janela de 1 minuto

        return false;
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
        return view('livewire.companies.graph', array_merge($this->graphData($graphs), $this->pageContext(), [
            'limitado' => $this->blocked(),
        ]));
    }

    /**
     * Exporta em Excel as ligações do grafo ATUAL (respeita foco + toggles de
     * sócio PF / endereço, pois esses são propriedades do componente). Reusa o
     * mesmo cálculo da tabela/Copiar via graphData().
     */
    public function exportConnections(OwnershipGraphService $graphs, GraphConnectionsExport $export): BinaryFileResponse
    {
        $data = $this->graphData($graphs);

        $path = $export->build($data['connections'], $data['pgfnDisponivel']);
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

        $rootPerson = $this->rootPerson();
        $root = $rootPerson === null ? Cnpj::normalize($this->rootCnpj()) : '';

        // Pessoa em foco: a do pivô, ou a raiz-pessoa quando nada está em foco.
        [$personDocument, $personName] = match (true) {
            $this->focusDocument !== '' => [$this->focusDocument, $this->focusName],
            $this->focus === '' && $rootPerson !== null => [$rootPerson['documento'], $rootPerson['nome']],
            default => ['', ''],
        };
        $personMode = $personDocument !== '';
        $cnpj = $this->focus !== '' ? $this->focus : $root;
        $focused = $this->foraDaRaiz($rootPerson, $root, $personMode);

        $available = true;
        $cyto = ['nodes' => [], 'edges' => [], 'center' => ''];
        $partnerCount = 0;
        $groupCount = 0;
        $addressCount = 0;
        $participationCount = 0;
        $participationCapped = false;
        $beneficiaries = [];
        $branches = [];
        $connections = [];
        $connectionsText = '';
        $focusLabel = null;
        $pgfnDisponivel = true;

        try {
            if ($this->blocked()) {
                throw new GraphBlocked;
            }

            if ($personMode) {
                $graph = $graphs->forPerson($personDocument, $personName);
            } else {
                $graph = $graphs->for($cnpj, $this->showProbable, $this->showAddress);
                $beneficiaries = $graphs->beneficialOwners($cnpj);
                $branches = $graphs->branches($cnpj);
            }

            $cyto = $this->toCytoscape($graph);

            // Vizinhos de endereço e participações contam à parte; o "grupo
            // econômico" exclui esses para não inflar a contagem.
            [$addressIds, $participationIds] = $this->sideIds($graph);
            // O grafo traz no máximo `reverse_limit` participações: "25+" em vez de um total falso.
            $participationCapped = count($participationIds) >= max(1, (int) config('cnpj.graph.reverse_limit', 25));

            // Empresa visível no canvas: com o filtro "situação negativa" desligado,
            // os nós negativos somem (client-side) — as contagens do cabeçalho
            // acompanham para não divergir do que aparece.
            $visibleEmpresa = fn (GraphNode $n): bool => $this->showNegative
                || $n->id === $graph->center
                || ! SituacaoCadastral::isNegative($n->situacao);

            $outros = fn (GraphNode $n): bool => $n->type === 'empresa' && $n->id !== $graph->center && $visibleEmpresa($n);

            $partnerCount = count(array_filter($graph->nodes, fn (GraphNode $n): bool => str_starts_with($n->type, 'socio') && $n->id !== $graph->center));
            $participationCount = count(array_filter($graph->nodes, fn (GraphNode $n): bool => $outros($n) && isset($participationIds[$n->id])));
            $addressCount = count(array_filter($graph->nodes, fn (GraphNode $n): bool => $outros($n) && ! isset($participationIds[$n->id]) && isset($addressIds[$n->id])));
            $groupCount = count(array_filter($graph->nodes, fn (GraphNode $n): bool => $outros($n) && ! isset($participationIds[$n->id]) && ! isset($addressIds[$n->id])));

            $center = array_values(array_filter($graph->nodes, fn (GraphNode $n): bool => $n->id === $graph->center));
            $focusLabel = $center !== [] ? $center[0]->label : null;

            $dividas = $this->dividasDoGrafo($graph);
            $pgfnDisponivel = $dividas !== null;
            $connections = $this->buildConnections($graph, $branches, $dividas ?? []);
            $connectionsText = $this->connectionsText($connections, $focusLabel, $personMode ? $personDocument : $cnpj, $pgfnDisponivel);
        } catch (GraphBlocked) {
            $available = false; // a tela mostra o aviso do teto, não "base indisponível"
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
            'participationCount' => $participationCount,
            'participationCapped' => $participationCapped,
            'beneficiaries' => $beneficiaries,
            'branches' => $branches,
            'connections' => $connections,
            'connectionsText' => $connectionsText,
            'pgfnDisponivel' => $pgfnDisponivel,
            'focused' => $focused,
            'focusLabel' => $focusLabel,
            'personMode' => $personMode,
            'showProbable' => $this->showProbable,
            'showAddress' => $this->showAddress,
            'showNegative' => $this->showNegative,
            'negativeLabels' => SituacaoCadastral::negativeLabels(),
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
     * @param  array<string, PgfnDebtSummary>  $dividas  dívida ativa por raiz de CNPJ
     * @return list<GraphConnection>
     */
    private function buildConnections(OwnershipGraph $graph, array $branches, array $dividas = []): array
    {
        $center = $graph->center;
        $personMode = str_starts_with($center, 'socio:');

        // Empresas ligadas por sócio em comum (grupo) × participação × só endereço.
        [$addressIds, $participationIds] = $this->sideIds($graph);
        $groupIds = [];
        foreach ($graph->edges as $edge) {
            if ($edge->relation === 'endereco' || $edge->relation === 'participacao') {
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
            } elseif (isset($participationIds[$node->id])) {
                $tipo = 'Participação';
            } elseif (isset($groupIds[$node->id])) {
                $tipo = 'Grupo econômico';
            } elseif (isset($addressIds[$node->id])) {
                $tipo = 'Mesmo endereço';
            } else {
                $tipo = 'Grupo econômico';
            }

            $cnpj = $this->cnpjDigits($node->document);

            $rows[] = new GraphConnection(
                nome: $node->label,
                documento: $this->formatDocument($node->document),
                tipo: $tipo,
                situacao: $node->situacao,
                divida: $cnpj !== null ? ($dividas[substr($cnpj, 0, 8)] ?? null) : null,
                consultaPgfn: $cnpj !== null,
                cnpj: $cnpj,
            );
        }

        foreach ($branches as $branch) {
            $rows[] = new GraphConnection(
                nome: $branch->nomeFantasia ?? ($branch->isMatriz ? 'Matriz' : 'Filial'),
                documento: Cnpj::tryFrom($branch->cnpj)?->formatted() ?? $branch->cnpj,
                tipo: 'Filial',
                situacao: $branch->situacao,
                cnpj: $this->cnpjDigits($branch->cnpj),
            );
        }

        return $rows;
    }

    /**
     * Ids das empresas ligadas ao centro só por endereço (alvo de aresta
     * `endereco`) e das participações (origem de aresta `participacao`: o centro
     * é sócio delas).
     *
     * @return array{0: array<string, true>, 1: array<string, true>}
     */
    private function sideIds(OwnershipGraph $graph): array
    {
        $address = [];
        $participation = [];

        foreach ($graph->edges as $edge) {
            if ($edge->relation === 'endereco') {
                $address[$edge->to] = true;
            } elseif ($edge->relation === 'participacao') {
                $participation[$edge->from] = true;
            }
        }

        return [$address, $participation];
    }

    /**
     * Dívida ativa (PGFN) das empresas do grafo (nós empresa e sócios PJ, pela
     * raiz do CNPJ) numa consulta só. Null = PGFN indisponível — o relatório
     * avisa em vez de sugerir que ninguém deve.
     *
     * @return array<string, PgfnDebtSummary>|null
     */
    private function dividasDoGrafo(OwnershipGraph $graph): ?array
    {
        $basicos = [];

        foreach ($graph->nodes as $node) {
            $cnpj = $this->cnpjDigits($node->document);

            if ($cnpj !== null && $node->id !== $graph->center) {
                $basicos[] = substr($cnpj, 0, 8);
            }
        }

        try {
            return app(PgfnLookup::class)->paraBasicos($basicos);
        } catch (Throwable $e) {
            report($e);

            return null;
        }
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
    private function connectionsText(array $connections, ?string $centerLabel, string $centerDocument, bool $pgfnDisponivel = true): string
    {
        $doc = $this->formatDocument($centerDocument) ?? $centerDocument;
        $header = 'Ligações societárias — '.($centerLabel ?? 'empresa');
        $lines = [$header, $doc, 'Gerado em '.Carbon::now()->format('d/m/Y H:i')];
        if (! $pgfnDisponivel) {
            $lines[] = 'Dívida ativa (PGFN): não consultada — base indisponível no momento.';
        }
        $lines[] = '';

        // Ordem fixa das seções; só sai a que tiver linhas.
        foreach (['Sócio', 'Participação', 'Grupo econômico', 'Empresa do sócio', 'Mesmo endereço', 'Filial'] as $tipo) {
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
                $divida = $c->dividaTexto($pgfnDisponivel);
                if ($divida !== null && $divida !== '' && $pgfnDisponivel) {
                    $parte .= ' — dívida ativa: '.$divida;
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
