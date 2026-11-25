<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Console\Commands\SweepDevedores;
use App\DTO\Admin\RotinaStatus;
use App\Models\MaintenanceRun;
use App\Services\Risco\SancoesLocais;
use App\Support\Tentar;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Situação das rotinas automáticas da plataforma para o card "Rotinas" do
 * /admin (super-admin): backup do banco, cópia das sanções da CGU, varredura de
 * sanções da carteira e montagem da busca de pessoas. Cada uma diz quando rodou
 * por último, se deu certo e se está ATRASADA (o agendamento parou em silêncio).
 * Fonte de cada uma = onde a rotina deixa rastro (maintenance_runs, cgu_cargas,
 * busca_pessoa_cargas na base CNPJ).
 */
final class StatusRotinas
{
    /** Backup e varredura rodam todo dia: passou disso sem rodada nova = atrasada. */
    private const DIARIA_HORAS = 26;

    /** Rotinas semanais (manutenção de segunda): mais de 8 dias = atrasada. */
    private const SEMANAL_HORAS = 8 * 24;

    public function __construct(
        private readonly SancoesLocais $sancoes,
        private readonly string $connection,
    ) {}

    /** @return list<RotinaStatus> */
    public function todas(): array
    {
        return [
            $this->simples(MaintenanceRun::KIND_BACKUP, 'Backup do banco', 'backup', 'todo dia às 03:00, com cópia na VM'),
            $this->sancoesCgu(),
            $this->simples(MaintenanceRun::KIND_VARREDURA, 'Varredura de sanções da carteira', 'policy', 'todo dia às 01:00 e 04:00'),
            $this->simples(MaintenanceRun::KIND_AERONAVES, 'Registro de aeronaves (ANAC)', 'flight', 'todo dia às 07:15'),
            $this->simples(MaintenanceRun::KIND_RISCO_AO_REDOR, 'Risco das empresas ligadas (alertas)', 'hub', 'todo dia às 09:30 e 12:30'),
            $this->simples(MaintenanceRun::KIND_DEVEDORES, 'Monitoramento de devedoras (alertas)', 'move_down', SweepDevedores::HORARIOS),
            $this->buscaPessoas(),
            $this->marcasInpi(),
        ];
    }

    private function simples(string $kind, string $nome, string $icone, string $periodicidade): RotinaStatus
    {
        $run = MaintenanceRun::ultima($kind);

        return new RotinaStatus(
            nome: $nome,
            icone: $icone,
            situacao: $this->situacao($run),
            ultimaEm: $run?->finished_at,
            detalhe: $run?->message,
            periodicidade: $periodicidade,
        );
    }

    private function sancoesCgu(): RotinaStatus
    {
        $run = MaintenanceRun::ultima(MaintenanceRun::KIND_SANCOES_CGU);
        $cargas = Tentar::ouNull(fn (): array => DB::table('cgu_cargas')->orderBy('fonte')->get(['fonte', 'data_arquivo'])->all()) ?? [];

        $copia = implode(' · ', array_map(
            fn (object $c): string => strtoupper((string) $c->fonte).' '.Carbon::parse((string) $c->data_arquivo)->format('d/m'),
            $cargas,
        ));

        // A cópia velha (agendador parado OU a CGU sem arquivo novo há dias)
        // também conta como atrasada, mesmo com a última rodada "ok".
        // Sem rodada registrada mas com cópia carregada = carga manual antes do
        // 1º agendamento: vale pela idade da cópia.
        $situacao = $run === null && $cargas !== [] ? 'ok' : $this->situacao($run);
        if ($situacao === 'ok' && (bool) Tentar::ouNull(fn (): bool => $this->sancoes->desatualizada())) {
            $situacao = 'atrasada';
        }

        return new RotinaStatus(
            nome: 'Cópia das sanções da CGU',
            icone: 'gavel',
            situacao: $situacao,
            ultimaEm: $run?->finished_at,
            detalhe: trim(($copia !== '' ? 'Cópia: '.$copia.'. ' : '').($run->message ?? '')) ?: null,
            periodicidade: 'todo dia às 08:30 e 14:30',
        );
    }

