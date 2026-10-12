<?php

declare(strict_types=1);

use App\Support\AddressNumber;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Guarda de DRIFT entre a normalização de número em PHP ({@see AddressNumber::normalize})
 * e em SQL ({@see AddressNumber::sqlExpression}) — a lista de tokens vive em 3
 * lugares (const + 2 literais SQL). Se qualquer uma divergir, uma variante deixa
 * de casar e este teste quebra.
 */
function bootNumeroTable(): void
{
    config()->set('database.connections.cnpj', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
        'foreign_key_constraints' => false,
    ]);

    DB::purge('cnpj');
    Schema::connection('cnpj')->create('estabelecimentos', function (Blueprint $t): void {
        $t->string('numero')->nullable();
    });
}

it('SQL normaliza o número igual ao PHP normalize() em cada variante (sem drift)', function () {
    bootNumeroTable();

    // Todas as variantes de prefixo "número" + espaços + casos "sem número".
    $variantes = ['320', 'Nº 320', 'N° 320', 'N.º 320', 'N.° 320', 'Nº. 320', 'N º 320', 'N ° 320', '  320  ', 'SN', 'S/N', ''];

    foreach ($variantes as $v) {
        DB::connection('cnpj')->table('estabelecimentos')->insert(['numero' => $v]);

        // A linha (numero = $v) casa quando a normalização SQL de $v é igual à
        // normalização PHP de $v — ou seja, PHP e SQL concordam para essa variante.
        $parePHPeSQL = DB::connection('cnpj')->table('estabelecimentos as e')
            ->where('e.numero', $v)
            ->where(DB::raw(AddressNumber::sqlExpression('e.numero')), '=', AddressNumber::normalize($v))
            ->exists();

        expect($parePHPeSQL)->toBeTrue("Divergência PHP↔SQL na variante [{$v}]");
    }
});

it('não casa número diferente (controle)', function () {
    bootNumeroTable();
    DB::connection('cnpj')->table('estabelecimentos')->insert([['numero' => 'Nº 320'], ['numero' => '500']]);

    $casam320 = DB::connection('cnpj')->table('estabelecimentos as e')
        ->where(DB::raw(AddressNumber::sqlExpression('e.numero')), '=', AddressNumber::normalize('320'))
        ->count();

    expect($casam320)->toBe(1); // só o "Nº 320", nunca o 500
});
