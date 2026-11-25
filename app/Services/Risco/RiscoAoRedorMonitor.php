<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\DTO\Risco\EmpresaLigadaRisco;
use App\DTO\Risco\RiscoAoRedorReport;
use App\Models\CompanyNeighborFlag;
use App\Models\MonitoredCompany;
use Illuminate\Support\Facades\DB;

/**
 * Monitor do "risco ao redor" de uma empresa da carteira: compara os sinais de
 * risco ATUAIS das empresas ligadas (relação certa) com os conhecidos
 * (`company_neighbor_flags`) e devolve só o que é NOVO — uma ligada que passou
 * a ter sinal, ou ganhou um sinal que não tinha. Sinal que some só atualiza o
 * conhecido (não alerta); ligada que some do grafo só é esquecida depois de
 * algumas rodadas seguidas (encolhimento de passagem não vira "novo" depois).
 *
 * Nunca decide com dado incompleto: se a PGFN ou as sanções não puderam ser
 * consultadas, não toca em nada (senão a volta da fonte viraria "novo"). 1ª
 * rodada da empresa (ou `silent`) = baseline: grava sem alertar.
 */
final class RiscoAoRedorMonitor
{
    /** Rodadas seguidas sem a ligada no grafo até esquecê-la. */
    private const AUSENCIAS_PARA_ESQUECER = 3;

    public function __construct(private readonly RiscoAoRedor $aoRedor) {}

    /**
     * Consulta (fora de transação: grafo + lotes na base CNPJ). Null = alguma
     * fonte não respondeu — não comparar agora.
     */
    public function consultar(MonitoredCompany $company): ?RiscoAoRedorReport
    {
        $report = $this->aoRedor->para($company->cnpj);

        return $report->completo() ? $report : null;
    }

    /**
     * Aplica o relatório ao conhecido, travando a linha da empresa (varredura
     * manual × agendada não duplicam alerta).
     *
     * @return list<array{empresa: EmpresaLigadaRisco, novos: list<string>}>
     */
    public function aplicar(MonitoredCompany $company, RiscoAoRedorReport $report, bool $silent = false): array
    {
        return DB::transaction(function () use ($company, $report, $silent): array {
            $atual = MonitoredCompany::query()->whereKey($company->id)->lockForUpdate()->first();

            if ($atual === null) {
                return []; // removida da carteira no meio da varredura
            }

            $baseline = $silent || $atual->neighbor_risk_synced_at === null;

            /** @var array<string, CompanyNeighborFlag> $conhecidos */
            $conhecidos = $atual->neighborFlags()->get()->keyBy('cnpj_basico')->all();
            $novidades = [];
            $vistos = [];

            foreach ($report->ligadas as $empresa) {
                $basico = substr($empresa->cnpj, 0, 8);
                $codigos = $empresa->codigos();
                $vistos[$basico] = true;
                $antes = isset($conhecidos[$basico]) ? $conhecidos[$basico]->codigos() : [];
                $novos = array_values(array_diff($codigos, $antes));

                if ($codigos === [] && ! isset($conhecidos[$basico])) {
                    continue; // ligada limpa e nunca sinalizada: nada a guardar
                }

                if ($novos !== [] && ! $baseline) {
                    $novidades[] = ['empresa' => $empresa, 'novos' => $novos];
                }

                if ($codigos === []) {
                    $conhecidos[$basico]->delete(); // limpou: esquece (se voltar, alerta de novo)

                    continue;
                }

                $atual->neighborFlags()->updateOrCreate(
                    ['cnpj_basico' => $basico],
                    ['sinais' => implode(',', $codigos), 'visto_em' => now(), 'ausencias' => 0],
                );
            }

            // Sumiu do grafo: só esquece depois de algumas rodadas seguidas (sócio que
            // saiu de verdade) — um encolhimento de passagem (base em recarga, teto
            // da reversa) não pode virar "novo" de novo quando a ligada voltar.
            foreach ($conhecidos as $basico => $flag) {
                if (isset($vistos[(string) $basico])) {
                    continue;
                }

                $flag->ausencias + 1 >= self::AUSENCIAS_PARA_ESQUECER
                    ? $flag->delete()
                    : $flag->forceFill(['ausencias' => $flag->ausencias + 1])->save();
            }

            $atual->forceFill(['neighbor_risk_synced_at' => now()])->save();

            return $novidades;
        });
    }
}
