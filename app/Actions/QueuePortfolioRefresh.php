<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\RefreshMonitoredCompanyJob;
use App\Models\MonitoredCompany;
use App\Models\Portfolio;
use Illuminate\Database\Eloquent\Builder;

/**
 * Marca as empresas-alvo de um portfólio como "pendentes" (limpa status/erro) e
 * (re)enfileira um refresh para cada uma, em chunks. A dedup de fila
 * (ShouldBeUnique no job) evita jobs repetidos.
 *
 * Usado tanto ao atualizar pela lista de portfólios quanto de dentro do
 * portfólio, garantindo estado idêntico (pendente + progresso) nos dois caminhos.
 */
final class QueuePortfolioRefresh
{
    /**
     * @param  (callable(Builder<MonitoredCompany>): void)|null  $constrain  filtro opcional (ex.: só erros/não encontrados)
     * @return int quantas empresas foram enfileiradas
     */
    public function handle(Portfolio $portfolio, ?callable $constrain = null): int
    {
        $query = MonitoredCompany::query()->where('portfolio_id', $portfolio->id);

        if ($constrain !== null) {
            $constrain($query);
        }

        // Congela os alvos ANTES de mexer no status: um filtro por status
        // (ex.: só "erro") deixaria de casar depois que o reset zera o status.
        $ids = (clone $query)->pluck('id');

        if ($ids->isEmpty()) {
            return 0;
        }

        MonitoredCompany::whereKey($ids)->update([
            'last_refresh_status' => null,
            'last_refresh_error' => null,
        ]);

        MonitoredCompany::whereKey($ids)
            ->select(['id', 'portfolio_id', 'cnpj', 'label'])
            ->chunkById(300, function ($chunk): void {
                foreach ($chunk as $company) {
                    RefreshMonitoredCompanyJob::dispatch($company);
                }
            });

        return $ids->count();
    }
}
