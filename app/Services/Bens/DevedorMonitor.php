<?php

declare(strict_types=1);

namespace App\Services\Bens;

use App\DTO\Bens\ConsultaDevedor;
use App\DTO\Bens\Marca;
use App\DTO\Contratos\ContratoPublico;
use App\Models\MonitoredCompany;
use App\Models\RecuperacaoItem;
use App\Services\Contratos\ContratosPublicos;
use App\Services\Graph\OwnershipGraphService;
use App\Support\Cnpj;
use App\Support\ConsultaComTimeout;
use App\Support\RazaoSocial;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Monitor de DEVEDORA (recuperação de crédito): para uma empresa da carteira
 * marcada como devedora, compara o que ela tem agora com o conhecido
 * (`recuperacao_itens`) e devolve só o que é NOVO —
 *
 * - aeronave ativa em nome dela (RAB/ANAC) — bem a penhorar;
 * - participação societária (quotas/ações);
 * - contrato federal vigente (crédito a receber do governo);
 * - marca do INPI (registro em vigor ou pedido em andamento — bem móvel);
 * - possível sucessora (empresa surgida depois da dívida, 2+ sinais).
 *
 * Cada tipo só é comparado quando a fonte respondeu COMPLETA (lista inteira,
 * sem teto nem falha) — senão a volta da fonte viraria "novo". Item que some só
 * é esquecido depois de algumas rodadas seguidas. Linha de base POR TIPO: a 1ª
 * vez que um tipo responde completo, grava sem alertar (inclusive na ativação).
 * Marcar/desmarcar e aplicar disputam a mesma trava da linha da empresa.
 */
final class DevedorMonitor
{
    public const AERONAVE = 'aeronave';

    public const PARTICIPACAO = 'participacao';

    public const CONTRATO = 'contrato';

    public const SUCESSORA = 'sucessora';

    public const MARCA = 'marca';

    /** Rodadas seguidas sem o item até esquecê-lo. */
    private const AUSENCIAS_PARA_ESQUECER = 3;

    /** Participações lidas (no teto a lista pode estar cortada → não compara). */
    private const PARTICIPACOES = 300;

    /** Tamanho do objeto do contrato na descrição do alerta. */
    private const OBJETO = 120;

    public function __construct(
        private readonly AeronavesLookup $aeronaves,
        private readonly OwnershipGraphService $grafo,
        private readonly ContratosPublicos $contratos,
        private readonly IndiciosEsvaziamento $indicios,
        private readonly MarcasLookup $marcas,
    ) {}

    /**
     * Marca a empresa como devedora (ou troca a data da dívida), sob a trava da
     * linha. Outra data muda quem é "possível sucessora": refaz só a linha de
     * base delas. Devolve true se passou a ser monitorada agora.
     */
    public function marcar(MonitoredCompany $company, ?Carbon $marco): bool
    {
        return DB::transaction(function () use ($company, $marco): bool {
            $atual = MonitoredCompany::query()->whereKey($company->id)->lockForUpdate()->firstOrFail();
            $nova = ! $atual->emRecuperacao();
            $mudouMarco = $atual->recuperacao_marco?->toDateString() !== $marco?->toDateString();

            if (! $nova && $mudouMarco) {
                $atual->recuperacaoItens()->where('tipo', self::SUCESSORA)->delete();
                $atual->recuperacao_tipos = $this->semTipo($atual->tiposSincronizados(), self::SUCESSORA);
            }

            $atual->forceFill(['recuperacao_desde' => $atual->recuperacao_desde ?? now(), 'recuperacao_marco' => $marco])->save();
            $company->setRawAttributes($atual->getAttributes(), sync: true);

            return $nova;
        });
    }

