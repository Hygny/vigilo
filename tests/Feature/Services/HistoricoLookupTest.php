<?php

declare(strict_types=1);

use App\Services\Vigilancia\HistoricoLookup;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function (): void {
    useCnpjSqlite();
    createHistoricoTables();

    $schema = Schema::connection('cnpj');
    foreach (['municipios', 'cnaes', 'naturezas_juridicas', 'motivos', 'qualificacoes_socios'] as $tabela) {
        $schema->create($tabela, function (Blueprint $t): void {
            $t->string('codigo');
            $t->string('descricao')->nullable();
        });
    }

    DB::connection('cnpj')->table('municipios')->insert(['codigo' => '7107', 'descricao' => 'SAO PAULO']);
    DB::connection('cnpj')->table('cnaes')->insert(['codigo' => '4712100', 'descricao' => 'Minimercados']);
    DB::connection('cnpj')->table('qualificacoes_socios')->insert(['codigo' => '49', 'descricao' => 'Sócio-Administrador']);
    DB::connection('cnpj')->table('historico_execucoes')->insert([
        ['referencia' => '2026-09', 'status' => 'primeira_carga'],
        ['referencia' => '2026-10', 'status' => 'ok'],
        ['referencia' => '2026-11', 'status' => 'ok'],
    ]);
});

function enderecoJson(string $logradouro, string $numero, string $cep = '01001000'): string
{
    return json_encode([
        'tipo_logradouro' => 'RUA', 'logradouro' => $logradouro, 'numero' => $numero, 'complemento' => null,
        'bairro' => 'CENTRO', 'cep' => $cep, 'municipio' => '7107', 'uf' => 'SP',
    ], JSON_THROW_ON_ERROR);
}

it('formats the events of the company and of its own establishment, newest first', function () {
    seedHistorico('11222333000181', 'situacao', '02', '08', '2026-11-20 06:00:00');
    seedHistorico('11222333000181', 'endereco', enderecoJson('A', '10'), enderecoJson('B', '20', '01002000'), '2026-10-19 06:00:00', '2026-10');
    seedHistorico('11222333', 'capital_social', '1000', '50000.5', '2026-11-20 06:00:00');
    seedHistorico('11222333', 'socio_saida', json_encode(['nome' => 'BRUNO LIMA', 'documento' => '***333444**', 'qualificacao' => '49']), null, '2026-10-19 06:00:00', '2026-10');
    seedHistorico('11222333000262', 'situacao', '02', '08'); // filial irmã: fora
    seedHistorico('99888777000166', 'situacao', '02', '04'); // outra empresa: fora

    $historico = app(HistoricoLookup::class)->para('11.222.333/0001-81', agora: Carbon::parse('2026-11-25'));

    $porCampo = collect($historico->eventos)->keyBy('campo');

    expect($historico->eventos)->toHaveCount(4)
        ->and($historico->eventos[0]->detectadoEm)->toBe('2026-11-20')
        ->and($porCampo['situacao']->rotulo)->toBe('Situação cadastral')
        ->and($porCampo['situacao']->antes)->toBe('ATIVA')
        ->and($porCampo['situacao']->depois)->toBe('BAIXADA')
        ->and($porCampo['situacao']->estabelecimento)->toBe('11222333000181')
        ->and($porCampo['endereco']->antes)->toBe('RUA A, 10 — CENTRO — SAO PAULO/SP — CEP 01001-000')
        ->and($porCampo['endereco']->depois)->toBe('RUA B, 20 — CENTRO — SAO PAULO/SP — CEP 01002-000')
        ->and($porCampo['capital_social']->depois)->toBe('R$ 50.000,50')
        ->and($porCampo['capital_social']->estabelecimento)->toBeNull()
        ->and($porCampo['socio_saida']->antes)->toBe('BRUNO LIMA (***333444**) — Sócio-Administrador')
        ->and($porCampo['socio_saida']->depois)->toBeNull()
        ->and($historico->desde)->toBe('2026-10');
});

it('counts the 12-month signals (address changes etc.) and flags frequent moves', function () {
    foreach (['2026-03-01', '2026-06-01', '2026-09-01'] as $i => $quando) {
        seedHistorico('11222333000181', 'endereco', enderecoJson('R'.$i, '1'), enderecoJson('R'.($i + 1), '1'), $quando.' 06:00:00');
    }
    seedHistorico('11222333000181', 'endereco', enderecoJson('X', '1'), enderecoJson('Y', '1'), '2025-01-01 06:00:00'); // > 12 meses
    seedHistorico('11222333', 'socio_entrada', null, json_encode(['nome' => 'CARLA']), '2026-10-19 06:00:00');

    $historico = app(HistoricoLookup::class)->para('11222333000181', agora: Carbon::parse('2026-11-25'));

    expect($historico->sinais12m)->toBe([
        'mudancas_endereco' => 3,
        'mudancas_situacao' => 0,
        'mudancas_razao_social' => 0,
        'entradas_socios' => 1,
        'saidas_socios' => 0,
    ])->and($historico->alertaEndereco())->toBeTrue();
});

it('masks a full CPF that would leak through a partner or a MEI company name', function () {
    seedHistorico('11222333', 'socio_entrada', null, json_encode(['nome' => 'ANA', 'documento' => '12345678901']));
    seedHistorico('11222333', 'razao_social', 'ANA SOUZA 12345678901', 'ANA SOUZA COMERCIO LTDA');

    $porCampo = collect(app(HistoricoLookup::class)->para('11222333000181')->eventos)->keyBy('campo');

    expect($porCampo['socio_entrada']->depois)->toBe('ANA (***456789**)')
        ->and($porCampo['razao_social']->antes)->toBe('ANA SOUZA ***456789**');
});

it('falls back to the raw code when a description table is missing', function () {
    Schema::connection('cnpj')->drop('cnaes');
    seedHistorico('11222333000181', 'cnae_principal', '4712100', '4711302');

    $evento = app(HistoricoLookup::class)->para('11222333000181')->eventos[0];

    expect($evento->antes)->toBe('4712100')->and($evento->depois)->toBe('4711302');
});

it('returns an empty history (not an error) when nothing changed', function () {
    $historico = app(HistoricoLookup::class)->para('11222333000181');

    expect($historico->eventos)->toBe([])
        ->and($historico->mudancasEndereco12m())->toBe(0)
        ->and($historico->alertaEndereco())->toBeFalse();
});

it('returns null for an invalid CNPJ and throws when the base is unavailable', function () {
    expect(app(HistoricoLookup::class)->para('123'))->toBeNull();

    Schema::connection('cnpj')->drop('historico_cadastral');

    app(HistoricoLookup::class)->para('11222333000181');
})->throws(QueryException::class);
