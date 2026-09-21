<?php

declare(strict_types=1);

use App\Contracts\CnpjDataProvider;
use App\Providers\Cnpj\LocalCnpjProvider;
use App\Support\Exceptions\InvalidCnpjException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Aponta a conexão `cnpj` para um sqlite em memória e cria as tabelas do dump
 * da Receita (só as colunas que o provider consulta), para exercitar o
 * LocalCnpjProvider sem um PostgreSQL de verdade.
 */
function bootCnpjBase(): void
{
    config()->set('database.connections.cnpj', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
        'foreign_key_constraints' => false,
    ]);

    DB::purge('cnpj');
    $schema = Schema::connection('cnpj');

    $schema->create('estabelecimentos', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('cnpj_ordem');
        $t->string('cnpj_dv');
        $t->string('nome_fantasia')->nullable();
        $t->string('situacao_cadastral')->nullable();
        $t->string('data_situacao_cadastral')->nullable();
        $t->string('cnae_fiscal_principal')->nullable();
        $t->string('tipo_logradouro')->nullable();
        $t->string('logradouro')->nullable();
        $t->string('numero')->nullable();
        $t->string('uf')->nullable();
        $t->string('municipio')->nullable();
    });
    $schema->create('empresas', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('razao_social')->nullable();
        $t->string('porte')->nullable();
        $t->string('natureza_juridica')->nullable();
    });
    $schema->create('municipios', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });
    $schema->create('naturezas_juridicas', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });
    $schema->create('socios', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('nome_socio')->nullable();
        $t->string('cnpj_cpf_do_socio')->nullable();
        $t->string('qualificacao_do_socio')->nullable();
    });
    $schema->create('qualificacoes_socios', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });
}

function seedExampleCompany(string $situacao = '08', string $municipio = '7107'): void
{
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        'cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81',
        'nome_fantasia' => 'EXEMPLO', 'situacao_cadastral' => $situacao,
        'data_situacao_cadastral' => '2020-05-10', 'cnae_fiscal_principal' => '6201501',
        'tipo_logradouro' => 'RUA', 'logradouro' => 'DAS FLORES', 'numero' => '100',
        'uf' => 'SP', 'municipio' => $municipio,
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        'cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA EXEMPLO LTDA',
        'porte' => '05', 'natureza_juridica' => '2062',
    ]);
    DB::connection('cnpj')->table('municipios')->insert(['codigo' => '7107', 'descricao' => 'SAO PAULO']);
    DB::connection('cnpj')->table('naturezas_juridicas')->insert(['codigo' => '2062', 'descricao' => 'Sociedade Empresária Limitada']);
    DB::connection('cnpj')->table('qualificacoes_socios')->insert([
        ['codigo' => '49', 'descricao' => 'Sócio-Administrador'],
        ['codigo' => '22', 'descricao' => 'Sócio'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'FULANO DE TAL', 'cnpj_cpf_do_socio' => '***123456**', 'qualificacao_do_socio' => '49'],
        ['cnpj_basico' => '11222333', 'nome_socio' => 'BELTRANO', 'cnpj_cpf_do_socio' => '***654321**', 'qualificacao_do_socio' => '22'],
    ]);
}

it('assembles CompanyData from the local base, translating codes and resolving references', function () {
    bootCnpjBase();
    seedExampleCompany(); // situação 08 = BAIXADA

    $data = (new LocalCnpjProvider('cnpj'))->fetch('11.222.333/0001-81');

    expect($data)->not->toBeNull()
        ->and($data->cnpj)->toBe('11222333000181')
        ->and($data->razaoSocial)->toBe('EMPRESA EXEMPLO LTDA')
        ->and($data->nomeFantasia)->toBe('EXEMPLO')
        ->and($data->situacaoCadastral)->toBe('BAIXADA')   // código 08 → texto
        ->and($data->situacaoData)->toBe('2020-05-10')
        ->and($data->cnaePrincipal)->toBe('6201501')
        ->and($data->porte)->toBe('DEMAIS')                // código 05 → rótulo
        ->and($data->naturezaJuridica)->toBe('Sociedade Empresária Limitada')
        ->and($data->logradouro)->toBe('RUA DAS FLORES, 100')
        ->and($data->municipio)->toBe('SAO PAULO')         // código 7107 → nome (join)
        ->and($data->uf)->toBe('SP')
        ->and($data->partners)->toHaveCount(2);

    expect($data->partners[0]->nome)->toBe('FULANO DE TAL')
        ->and($data->partners[0]->documento)->toBe('***123456**')
        ->and($data->partners[0]->qualificacao)->toBe('Sócio-Administrador'); // código 49 → texto
});

it('maps an active status and falls back to the municipality code when unmapped', function () {
    bootCnpjBase();
    seedExampleCompany(situacao: '02', municipio: '9999'); // 02 = ATIVA, 9999 sem referência

    $data = (new LocalCnpjProvider('cnpj'))->fetch('11222333000181');

    expect($data)->not->toBeNull()
        ->and($data->situacaoCadastral)->toBe('ATIVA')
        ->and($data->municipio)->toBe('9999'); // sem linha em municipios → cai no código
});

it('degrades gracefully on unmapped codes and missing reference rows', function () {
    bootCnpjBase();

    DB::connection('cnpj')->table('estabelecimentos')->insert([
        'cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81',
        'situacao_cadastral' => '99', 'municipio' => '0000', 'uf' => 'SP', // situação fora do mapa, município sem ref
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        'cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA X', 'porte' => '00', 'natureza_juridica' => '0000',
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        'cnpj_basico' => '11222333', 'nome_socio' => 'FULANO', 'cnpj_cpf_do_socio' => '***1**', 'qualificacao_do_socio' => '99',
    ]);

    $data = (new LocalCnpjProvider('cnpj'))->fetch('11222333000181');

    expect($data)->not->toBeNull()
        ->and($data->porte)->toBeNull()                     // porte 00 → null
        ->and($data->situacaoCadastral)->toBe('')           // código fora do mapa → '' (baseline não alerta)
        ->and($data->naturezaJuridica)->toBe('0000')        // sem linha de ref → cai no código
        ->and($data->municipio)->toBe('0000')               // sem linha de ref → cai no código
        ->and($data->partners[0]->qualificacao)->toBeNull(); // sem linha de ref → null
});

it('returns null when the CNPJ is not in the local base', function () {
    bootCnpjBase(); // tabelas vazias

    expect((new LocalCnpjProvider('cnpj'))->fetch('11444777000161'))->toBeNull();
});

it('throws on a malformed CNPJ (never confused with not found)', function () {
    bootCnpjBase();

    expect(fn () => (new LocalCnpjProvider('cnpj'))->fetch('123'))
        ->toThrow(InvalidCnpjException::class);
});

it('resolves the local driver from the container when configured', function () {
    config()->set('cnpj.driver', 'local');

    expect(app(CnpjDataProvider::class))->toBeInstanceOf(LocalCnpjProvider::class);
});