    /** Desliga o monitoramento e esquece o conhecido, sob a trava da linha. */
    public function desmarcar(MonitoredCompany $company): void
    {
        DB::transaction(function () use ($company): void {
            $atual = MonitoredCompany::query()->whereKey($company->id)->lockForUpdate()->firstOrFail();
            $atual->forceFill(['recuperacao_desde' => null, 'recuperacao_marco' => null, 'recuperacao_synced_at' => null, 'recuperacao_tipos' => null])->save();
            $atual->recuperacaoItens()->delete();
            $company->setRawAttributes($atual->getAttributes(), sync: true);
        });
    }

    /**
     * Itens atuais por tipo (fora de transação: base CNPJ, cópia do RAB, Portal).
     */
    public function consultar(MonitoredCompany $company): ConsultaDevedor
    {
        $falhou = false;
        $marco = $company->recuperacao_marco?->toDateString();

        $itens = [
            self::AERONAVE => $this->tentar(fn (): ?array => $this->aeronaves($company->cnpj), $falhou),
            self::PARTICIPACAO => $this->tentar(fn (): ?array => $this->participacoes($company->cnpj), $falhou),
            self::CONTRATO => $this->tentar(fn (): ?array => $this->contratos($company->cnpj), $falhou),
            self::MARCA => $this->tentar(fn (): ?array => $this->marcas($company->cnpj), $falhou),
            // closure (não arrow fn): o erro da base dentro dos indícios precisa chegar ao $falhou de fora
            self::SUCESSORA => $this->tentar(function () use ($company, $marco, &$falhou): ?array {
                return $this->sucessoras($company->cnpj, $marco, $falhou);
            }, $falhou),
        ];

        return new ConsultaDevedor($itens, $falhou, $marco);
    }

    /**
     * Aplica ao conhecido, travando a linha da empresa (varredura manual ×
     * agendada × marcar/desmarcar não se atropelam).
     *
     * @param  bool  $soLinhaDeBase  só grava os tipos ainda sem linha de base e não conta como checagem (job da ativação: nunca engole alerta nem adia a varredura)
     * @return list<array{tipo: string, chave: string, descricao: string, grave: bool}>
     */
    public function aplicar(MonitoredCompany $company, ConsultaDevedor $consulta, bool $soLinhaDeBase = false): array
    {
        return DB::transaction(function () use ($company, $consulta, $soLinhaDeBase): array {
            $atual = MonitoredCompany::query()->whereKey($company->id)->lockForUpdate()->first();

            if ($atual === null || ! $atual->emRecuperacao()) {
                return []; // saiu da carteira ou deixou de ser devedora no meio da varredura
            }

            $itensPorTipo = $consulta->itens;

            if ($atual->recuperacao_marco?->toDateString() !== $consulta->marco) {
                $itensPorTipo[self::SUCESSORA] = null; // a data mudou depois da consulta: sucessoras de outro marco
            }

            $sincronizados = $atual->tiposSincronizados();
            $novidades = [];

            foreach ($itensPorTipo as $tipo => $itens) {
                if ($itens === null) {
                    continue; // fonte incompleta: não mexe no conhecido desse tipo
                }

                $baseline = ! in_array($tipo, $sincronizados, true);

                if ($soLinhaDeBase && ! $baseline) {
                    continue; // a varredura já cuida desse tipo
                }

                $sincronizados[] = $tipo;
                array_push($novidades, ...$this->comparar($atual, $tipo, $itens, $baseline));
            }

            $atual->recuperacao_tipos = implode(',', array_values(array_unique($sincronizados))) ?: null;

            // Checagem do dia só quando nenhuma fonte deu erro (senão a 2ª passada tenta de novo).
            if (! $soLinhaDeBase && ! $consulta->falhou) {
                $atual->recuperacao_synced_at = now();
            }

            $atual->save();

            return $novidades;
        });
    }

