<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\DTO\Risco\RiscoReport;
use App\DTO\Risco\Sancao;
use App\Enums\FonteSancao;
use App\Services\Risco\Exceptions\PortalUnavailable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Enriquecimento de risco de um CNPJ a partir dos cadastros de sanção da CGU
 * (CEIS/CNEP/CEPIM), via {@see PortalTransparenciaClient}.
 *
 * Correção acima de completude: como o filtro da API é furado, cada registro é
 * REVALIDADO pelo CNPJ consultado — só entra no relatório a sanção que a gente
 * consegue amarrar ao CNPJ (evita acusar a empresa errada). Registro sem CNPJ
 * reconhecível é descartado.
 *
 * Só resultado BOM é cacheado (falha não vira cache → tenta de novo na próxima).
 */
final class RiscoService
{
    /**
     * Caminhos (dot-path) onde o CNPJ do sancionado pode aparecer na resposta,
     * variando por cadastro. Usados para revalidar o vínculo com o CNPJ.
     *
     * @var list<string>
     */
    private const CNPJ_PATHS = [
        'pessoa.cnpjFormatado',
        'pessoa.cpfCnpj',
        'pessoaJuridica.cnpjFormatado',
        'sancionado.codigoFormatado',
        'sancionado.cnpjFormatado',
        'cnpjSancionado',
        'cnpjFormatado',
    ];

    public function __construct(
        private readonly PortalTransparenciaClient $client,
        private readonly int $cacheTtlMinutes,
    ) {}

    /**
     * @param  bool  $fresh  Ignora o cache na LEITURA (mas grava o resultado bom):
     *                       a varredura de sanções precisa do dado de agora, não
     *                       do que uma visita à página cacheou horas antes.
     */
    public function para(string $cnpj, bool $fresh = false): RiscoReport
    {
        $digits = preg_replace('/\D/', '', $cnpj) ?? '';

        if (strlen($digits) !== 14) {
            return RiscoReport::indisponivel($cnpj);
        }

        if (! $this->client->isConfigured()) {
            return RiscoReport::naoConfigurado($digits);
        }

        $cached = $fresh ? null : Cache::get($this->cacheKey($digits));

        if ($cached instanceof RiscoReport) {
            return $cached;
        }

        try {
            $sancoes = $this->coletar($digits);
        } catch (PortalUnavailable) {
            return RiscoReport::indisponivel($digits);
        }

        $report = RiscoReport::ok($digits, $sancoes);

        Cache::put($this->cacheKey($digits), $report, now()->addMinutes(max(1, $this->cacheTtlMinutes)));

        return $report;
    }

    /**
     * @return list<Sancao>
     *
     * @throws PortalUnavailable
     */
    private function coletar(string $digits): array
    {
        $sancoes = [];

        foreach (FonteSancao::cases() as $fonte) {
            foreach ($this->client->cadastro($fonte, $digits) as $record) {
                if (! $this->pertenceAoCnpj($record, $digits)) {
                    continue;
                }

                $sancoes[] = $this->mapear($fonte, $record);
            }
        }

        return $sancoes;
    }

    /**
     * O registro é realmente deste CNPJ? Varre os caminhos conhecidos e exige
     * um CNPJ de 14 dígitos idêntico ao consultado.
     *
     * @param  array<string, mixed>  $record
     */
    private function pertenceAoCnpj(array $record, string $digits): bool
    {
        foreach (self::CNPJ_PATHS as $path) {
            $value = data_get($record, $path);

            if (! is_scalar($value)) {
                continue;
            }

            $found = preg_replace('/\D/', '', (string) $value) ?? '';

            if (strlen($found) === 14 && $found === $digits) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $record
     */
    private function mapear(FonteSancao $fonte, array $record): Sancao
    {
        return new Sancao(
            fonte: $fonte,
            tipo: $this->pick($record, 'tipoSancao.descricaoResumida', 'tipoSancao.descricaoPortal', 'motivo', 'descricaoMotivo'),
            orgao: $this->pick($record, 'orgaoSancionador.nome', 'orgaoSuperior.nome', 'orgaoLotacao.nome'),
            dataInicio: $this->data($this->pick($record, 'dataInicioSancao', 'dataReferencia', 'dataPublicacaoSancao')),
            dataFim: $this->data($this->pick($record, 'dataFimSancao', 'dataFinalSancao')),
            fundamentacao: $this->fundamentacao($record),
        );
    }

    /**
     * Primeiro valor escalar não-vazio entre os caminhos candidatos.
     *
     * @param  array<string, mixed>  $record
     */
    private function pick(array $record, string ...$paths): ?string
    {
        foreach ($paths as $path) {
            $value = data_get($record, $path);

            if (is_scalar($value)) {
                $value = trim((string) $value);

                if ($value !== '') {
                    return $value;
                }
            }
        }

        return null;
    }

    /**
     * Fundamentação pode vir como lista de objetos (`[{descricao}]`) ou string.
     *
     * @param  array<string, mixed>  $record
     */
    private function fundamentacao(array $record): ?string
    {
        $raw = data_get($record, 'fundamentacao');

        if (is_array($raw)) {
            $descricoes = [];

            foreach ($raw as $item) {
                $descricao = data_get($item, 'descricao');

                if (is_scalar($descricao) && trim((string) $descricao) !== '') {
                    $descricoes[] = trim((string) $descricao);
                }
            }

            return $descricoes === [] ? null : implode(' · ', array_values(array_unique($descricoes)));
        }

        return $this->pick($record, 'fundamentacao', 'textoPublicacao');
    }

    /**
     * Normaliza a data (a API usa dd/mm/aaaa) para Y-m-d. Null se não parsear.
     */
    private function data(?string $raw): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        foreach (['d/m/Y', 'Y-m-d'] as $format) {
            $date = \DateTimeImmutable::createFromFormat($format, $raw);

            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }

        try {
            return Carbon::parse($raw)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function cacheKey(string $digits): string
    {
        return "risco:portal:{$digits}";
    }
}