    /**
     * Busca de pessoas (base CNPJ, na VM): montada pelo reimport a cada dump novo.
     * "Atrasada" = há dump novo carregado que a tabela ainda não reflete.
     */
    private function buscaPessoas(): RotinaStatus
    {
        $dados = Tentar::ouNull(function (): array {
            $db = DB::connection($this->connection);
            $carga = $db->table('busca_pessoa_cargas')->where('id', 1)->first(['assinatura', 'pessoas', 'montado_em']);
            // A MESMA assinatura do bin/pessoas-popular.sh (count|max(processed_at)).
            $atual = $db->table('processed_files')
                ->selectRaw("count(*) || '|' || coalesce(cast(max(processed_at) as text), '') as assinatura")
                ->value('assinatura');

            return ['carga' => $carga, 'atual' => is_scalar($atual) ? (string) $atual : null];
        });

        $base = ['nome' => 'Busca de pessoas (índice)', 'icone' => 'person_search', 'periodicidade' => 'a cada dump novo da Receita (reimport de segunda)'];

        if ($dados === null) {
            return new RotinaStatus(...$base, situacao: 'indisponivel', ultimaEm: null, detalhe: 'Base CNPJ não respondeu.');
        }

        $carga = $dados['carga'];

        if ($carga === null) {
            return new RotinaStatus(...$base, situacao: 'nunca', ultimaEm: null, detalhe: null);
        }

        $reflete = $dados['atual'] !== null && (string) $carga->assinatura === $dados['atual'];

        return new RotinaStatus(
            ...$base,
            situacao: $reflete ? 'ok' : 'atrasada',
            ultimaEm: Carbon::parse((string) $carga->montado_em),
            detalhe: number_format((int) $carga->pessoas, 0, ',', '.').' pessoas'.($reflete ? ' — reflete o dump atual' : ' — há dump novo ainda não refletido'),
        );
    }

    /**
     * Marcas do INPI (cópia na VM, carga semanal com a manutenção de segunda).
     * A rodada vem do reimport-mensal.sh (`vigilo:rotina marcas_inpi`); o
     * tamanho/data da cópia, da própria base.
     */
    private function marcasInpi(): RotinaStatus
    {
        $run = MaintenanceRun::ultima(MaintenanceRun::KIND_MARCAS_INPI);
        $carga = Tentar::ouNull(fn (): ?object => DB::connection($this->connection)->table('inpi_cargas')->orderByDesc('carregado_em')->first(['marcas', 'carregado_em']));
        $copia = $carga !== null
            ? number_format((int) $carga->marcas, 0, ',', '.').' marcas de empresas, cópia de '.Carbon::parse((string) $carga->carregado_em)->format('d/m/Y').'. '
            : '';

        return new RotinaStatus(
            nome: 'Marcas do INPI (cópia)',
            icone: 'verified',
            situacao: $run === null && $carga !== null ? $this->idadeDaCopia(Carbon::parse((string) $carga->carregado_em)) : $this->situacao($run, self::SEMANAL_HORAS),
            ultimaEm: $run?->finished_at,
            detalhe: trim($copia.($run->message ?? '')) ?: null,
            periodicidade: 'toda semana, na manutenção da base CNPJ (segunda)',
        );
    }

    /** Cópia carregada à mão antes da 1ª rodada registrada: vale pela idade dela. */
    private function idadeDaCopia(Carbon $carregadoEm): string
    {
        return $carregadoEm->lt(now()->subHours(self::SEMANAL_HORAS)) ? 'atrasada' : 'ok';
    }

    private function situacao(?MaintenanceRun $run, int $horas = self::DIARIA_HORAS): string
    {
        return match (true) {
            $run === null => 'nunca',
            ! $run->isSuccess() => 'falhou',
            $run->finished_at === null || $run->finished_at->lt(now()->subHours($horas)) => 'atrasada',
            default => 'ok',
        };
    }
}
