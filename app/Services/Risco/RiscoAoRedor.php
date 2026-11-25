<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\DTO\Graph\GraphNode;
use App\DTO\Risco\EmpresaLigadaRisco;
use App\DTO\Risco\RiscoAoRedorReport;
use App\Services\Graph\OwnershipGraphService;
use App\Support\Cnpj;
use App\Support\SituacaoCadastral;
use App\Support\Tentar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * "Risco ao redor" de uma empresa: as empresas ligadas a ela por relação CERTA
 * (sócio PJ, participação e grupo econômico pelo sócio PJ — o grafo padrão, sem
 * as ligações por CPF mascarado nem por endereço) e os sinais de risco de cada
 * uma nas fontes LOCAIS — dívida ativa (PGFN), sanção vigente (cópia da CGU),
 * Lista Suja e situação cadastral. Tudo em lote: uma consulta por fonte.
 */
final class RiscoAoRedor
{
    public function __construct(
        private readonly OwnershipGraphService $grafo,
        private readonly PgfnLookup $pgfn,
        private readonly SancoesLocais $sancoes,
        private readonly DirtyListLookup $listaSuja,
        private readonly string $connection,
        private readonly int $cacheMinutos = 10,
    ) {}

    /**
     * Cache curto por CNPJ (o grafo + 3 consultas em lote a cada abertura de
     * perfil/painel). Só guarda resultado COMPLETO — com PGFN ou sanções fora, a
     * próxima abertura tenta de novo.
     */
    public function para(string $cnpj): RiscoAoRedorReport
    {
        $chave = 'risco-ao-redor:'.Cnpj::normalize($cnpj);
        $cache = $this->cacheMinutos > 0 ? Cache::get($chave) : null;

        if ($cache instanceof RiscoAoRedorReport) {
            return $cache;
        }

        $report = $this->calcular($cnpj);

        if ($this->cacheMinutos > 0 && $report->completo()) {
            Cache::put($chave, $report, now()->addMinutes($this->cacheMinutos));
        }

        return $report;
    }

    private function calcular(string $cnpj): RiscoAoRedorReport
    {
        $graph = $this->grafo->for($cnpj);
        $centro = substr(Cnpj::normalize($cnpj), 0, 8);

        $participacoes = [];
        foreach ($graph->edges as $edge) {
            if ($edge->relation === 'participacao') {
                $participacoes[$edge->from] = true;
            }
        }

        /** @var array<string, array{nome: string, cnpj: string, ligacao: string, situacao: ?string}> $ligadas */
        $ligadas = [];

        foreach ($graph->nodes as $node) {
            $doc = $this->cnpj14($node);

            if ($node->id === $graph->center || $doc === null || substr($doc, 0, 8) === $centro) {
                continue;
            }

            $ligacao = match (true) {
                $node->type === 'socio_pj' => 'Sócio PJ',
                isset($participacoes[$node->id]) => 'Participação',
                $node->type === 'empresa' => 'Grupo econômico',
                default => null,
            };

            if ($ligacao === null) {
                continue;
            }

            $ligadas[substr($doc, 0, 8)] ??= ['nome' => $node->label, 'cnpj' => $doc, 'ligacao' => $ligacao, 'situacao' => $node->situacao];
        }

        $basicos = array_map('strval', array_keys($ligadas));

        // O nó de sócio PJ não traz situação: a da matriz, numa consulta.
        $semSituacao = array_map('strval', array_keys(array_filter($ligadas, fn (array $l): bool => $l['situacao'] === null)));
        $situacoes = $this->situacoesMatriz($semSituacao); // null = a consulta falhou
        foreach ($situacoes ?? [] as $basico => $situacao) {
            if (isset($ligadas[$basico])) {
                $ligadas[$basico]['situacao'] = $situacao;
            }
        }

        $dividas = Tentar::ouNull(fn (): array => $this->pgfn->paraBasicos($basicos));
        $sancoes = Tentar::ouNull(fn (): ?array => $this->sancoes->disponivel() ? $this->sancoes->porBasicos($basicos) : null);
        $listaSuja = $this->listaSuja->raizesConstantes($basicos);

        $empresas = [];

        foreach ($ligadas as $basico => $l) {
            $basico = (string) $basico;
            $vigente = false;

            foreach ($sancoes[$basico] ?? [] as $s) {
                $vigente = $vigente || $s->estaAtiva();
            }

            $empresas[] = new EmpresaLigadaRisco(
                nome: $l['nome'],
                cnpj: $l['cnpj'],
                ligacao: $l['ligacao'],
                situacao: $l['situacao'],
                situacaoNegativa: SituacaoCadastral::isNegative($l['situacao']),
                dividaAtiva: isset($dividas[$basico]),
                sancaoVigente: $vigente,
                listaSuja: isset($listaSuja[$basico]),
            );
        }

        return new RiscoAoRedorReport(
            $empresas,
            pgfnConsultada: $dividas !== null,
            sancoesConsultadas: $sancoes !== null,
            situacaoConsultada: $situacoes !== null,
            sancoesDesatualizadas: $sancoes !== null && (bool) Tentar::ouNull(fn (): bool => $this->sancoes->desatualizada()),
        );
    }

    /** CNPJ (14 díg) do nó, se for empresa/sócio PJ; PF (mascarado) → null. */
    private function cnpj14(GraphNode $node): ?string
    {
        return $node->document !== null && preg_match('/^\d{14}$/', $node->document) === 1 ? $node->document : null;
    }

    /**
     * Situação (rótulo) da matriz de cada raiz, numa consulta. Falha da base →
     * vazio (fica sem situação; não inventa "ativa").
     *
     * @param  list<string>  $basicos
     * @return array<string, ?string>|null null = a consulta falhou
     */
    private function situacoesMatriz(array $basicos): ?array
    {
        if ($basicos === []) {
            return [];
        }

        $codigos = Tentar::ouNull(fn (): array => DB::connection($this->connection)
            ->table('estabelecimentos')
            ->whereIn('cnpj_basico', $basicos)
            ->where('cnpj_ordem', '0001')
            ->pluck('situacao_cadastral', 'cnpj_basico')
            ->all());

        if ($codigos === null) {
            return null;
        }

        $out = [];

        foreach ($codigos as $basico => $codigo) {
            $label = SituacaoCadastral::label($codigo);
            $out[(string) $basico] = $label === '' ? null : $label;
        }

        return $out;
    }
}
