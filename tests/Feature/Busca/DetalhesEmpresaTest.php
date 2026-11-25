<?php

declare(strict_types=1);

use App\DTO\Vigilancia\DetalhesEmpresa;
use App\DTO\Vigilancia\RegimeSimples;
use App\Services\Vigilancia\DetalhesEmpresaLookup;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

it('describes each Simples/MEI situation in plain pt-BR', function () {
    expect((new RegimeSimples(true, '2020-01-01'))->texto())->toBe('Optante desde 01/01/2020')
        ->and((new RegimeSimples(true))->texto())->toBe('Optante')
        ->and((new RegimeSimples(false, '2019-01-01', '2021-06-30'))->texto())->toBe('Não optante — excluída em 30/06/2021 (optou em 01/01/2019)')
        ->and((new RegimeSimples(false))->texto())->toBe('Não optante');
});

it('formats legal nature, size and capital, and hides what is missing', function () {
    $d = new DetalhesEmpresa('2062', 'Sociedade Empresária Limitada', 'EPP', 1234567.8);

    expect($d->natureza())->toBe('206-2 — Sociedade Empresária Limitada')
        ->and($d->porteTexto())->toBe('Empresa de pequeno porte (EPP)')
        ->and($d->capitalTexto())->toBe('R$ 1.234.567,80');

    $vazio = new DetalhesEmpresa(null, null, null, null);
    expect($vazio->natureza())->toBeNull()
        ->and($vazio->porteTexto())->toBeNull()
        ->and($vazio->capitalTexto())->toBeNull();
});

it('reads a company that never opted for the Simples as "não optante" and returns null outside the base', function () {
    useCnpjSqlite();
    $schema = Schema::connection('cnpj');
    $schema->create('empresas', function (Blueprint $t): void {
        foreach (['cnpj_basico', 'natureza_juridica', 'porte', 'capital_social'] as $c) {
            $t->string($c)->nullable();
        }
    });
    $schema->create('naturezas_juridicas', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });
    $schema->create('dados_simples', function (Blueprint $t): void {
        foreach (['cnpj_basico', 'opcao_pelo_simples', 'data_opcao_pelo_simples', 'data_exclusao_do_simples', 'opcao_pelo_mei', 'data_opcao_pelo_mei', 'data_exclusao_do_mei'] as $c) {
            $t->string($c)->nullable();
        }
    });
    DB::connection('cnpj')->table('empresas')->insert(['cnpj_basico' => '11222333', 'natureza_juridica' => '2062', 'porte' => '05', 'capital_social' => '0']);

    $lookup = app(DetalhesEmpresaLookup::class);
    $d = $lookup->para('11222333000181');

    expect($d?->simples?->texto())->toBe('Não optante')
        ->and($d?->mei?->texto())->toBe('Não optante')
        ->and($d?->porteTexto())->toBe('Demais')
        ->and($d?->capitalTexto())->toBe('R$ 0,00')
        ->and($lookup->para('99888777000166'))->toBeNull();
});
