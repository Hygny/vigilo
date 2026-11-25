<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MaintenanceRun;
use Illuminate\Console\Command;

/**
 * Registra o resultado de uma rotina simples que roda FORA do app — o backup
 * diário do MySQL (`deploy/backup-db.sh`, no host) e a cópia semanal das marcas
 * do INPI na VM (passo `run_inpi` do `deploy/reimport-mensal.sh`). É o que o card
 * "Rotinas" do /admin lê. As rotinas que rodam dentro do app (cópia da CGU,
 * varredura de sanções) registram sozinhas.
 */
final class RegistrarRotina extends Command
{
    protected $signature = 'vigilo:rotina
        {tipo : backup | marcas_inpi}
        {resultado : ok|fail}
        {--message= : Mensagem curta (o que aconteceu)}';

    protected $description = 'Registra o resultado de uma rotina externa ao app (ex.: backup) para o card de Rotinas do /admin.';

    /** Só as rotinas externas: as internas registram pelo próprio comando. */
    private const TIPOS = [MaintenanceRun::KIND_BACKUP, MaintenanceRun::KIND_MARCAS_INPI];

    public function handle(): int
    {
        $tipo = (string) $this->argument('tipo');
        $resultado = strtolower((string) $this->argument('resultado'));

        if (! in_array($tipo, self::TIPOS, true)) {
            $this->error("Tipo desconhecido: {$tipo} (use: ".implode(', ', self::TIPOS).').');

            return self::INVALID;
        }

        if (! in_array($resultado, ['ok', 'fail'], true)) {
            $this->error('Resultado tem de ser ok ou fail.');

            return self::INVALID;
        }

        $run = MaintenanceRun::registrar($tipo, $resultado === 'ok', is_string($this->option('message')) ? $this->option('message') : null);

        $this->info("Rotina {$tipo} registrada (#{$run->id}): {$run->status}.");

        return self::SUCCESS;
    }
}