    /**
     * @param  array<array-key, array{descricao: string, grave: bool}>  $itens
     * @return list<array{tipo: string, chave: string, descricao: string, grave: bool}>
     */
    private function comparar(MonitoredCompany $atual, string $tipo, array $itens, bool $baseline): array
    {
        /** @var array<string, RecuperacaoItem> $conhecidos */
        $conhecidos = $atual->recuperacaoItens()->where('tipo', $tipo)->get()->keyBy('chave')->all();
        $novidades = [];
        $vistos = [];
        $agora = now();

        foreach ($itens as $chave => $item) {
            $chave = (string) $chave; // raiz numérica vira chave int no array do PHP

            if (! isset($conhecidos[$chave]) && ! $baseline) {
                $novidades[] = ['tipo' => $tipo, 'chave' => $chave] + $item;
            }

            $vistos[] = ['monitored_company_id' => $atual->id, 'tipo' => $tipo, 'chave' => $chave, 'visto_em' => $agora, 'ausencias' => 0];
        }

        foreach (array_chunk($vistos, 500) as $lote) {
            RecuperacaoItem::query()->upsert($lote, ['monitored_company_id', 'tipo', 'chave'], ['visto_em', 'ausencias']);
        }

        foreach ($conhecidos as $chave => $item) {
            if (isset($itens[(string) $chave])) {
                continue;
            }

            $item->ausencias + 1 >= self::AUSENCIAS_PARA_ESQUECER
                ? $item->delete()
                : $item->forceFill(['ausencias' => $item->ausencias + 1])->save();
        }

        return $novidades;
    }

    /**
     * Roda uma fonte; erro = null + `$falhou` (reportado sem o SQL).
     *
     * @param  callable(): (array<array-key, array{descricao: string, grave: bool}>|null)  $fonte
     * @return array<array-key, array{descricao: string, grave: bool}>|null
     */
    private function tentar(callable $fonte, bool &$falhou): ?array
    {
        try {
            return $fonte();
        } catch (Throwable $e) {
            ConsultaComTimeout::reportarSemDados($e);
            $falhou = true;

            return null;
        }
    }

    /** @return array<array-key, array{descricao: string, grave: bool}>|null */
    private function aeronaves(string $cnpj): ?array
    {
        if (! $this->aeronaves->disponivel()) {
            return null; // cópia do RAB nunca carregada: não se aplica
        }

        $lista = $this->aeronaves->daEmpresa($cnpj);

        if (count($lista) >= AeronavesLookup::LIMITE) {
            return null; // lista no teto: pode estar cortada
        }

        $out = [];

        foreach ($lista as $a) {
            if (! $a->ativa()) {
                continue;
            }

            $detalhes = array_filter([$a->rotuloPapeis(empresa: true), $a->onus() ?? ($a->livre() ? 'sem ônus' : null)]);
            $out[$a->marca] = [
                'descricao' => sprintf('%s — %s (%s)', $a->marca, $a->descricao(), implode(', ', $detalhes)),
                'grave' => $a->livre(), // dela, ativa e sem ônus: penhorável à primeira vista
            ];
        }

        return $out;
    }

    /** @return array<array-key, array{descricao: string, grave: bool}>|null */
    private function marcas(string $cnpj): ?array
    {
        if ($this->marcas->referencia() === null) {
            return null; // cópia do INPI nunca carregada: não se aplica
        }

        $r = $this->marcas->daEmpresa($cnpj);

        if (! $r['completa']) {
            return null; // leitura no teto: pode estar cortada
        }

        $out = [];

        foreach ($r['marcas'] as $m) {
            if ($m->estado() === Marca::INATIVA) {
                continue;
            }

            // Estado na chave: o pedido que vira registro (passa a ser penhorável) também alerta.
            $out[$m->numero.':'.($m->emVigor() ? 'registro' : 'pedido')] = [
                'descricao' => sprintf('%s (%s)', $m->descricao(), $m->emVigor() ? 'registro em vigor' : 'pedido: '.($m->situacao ?? 'em andamento')),
                'grave' => false,
            ];
        }

        return $out;
    }

