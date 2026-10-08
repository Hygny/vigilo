<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Tabela socios mínima (sqlite) com a coluna nome_norm já existente. */
function seedSociosParaNormalizar(): void
{
    config()->set('database.connections.cnpj', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
        'foreign_key_constraints' => false,
    ]);

    DB::purge('cnpj');
    Schema::connection('cnpj')->create('socios', function (Blueprint $t): void {
        $t->string('socio_id');
        $t->string('cnpj_basico')->nullable();
        $t->string('nome_socio')->nullable();
        $t->string('nome_norm')->nullable();
    });

    DB::connection('cnpj')->table('socios')->insert([
        ['socio_id' => 'a1', 'cnpj_basico' => '1', 'nome_socio' => 'José da Silva', 'nome_norm' => null],
        ['socio_id' => 'a2', 'cnpj_basico' => '2', 'nome_socio' => '  Maria   Souza ', 'nome_norm' => null],
        ['socio_id' => 'a3', 'cnpj_basico' => '3', 'nome_socio' => 'JOAO', 'nome_norm' => 'VALOR-ANTIGO'],
    ]);
}

it('normalizes only the pending rows by default (incremental/resumable)', function () {
    seedSociosParaNormalizar();

    $this->artisan('vigilo:normalizar-socios')->assertSuccessful();

    $norm = DB::connection('cnpj')->table('socios')->pluck('nome_norm', 'socio_id');

    expect($norm['a1'])->toBe('JOSE DA SILVA')
        ->and($norm['a2'])->toBe('MARIA SOUZA')
        ->and($norm['a3'])->toBe('VALOR-ANTIGO'); // já preenchido → o incremental não toca
});

it('advances across multiple chunks (chunk=1)', function () {
    seedSociosParaNormalizar(); // a1 e a2 pendentes (nome_norm null)

    $this->artisan('vigilo:normalizar-socios --chunk=1')->assertSuccessful();

    $norm = DB::connection('cnpj')->table('socios')->pluck('nome_norm', 'socio_id');

    expect($norm['a1'])->toBe('JOSE DA SILVA')
        ->and($norm['a2'])->toBe('MARIA SOUZA'); // os dois pendentes normalizados em chunks separados
});

it('reprocesses everything with --all', function () {
    seedSociosParaNormalizar();

    $this->artisan('vigilo:normalizar-socios --all')->assertSuccessful();

    $norm = DB::connection('cnpj')->table('socios')->pluck('nome_norm', 'socio_id');

    expect($norm['a3'])->toBe('JOAO'); // --all recalcula o já preenchido
});

it('adds the nome_norm column when missing', function () {
    config()->set('database.connections.cnpj', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
        'foreign_key_constraints' => false,
    ]);
    DB::purge('cnpj');
    Schema::connection('cnpj')->create('socios', function (Blueprint $t): void {
        $t->string('socio_id');
        $t->string('nome_socio')->nullable();
        // sem nome_norm de propósito
    });
    DB::connection('cnpj')->table('socios')->insert([
        ['socio_id' => 'b1', 'nome_socio' => 'ANA LÚCIA'],
    ]);

    $this->artisan('vigilo:normalizar-socios')->assertSuccessful();

    expect(Schema::connection('cnpj')->hasColumn('socios', 'nome_norm'))->toBeTrue()
        ->and(DB::connection('cnpj')->table('socios')->where('socio_id', 'b1')->value('nome_norm'))->toBe('ANA LUCIA');
});
