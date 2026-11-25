<?php

declare(strict_types=1);

namespace App\Services\Contratos;

use App\DTO\Contratos\ContratoPublico;
use App\DTO\Contratos\ContratosReport;
use App\Services\Risco\Exceptions\PortalUnavailable;
use App\Services\Risco\PortalTransparenciaClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Throwable;

/**
 * Contratos públicos de uma empresa como FORNECEDORA, consultados na hora na API
 * do Portal da Transparência (contratos do Poder Executivo Federal — o que a
 * API cobre por CPF/CNPJ do fornecedor). A API não ordena por data nem diz o
 * total: lê todas as páginas (lotes paralelos) até a última ou o teto
 * `max_paginas`, revalida cada contrato pelo CNPJ do fornecedor (raiz) e ordena
 * do mais recente para o mais antigo. Só resultado bom vai para o cache.
 *
 * PNCP (estados e municípios): a API oficial de consulta não filtra por
 * fornecedor — a tela oferece o link da busca do próprio site do PNCP.
 *
 * Não expõe `compra.contatoResponsavel` (nome de servidor — pessoa física).
 */
final class ContratosPublicos
{
    /** Registros por página da API (página com menos = a última). */
    private const POR_PAGINA = 15;

    /** Páginas pedidas em paralelo por rodada. */
    private const LOTE = 5;

    public function __construct(
        private readonly PortalTransparenciaClient $portal,
        private readonly int $maxPaginas = 40,
        private readonly int $cacheMinutos = 1440,
        private readonly int $falhaMinutos = 5,
        // Prazo total da leitura (entre os lotes): passou → "indisponível", em vez
        // de a requisição lazy morrer no max_execution_time.
        private readonly int $prazoSegundos = 20,
    ) {}

    public function para(string $cnpj): ContratosReport
    {
        $digits = preg_replace('/\D/', '', $cnpj) ?? '';

        if (strlen($digits) !== 14) {
            return ContratosReport::indisponivel($digits);
        }

        if (! $this->portal->isConfigured()) {
            return ContratosReport::naoConfigurado($digits);
        }

        $chave = 'contratos-publicos:'.$digits;
        $cache = Cache::get($chave);

        if ($cache instanceof ContratosReport) {
            return $cache;
        }

        // Falhou há pouco (ex.: 429 no meio das páginas): não relê tudo a cada
        // visita — espera a janela curta passar.
        if (Cache::has($chave.':falha')) {
            return ContratosReport::indisponivel($digits);
        }

        try {
            $report = $this->consultar($digits);
        } catch (PortalUnavailable $e) {
            report($e);
            Cache::put($chave.':falha', true, now()->addMinutes(max(1, $this->falhaMinutos)));

            return ContratosReport::indisponivel($digits);
        }

        Cache::put($chave, $report, now()->addMinutes(max(1, $this->cacheMinutos)));

        return $report;
    }

    /**
     * Lê as páginas em lotes paralelos até a última (a que vem com menos de 15)
     * ou até o teto `maxPaginas` — aí marca `truncado`. A API não ordena por
     * data, então só dá para ordenar depois de ler tudo.
     *
     * @throws PortalUnavailable
     */
    private function consultar(string $digits): ContratosReport
    {
        /** @var array<int|string, ContratoPublico> $contratos */
        $contratos = [];
        $raiz = substr($digits, 0, 8);
        $teto = max(1, $this->maxPaginas);
        $acabou = false;
        $inicioEm = microtime(true);

        // A 1ª página sozinha (a empresa comum termina nela: 1 chamada); daí em
        // diante, lotes paralelos.
        for ($inicio = 1, $tamanho = 1; $inicio <= $teto && ! $acabou; $inicio += $tamanho, $tamanho = self::LOTE) {
            if ($inicio > 1 && microtime(true) - $inicioEm > $this->prazoSegundos) {
                throw PortalUnavailable::unreachable('contratos', 'prazo total de '.$this->prazoSegundos.' s esgotado');
            }

            $lote = $this->portal->contratosPorFornecedorPaginas($digits, range($inicio, min($inicio + $tamanho - 1, $teto)));
            ksort($lote);

            foreach ($lote as $registros) {
                foreach ($registros as $r) {
                    $contrato = $this->mapear($r, $raiz);

                    if ($contrato !== null) {
                        $contratos[$contrato->id ?? 'n'.count($contratos)] = $contrato; // dedup pelo id do Portal
                    }
                }

                if (count($registros) < self::POR_PAGINA) {
                    $acabou = true; // última página (as seguintes do lote vêm vazias)

                    break;
                }
            }
        }

        $truncado = ! $acabou;
        $lista = array_values($contratos);
        usort($lista, fn (ContratoPublico $a, ContratoPublico $b): int => strcmp((string) $b->dataAssinatura, (string) $a->dataAssinatura));

        return ContratosReport::ok($digits, $lista, $truncado);
    }

    /** @param  array<string, mixed>  $r */
    private function mapear(array $r, string $raiz): ?ContratoPublico
    {
        // Revalida o fornecedor (correção > completude: nunca atribui contrato alheio).
        $cnpjFornecedor = preg_replace('/\D/', '', $this->str(data_get($r, 'fornecedor.cnpjFormatado')) ?? '') ?? '';

        if (strlen($cnpjFornecedor) !== 14 || ! str_starts_with($cnpjFornecedor, $raiz)) {
            return null;
        }

        $objeto = $this->str($r['objeto'] ?? null);
        $objeto = $objeto !== null ? (preg_replace('/^objeto:\s*/i', '', $objeto) ?? $objeto) : null;

        return new ContratoPublico(
            id: is_numeric($r['id'] ?? null) ? (int) $r['id'] : null,
            numero: $this->str($r['numero'] ?? null),
            objeto: $objeto,
            orgao: $this->str(data_get($r, 'unidadeGestora.orgaoVinculado.nome')) ?? $this->str(data_get($r, 'unidadeGestora.nome')),
            orgaoSigla: $this->str(data_get($r, 'unidadeGestora.orgaoVinculado.sigla')),
            modalidade: $this->str($r['modalidadeCompra'] ?? null),
            situacao: $this->str($r['situacaoContrato'] ?? null),
            dataAssinatura: $this->data($r['dataAssinatura'] ?? null),
            vigenciaInicio: $this->data($r['dataInicioVigencia'] ?? null),
            vigenciaFim: $this->data($r['dataFimVigencia'] ?? null),
            valorInicial: is_numeric($r['valorInicialCompra'] ?? null) ? (float) $r['valorInicialCompra'] : null,
            valorFinal: is_numeric($r['valorFinalCompra'] ?? null) ? (float) $r['valorFinalCompra'] : null,
            cnpjFornecedor: $cnpjFornecedor,
        );
    }

    private function data(mixed $value): ?string
    {
        $raw = $this->str($value);

        if ($raw === null) {
            return null;
        }

        try {
            return Carbon::parse($raw)->toDateString();
        } catch (Throwable) {
            return null;
        }
    }

    private function str(mixed $value): ?string
    {
        $value = is_scalar($value) ? trim((string) $value) : '';

        return $value === '' ? null : $value;
    }
}