    /** @return array<array-key, array{descricao: string, grave: bool}>|null */
    private function participacoes(string $cnpj): ?array
    {
        $lista = $this->grafo->participations($cnpj, self::PARTICIPACOES);

        if (count($lista) >= self::PARTICIPACOES) {
            return null;
        }

        $out = [];

        foreach ($lista as $p) {
            $out[substr($p->cnpj, 0, 8)] = [
                'descricao' => sprintf('%s (%s) — quotas/ações', $this->nome($p->nome, $p->cnpj), $this->cnpjFormatado($p->cnpj)),
                'grave' => false,
            ];
        }

        return $out;
    }

    /** @return array<array-key, array{descricao: string, grave: bool}>|null */
    private function contratos(string $cnpj): ?array
    {
        $report = $this->contratos->para($cnpj);

        // Portal sem chave, fora (cache de falha) ou leitura no teto: não compara agora.
        if (! $report->configurado || ! $report->disponivel || $report->truncado) {
            return null;
        }

        $out = [];

        foreach (array_filter($report->contratos, fn (ContratoPublico $c): bool => $c->vigente()) as $c) {
            $chave = $c->id !== null ? (string) $c->id : ($c->numero !== null ? 'n'.$c->numero : null);

            if ($chave === null) {
                continue; // sem identificador estável: não dá para saber se é novo
            }

            $partes = array_filter([
                $c->objeto !== null ? mb_strimwidth($c->objeto, 0, self::OBJETO, '…') : null,
                $c->detalhe(),
                $c->valor() !== null ? 'R$ '.number_format((float) $c->valor(), 2, ',', '.') : null,
            ]);
            $out[$chave] = ['descricao' => implode(' · ', $partes), 'grave' => false];
        }

        return $out;
    }

    /** @return array<array-key, array{descricao: string, grave: bool}>|null */
    private function sucessoras(string $cnpj, ?string $marco, bool &$falhou): ?array
    {
        $report = $this->indicios->para($cnpj, $marco);

        if ($report->semMarco()) {
            $falhou = $falhou || ! $report->pgfnConsultada; // PGFN fora = erro; sem dívida = não se aplica

            return null;
        }

        if ($report->foraDaBase) {
            return null; // empresa ainda fora da base da Receita: não se aplica
        }

        // Varredura falhou, ou o histórico (de onde vêm os ex-sócios procurados) caiu:
        // a lista sairia incompleta e sucessora conhecida seria esquecida/re-alertada.
        if ($report->sucessoras === null || $report->saidas === null) {
            $falhou = true;

            return null;
        }

        if ($report->parcial || $report->totalSucessoras > count($report->sucessoras)) {
            return null; // lista cortada: não compara agora
        }

        $out = [];

        foreach ($report->sucessoras as $s) {
            $comuns = $s->sociosEmComum !== [] ? ' · '.implode(', ', array_slice($s->sociosEmComum, 0, 3)) : '';
            $out[substr($s->cnpj, 0, 8)] = [
                'descricao' => sprintf('%s (%s) — %s%s', $this->nome($s->nome, $s->cnpj), $this->cnpjFormatado($s->cnpj), implode(', ', $s->rotulos()), $comuns),
                'grave' => $s->forte(),
            ];
        }

        return $out;
    }

    /** Nome para o alerta (sai por e-mail/webhook): sem o CPF de MEI/EI, nem mascarado. */
    private function nome(string $nome, string $cnpj): string
    {
        return RazaoSocial::semDocumento($nome) ?? $this->cnpjFormatado($cnpj);
    }

    private function cnpjFormatado(string $cnpj): string
    {
        return Cnpj::tryFrom($cnpj)?->formatted() ?? $cnpj;
    }

    /**
     * @param  list<string>  $tipos
     */
    private function semTipo(array $tipos, string $tipo): ?string
    {
        $resto = array_values(array_diff($tipos, [$tipo]));

        return $resto === [] ? null : implode(',', $resto);
    }
}
