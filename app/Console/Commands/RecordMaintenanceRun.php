<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MaintenanceRun;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * Registra o resultado de uma rodada de manutenção mensal da base CNPJ —
 * chamado pelo `reimport-mensal.sh` ao final (e também quando o reimport falha
 * e aborta). Uma linha por execução: status geral + status de cada passo +
 * mensagem. É o que o super-admin vê em /admin.
 *
 * `status` = failed se qualquer passo for `fail`; senão success.
 */
final class RecordMaintenanceRun extends Command
{
    protected $signature = 'vigilo:maintenance-record
        {--kind=cnpj_mensal : Tipo da rotina}
        {--started= : Início em ISO8601 (default: agora)}
        {--reimport=skip : ok|fail|skip}
        {--recoleta=skip : ok|fail|skip}
        {--normalizar=skip : ok|fail|skip}
        {--pgfn= : ok|fail|skip — carga da PGFN na VM (omitido = passo não existiu)}
        {--message= : Mensagem/erro curto}';

    protected $description = 'Registra o resultado de uma rodada de manutenção mensal da base CNPJ.';

    public function handle(): int
    {
        $steps = [
            'reimport' => $this->step('reimport'),
            'recoleta' => $this->step('recoleta'),
            'normalizar' => $this->step('normalizar'),
        ];
        $pgfn = $this->option('pgfn') === null ? null : $this->step('pgfn');

        $status = in_array('fail', [...$steps, $pgfn], true) ? 'failed' : 'success';
        $message = trim((string) $this->option('message'));
        $startedRaw = trim((string) $this->option('started'));

        $run = MaintenanceRun::create([
            'kind' => (string) $this->option('kind'),
            'status' => $status,
            'reimport_status' => $steps['reimport'],
            'recoleta_status' => $steps['recoleta'],
            'normalizar_status' => $steps['normalizar'],
            'pgfn_status' => $pgfn,
            'message' => $message === '' ? null : $message,
            'started_at' => $startedRaw === '' ? now() : $this->parse($startedRaw),
            'finished_at' => now(),
        ]);

        $this->info("Manutenção registrada (#{$run->id}): {$status}.");

        return self::SUCCESS;
    }

    /** Normaliza o status de um passo para o vocabulário fixo; valor estranho → skip. */
    private function step(string $option): string
    {
        $value = $this->option($option);
        $value = is_string($value) ? strtolower(trim($value)) : '';

        return in_array($value, ['ok', 'fail', 'skip'], true) ? $value : 'skip';
    }

    private function parse(string $iso): Carbon
    {
        try {
            return Carbon::parse($iso);
        } catch (\Throwable) {
            $this->warn("--started inválido ('{$iso}'); usando o horário atual.");

            return now();
        }
    }
}
