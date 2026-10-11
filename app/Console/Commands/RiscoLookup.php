<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\DTO\Risco\Sancao;
use App\Services\Risco\RiscoService;
use Illuminate\Console\Command;

/**
 * Diagnóstico do painel de risco: consulta as sanções da CGU (CEIS/CNEP/CEPIM)
 * de um CNPJ pela MESMA via que a tela usa ({@see RiscoService}) e imprime o
 * resultado. Serve para o check de go-live (confirmar, com a chave real, que a
 * consulta devolve a sanção de um CNPJ sabidamente sancionado).
 *
 * No VPS (app em Docker): `docker compose exec app php artisan vigilo:risco <cnpj>`
 */
final class RiscoLookup extends Command
{
    protected $signature = 'vigilo:risco {cnpj : CNPJ a consultar (com ou sem máscara)}';

    protected $description = 'Consulta as sanções da CGU (CEIS/CNEP/CEPIM) de um CNPJ — diagnóstico do painel de risco.';

    public function handle(RiscoService $risco): int
    {
        $report = $risco->para((string) $this->argument('cnpj'));

        if (! $report->configurado) {
            $this->warn('Integração não configurada: defina PORTAL_TRANSPARENCIA_API_KEY no .env.');

            return self::FAILURE;
        }

        if (! $report->disponivel) {
            $this->error('Fonte indisponível: erro/timeout ao consultar o Portal da Transparência.');

            return self::FAILURE;
        }

        if (! $report->temSancoes()) {
            $this->info("Nada consta para {$report->cnpj} nos cadastros CEIS, CNEP e CEPIM.");

            return self::SUCCESS;
        }

        $this->info("{$report->total()} sanção(ões) para {$report->cnpj}:");

        $this->table(
            ['Fonte', 'Tipo', 'Órgão', 'Início', 'Fim'],
            array_map(fn (Sancao $s): array => [
                $s->fonte->sigla(),
                $s->tipo ?? '—',
                $s->orgao ?? '—',
                $s->dataInicio ?? '—',
                $s->dataFim ?? '—',
            ], $report->sancoes),
        );

        return self::SUCCESS;
    }
}
