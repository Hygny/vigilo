<?php

declare(strict_types=1);

use App\Support\BuscaNome;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Base CNPJ (conexão `cnpj`) num SQLite em memória — para testes que leem a
 * base da Receita/PGFN sem o Postgres da VM.
 */
function useCnpjSqlite(): void
{
    config()->set('database.connections.cnpj', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
        'foreign_key_constraints' => false,
    ]);

    DB::purge('cnpj');
}

/** Tabelas da PGFN inteira na base CNPJ (o que o deploy/cnpj-db/bin/pgfn.sh carrega). */
function createPgfnTables(): void
{
    $schema = Schema::connection('cnpj');

    $schema->create('pgfn_dividas', function (Blueprint $t): void {
        $t->string('cnpj');
        $t->string('cnpj_basico')->index();
        $t->string('tipo');
        $t->string('tipo_devedor')->nullable();
        $t->string('numero_inscricao')->nullable();
        $t->string('tipo_situacao')->nullable();
        $t->string('situacao')->nullable();
        $t->string('receita')->nullable();
        $t->date('data_inscricao')->nullable();
        $t->boolean('ajuizado')->default(false);
        $t->decimal('valor', 16, 2)->nullable();
    });

    $schema->create('pgfn_cargas', function (Blueprint $t): void {
        $t->string('trimestre')->primary();
        $t->bigInteger('inscricoes');
        $t->timestamp('carregado_em')->nullable();
    });
}

/** Uma inscrição em dívida ativa (PGFN) de um CNPJ. */
function seedPgfnDivida(string $cnpj, float $valor, bool $ajuizado = false, string $tipo = 'nao-previdenciario'): void
{
    DB::connection('cnpj')->table('pgfn_dividas')->insert([
        'cnpj' => $cnpj,
        'cnpj_basico' => substr($cnpj, 0, 8),
        'tipo' => $tipo,
        'tipo_devedor' => 'PRINCIPAL',
        'situacao' => 'ATIVA AJUIZADA',
        'receita' => 'IRPJ',
        'ajuizado' => $ajuizado,
        'valor' => $valor,
    ]);
}

/** Tabelas do histórico cadastral na base CNPJ (deploy/cnpj-db/sql/historico.sql). */
function createHistoricoTables(): void
{
    $schema = Schema::connection('cnpj');

    $schema->create('historico_cadastral', function (Blueprint $t): void {
        $t->id();
        $t->string('cnpj_basico')->index();
        $t->string('cnpj_ordem')->nullable();
        $t->string('cnpj_dv')->nullable();
        $t->string('campo');
        $t->text('valor_anterior')->nullable();
        $t->text('valor_novo')->nullable();
        $t->timestamp('detectado_em');
        $t->string('referencia')->nullable();
    });

    $schema->create('historico_execucoes', function (Blueprint $t): void {
        $t->string('referencia')->primary();
        $t->string('status');
    });
}

/**
 * Um evento do histórico cadastral. `$cnpj` de 14 díg = evento do
 * estabelecimento; de 8 díg (raiz) = evento da empresa.
 */
function seedHistorico(string $cnpj, string $campo, ?string $antes, ?string $depois, string $quando = '2026-11-20 06:00:00', ?string $referencia = '2026-11'): void
{
    DB::connection('cnpj')->table('historico_cadastral')->insert([
        'cnpj_basico' => substr($cnpj, 0, 8),
        'cnpj_ordem' => strlen($cnpj) === 14 ? substr($cnpj, 8, 4) : null,
        'cnpj_dv' => strlen($cnpj) === 14 ? substr($cnpj, 12, 2) : null,
        'campo' => $campo,
        'valor_anterior' => $antes,
        'valor_novo' => $depois,
        'detectado_em' => $quando,
        'referencia' => $referencia,
    ]);
}

/**
 * Base CNPJ (sqlite) com as tabelas da busca, chaves geradas pelo normalizador
 * PHP. Cada empresa: [cnpj, razão social, nome fantasia, situação, uf, matriz?].
 *
 * @param  list<array<int, mixed>>  $empresas
 */
function seedBuscaBase(array $empresas): void
{
    useCnpjSqlite();
    $schema = Schema::connection('cnpj');

    $schema->create('estabelecimentos', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('cnpj_ordem');
        $t->string('cnpj_dv');
        $t->integer('identificador_matriz_filial')->nullable();
        $t->string('nome_fantasia')->nullable();
        $t->string('situacao_cadastral')->nullable();
        $t->string('uf')->nullable();
        $t->string('municipio')->nullable();
        $t->string('data_inicio_atividade')->nullable();
    });
    $schema->create('empresas', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('razao_social')->nullable();
    });
    $schema->create('municipios', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });
    $schema->create('busca_razao', function (Blueprint $t): void {
        $t->string('cnpj_basico')->primary();
        $t->string('nome');
        $t->string('uf')->nullable();
        $t->string('situacao')->nullable();
    });
    $schema->create('busca_fantasia', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('cnpj_ordem');
        $t->string('cnpj_dv');
        $t->string('nome');
        $t->string('uf')->nullable();
        $t->string('situacao')->nullable();
    });

    $db = DB::connection('cnpj');
    $db->table('municipios')->insert(['codigo' => '7107', 'descricao' => 'SAO PAULO']);

    foreach ($empresas as $e) {
        [$cnpj, $razao, $fantasia, $situacao, $uf] = $e;
        $matriz = $e[5] ?? true;
        $basico = substr($cnpj, 0, 8);

        $db->table('estabelecimentos')->insert([
            'cnpj_basico' => $basico, 'cnpj_ordem' => substr($cnpj, 8, 4), 'cnpj_dv' => substr($cnpj, 12, 2),
            'identificador_matriz_filial' => $matriz ? 1 : 2, 'nome_fantasia' => $fantasia,
            'situacao_cadastral' => $situacao, 'uf' => $uf, 'municipio' => '7107', 'data_inicio_atividade' => '2020-01-01',
        ]);

        if ($razao !== null && $db->table('empresas')->where('cnpj_basico', $basico)->doesntExist()) {
            $db->table('empresas')->insert(['cnpj_basico' => $basico, 'razao_social' => $razao]);
            // UF/situação da razão social = as da matriz (como no busca-popular.sh).
            $db->table('busca_razao')->insert(['cnpj_basico' => $basico, 'nome' => BuscaNome::chave($razao), 'uf' => $uf, 'situacao' => $situacao]);
        }

        if ($fantasia !== null && trim(BuscaNome::chave($fantasia)) !== '') {
            $db->table('busca_fantasia')->insert([
                'cnpj_basico' => $basico, 'cnpj_ordem' => substr($cnpj, 8, 4), 'cnpj_dv' => substr($cnpj, 12, 2),
                'nome' => BuscaNome::chave($fantasia), 'uf' => $uf, 'situacao' => $situacao,
            ]);
        }
    }
}
