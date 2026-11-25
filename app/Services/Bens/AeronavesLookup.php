<?php

declare(strict_types=1);

namespace App\Services\Bens;

use App\DTO\Bens\Aeronave;
use App\Support\NomeSocio;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Aeronaves na cópia local do RAB (ANAC). Por EMPRESA: pela raiz do CNPJ do
 * proprietário/operador — vínculo certo. Por PESSOA: pelo nome normalizado —
 * vínculo POSSÍVEL (a ANAC não publica o CPF inteiro e mascara dígitos
 * diferentes dos da Receita; homônimo é possível). Matrícula cancelada sai
 * marcada (histórico, não bem atual).
 */
final class AeronavesLookup
{
    /** Aeronaves listadas por consulta (ativas primeiro); o total vem de contagemDaEmpresa(). */
    public const LIMITE = 200;

    /**
     * Aeronave ATIVA no SQL (ordenação e contagem) — espelha Aeronave::ativa():
     * sem data de cancelamento e sem os marcadores de cancelada/reservada no
     * gravame. Os marcadores entram por binding ({@see bindingsAtiva()}).
     */
    private const SQL_ATIVA = "a.data_cancelamento is null and upper(coalesce(a.gravame, '')) not like ? and upper(coalesce(a.gravame, '')) not like ?";

    /** A cópia já foi carregada alguma vez? (senão: "não consultado", nunca "nenhuma") */
    public function disponivel(): bool
    {
        return DB::table('aeronave_cargas')->exists();
    }

    public function referencia(): ?string
    {
        $data = DB::table('aeronave_cargas')->orderByDesc('id')->value('data_arquivo');

        return is_string($data) ? substr($data, 0, 10) : null;
    }

    /** @return list<Aeronave> as primeiras LIMITE (ativas primeiro) */
    public function daEmpresa(string $cnpj): array
    {
        $basico = $this->basico($cnpj);

        return $basico !== null ? $this->buscar(fn (Builder $q) => $q->where('v.tipo', Aeronave::TIPO_PJ)->where('v.cnpj_basico', $basico)) : [];
    }

    /**
     * Totais da empresa, para o "mostrando N de M" quando a lista bate no LIMITE.
     *
     * @return array{total: int, ativas: int}
     */
    public function contagemDaEmpresa(string $cnpj): array
    {
        $basico = $this->basico($cnpj);

        if ($basico === null) {
            return ['total' => 0, 'ativas' => 0];
        }

        $r = DB::table('aeronave_vinculos as v')
            ->join('aeronaves as a', 'a.marca', '=', 'v.marca')
            ->where('v.tipo', Aeronave::TIPO_PJ)
            ->where('v.cnpj_basico', $basico)
            ->selectRaw('count(distinct v.marca) as total, count(distinct case when '.self::SQL_ATIVA.' then v.marca end) as ativas', self::bindingsAtiva())
            ->first();

        return ['total' => (int) ($r->total ?? 0), 'ativas' => (int) ($r->ativas ?? 0)];
    }

    /** @return list<Aeronave> */
    public function daPessoa(string $nome): array
    {
        $norm = NomeSocio::norm($nome);

        return $norm !== '' ? $this->buscar(fn (Builder $q) => $q->where('v.tipo', Aeronave::TIPO_PF)->where('v.nome_norm', $norm)) : [];
    }

    /** @return list<string> */
    private static function bindingsAtiva(): array
    {
        return ['%'.Aeronave::GRAVAME_CANCELADA.'%', '%'.Aeronave::GRAVAME_RESERVADA.'%'];
    }

    private function basico(string $cnpj): ?string
    {
        $basico = substr(preg_replace('/\D/', '', $cnpj) ?? '', 0, 8);

        return strlen($basico) === 8 ? $basico : null;
    }

    /**
     * O teto vale por AERONAVE (não por linha de vínculo): primeiro as marcas
     * (ativas primeiro), depois os vínculos delas.
     *
     * @param  callable(Builder): mixed  $filtro
     * @return list<Aeronave>
     */
    private function buscar(callable $filtro): array
    {
        $marcas = DB::table('aeronave_vinculos as v')
            ->join('aeronaves as a', 'a.marca', '=', 'v.marca')
            ->tap($filtro)
            ->groupBy('v.marca', 'a.data_cancelamento', 'a.gravame')
            ->orderByRaw('case when '.self::SQL_ATIVA.' then 0 else 1 end', self::bindingsAtiva())
            ->orderBy('v.marca')
            ->limit(self::LIMITE)
            ->pluck('v.marca')
            ->map(fn ($m): string => (string) $m)
            ->all();

        if ($marcas === []) {
            return [];
        }

        $linhas = DB::table('aeronave_vinculos as v')
            ->join('aeronaves as a', 'a.marca', '=', 'v.marca')
            ->tap($filtro)
            ->whereIn('v.marca', $marcas)
            ->get(['a.*', 'v.papel', 'v.percentual', 'v.uf'])
            ->groupBy('marca');

        $out = [];

        foreach ($marcas as $marca) {
            $vinculos = $linhas->get($marca);
            $a = $vinculos?->first();

            if ($vinculos === null || $a === null) {
                continue;
            }

            // % e UF vêm do(s) vínculo(s) de PROPRIETÁRIO — a linha de operador
            // não os sobrescreve. Matriz e filial coproprietárias: soma (é a mesma empresa).
            $donos = $vinculos->where('papel', Aeronave::PAPEL_PROPRIETARIO);
            $percentuais = $donos->pluck('percentual')->filter(fn ($p): bool => $p !== null);
            $papeis = array_values(array_filter(Aeronave::PAPEIS, fn (string $p): bool => $vinculos->contains('papel', $p)));
            $dono = $donos->first();
            $uf = $dono !== null && $dono->uf !== null ? $dono->uf : $a->uf;

            $out[] = new Aeronave(
                marca: $marca,
                modelo: $a->modelo !== null ? (string) $a->modelo : null,
                fabricante: $a->fabricante !== null ? (string) $a->fabricante : null,
                ano: $a->ano_fabricacao !== null ? (int) $a->ano_fabricacao : null,
                gravame: $a->gravame !== null ? (string) $a->gravame : null,
                comDataCancelamento: $a->data_cancelamento !== null,
                papeis: $papeis,
                percentual: $percentuais->isNotEmpty() ? min(100.0, (float) $percentuais->sum()) : null,
                uf: $uf !== null ? (string) $uf : null,
            );
        }

        return $out;
    }
}
