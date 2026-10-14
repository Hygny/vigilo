<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\DTO\Risco\RiscoReport;
use App\DTO\Risco\Sancao;
use App\Models\CompanySanction;
use App\Models\MonitoredCompany;
use Illuminate\Support\Facades\DB;

/**
 * Sincroniza as sanções conhecidas de uma empresa monitorada com a fonte (CGU,
 * via {@see RiscoService}) e devolve as NOVAS — para a varredura diária
 * (`vigilo:varrer-sancoes`) transformar em alerta/evento. É aqui que mora a
 * persistência (tabela `company_sanctions`) e o diff; a varredura cuida do
 * change_event/notificação/webhook.
 *
 * Dividido em duas etapas para a varredura poder gravar sanção + change_event
 * num único átomo: {@see consultar()} faz o HTTP (fora de transação) e
 * {@see aplicar()} só mexe no banco. {@see sync()} junta as duas.
 *
 * Regras de segurança do diff:
 *  - fonte indisponível/sem chave → NÃO toca em nada (nunca inferir sanção a
 *    partir de uma consulta que falhou) e devolve null;
 *  - 1ª sincronização (ou pedida como silenciosa, ex.: rebaseline) é BASELINE:
 *    grava o que existe mas devolve [] (sanção pré-existente não é "novidade").
 */
final class SanctionMonitor
{
    public function __construct(private readonly RiscoService $risco) {}

    /**
     * @return list<CompanySanction>|null As sanções recém-descobertas ([] em
     *                                    baseline/nada novo); null quando a fonte
     *                                    estava indisponível (não deu para checar)
     */
    public function sync(MonitoredCompany $company, bool $silent = false): ?array
    {
        $report = $this->consultar($company);

        if ($report === null) {
            return null;
        }

        return $this->aplicar($company, $report, $silent);
    }

    /**
     * Consulta a fonte SEM cache de leitura (o monitor precisa do dado de agora).
     * Null quando indisponível/não configurada: não dá para distinguir "nada
     * consta" de "não consegui checar" — o chamador não grava nem alerta (a
     * varredura usa isso como disjuntor).
     */
    public function consultar(MonitoredCompany $company): ?RiscoReport
    {
        $report = $this->risco->para($company->cnpj, fresh: true);

        return $report->disponivel ? $report : null;
    }

    /**
     * Aplica um relatório disponível: grava as sanções ainda desconhecidas e
     * marca a empresa como sincronizada. Só banco — o chamador pode envolver
     * numa transação maior (a varredura grava os change_events no mesmo átomo).
     *
     * Trava a linha da empresa: duas execuções concorrentes (varredura manual ×
     * agendada) serializam aqui, e a 2ª já enxerga o que a 1ª gravou — sem
     * duplicar sanção nem alerta.
     *
     * @return list<CompanySanction>
     */
    public function aplicar(MonitoredCompany $company, RiscoReport $report, bool $silent = false): array
    {
        return DB::transaction(function () use ($company, $report, $silent): array {
            $atual = MonitoredCompany::query()->whereKey($company->id)->lockForUpdate()->first();

            if ($atual === null) {
                return []; // removida da carteira no meio da varredura
            }

            $baseline = $silent || $atual->sanctions_synced_at === null;

            /** @var array<string, true> $conhecidas */
            $conhecidas = [];
            foreach ($company->sanctions()->pluck('fingerprint') as $fingerprint) {
                $conhecidas[(string) $fingerprint] = true;
            }

            $novas = [];

            foreach ($report->sancoes as $sancao) {
                $fingerprint = $this->fingerprint($sancao);

                if (isset($conhecidas[$fingerprint])) {
                    continue;
                }

                $conhecidas[$fingerprint] = true; // evita duplicar na mesma passada

                $registro = $company->sanctions()->create([
                    'fonte' => $sancao->fonte->value,
                    'fingerprint' => $fingerprint,
                    'tipo' => $sancao->tipo,
                    'orgao' => $sancao->orgao,
                    'data_inicio' => $sancao->dataInicio,
                    'data_fim' => $sancao->dataFim,
                    'detected_at' => now(),
                ]);

                if (! $baseline) {
                    $novas[] = $registro;
                }
            }

            $company->forceFill(['sanctions_synced_at' => now()])->save();

            return $novas;
        });
    }

    /**
     * Chave estável de uma sanção. NÃO inclui a data de fim de propósito: uma
     * prorrogação não deve ser contada como sanção nova.
     */
    private function fingerprint(Sancao $sancao): string
    {
        return sha1(implode('|', [
            $sancao->fonte->value,
            $sancao->tipo ?? '',
            $sancao->dataInicio ?? '',
            $sancao->orgao ?? '',
        ]));
    }
}
