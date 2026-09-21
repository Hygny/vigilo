<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Aponta a conexão `cnpj` para um sqlite em memória, para exercitar o comando
 * sem um PostgreSQL de verdade.
 */
function useSqliteCnpjConnection(): void
{
    config()->set('database.connections.cnpj', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
        'foreign_key_constraints' => false,
    ]);

    DB::purge('cnpj');
}

it('reports row counts for the CNPJ base tables', function () {
    useSqliteCnpjConnection();

    $schema = Schema::connection('cnpj');
    foreach (['empresas', 'estabelecimentos', 'socios'] as $table) {
        $schema->create($table, fn (Blueprint $t) => $t->integer('n'));
    }

    DB::connection('cnpj')->table('empresas')->insert([['n' => 1], ['n' => 1]]);
    DB::connection('cnpj')->table('socios')->insert([['n' => 1]]);

    $this->artisan('cnpj:status')
        ->assertSuccessful()
        ->expectsTable(['Tabela', 'Linhas'], [
            ['empresas', '2'],
            ['estabelecimentos', '0'],
            ['socios', '1'],
        ]);
});

it('fails gracefully when the CNPJ base is unavailable', function () {
    useSqliteCnpjConnection();

    // Só 'empresas' existe; a contagem de 'estabelecimentos' lança → o comando
    // reporta o erro e sai com falha, em vez de estourar.
    Schema::connection('cnpj')->create('empresas', fn (Blueprint $t) => $t->integer('n'));

    $this->artisan('cnpj:status')->assertFailed();
});
