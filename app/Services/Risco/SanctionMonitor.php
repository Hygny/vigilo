<?php

declare(strict_types=1);

namespace App\Services\Risco;

use App\DTO\Risco\Sancao;
use App\Models\CompanySanction;
use App\Models\MonitoredCompany;

/**
 * Sincroniza as sanções conhecidas de uma empresa monitorada com a fonte (CGU,
 * via {@see RiscoService}) e devolve as NOVAS — para o job de refresh transformar
 * em alerta/evento. É aqui que mora a persistência (tabela `company_sanctions`)
 * e o diff; o job cuida do change_event/notificação/webhook.
 *
 * Regras de segurança do diff:
 *  - fonte indisponível/sem chave → NÃO toca em nada (nunca inferir sanção a
 *    partir de uma consulta que falhou);
 *  - 1ª sincronização (ou pedida como silenciosa, ex.: rebaseline) é BASELINE:
 *    grava o que existe mas devolve [] (sanção pré-existente não é "novidade").
 */
final class SanctionMonitor
{
    public function __construct(private readonly RiscoService $risco) {}

    /**
     * @return list<CompanySanction> As sanções recém-descobertas (vazio em baseline/indisponível)
     */
    public function sync(MonitoredCompany $company, bool $silent = false): array
    {
        $report = $this->risco->para($company->cnpj);

        // Indisponível/não configurada: não dá para distinguir "nada consta" de
        // "não consegui checar" — então não gravamos nem alertamos.
        if (! $report->disponivel) {
            return [];
        }

        $baseline = $silent || $company->sanctions_synced_at === null;

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
