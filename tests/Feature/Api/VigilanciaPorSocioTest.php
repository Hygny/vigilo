<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use App\Support\NomeSocio;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Base CNPJ local (sqlite) para os testes de busca por sócio. */
function seedSocioBase(): void
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
        $t->string('situacao_cadastral')->nullable();
        $t->string('data_situacao_cadastral')->nullable();
        $t->string('data_inicio_atividade')->nullable();
        $t->string('nome_fantasia')->nullable();
        $t->string('cnae_fiscal_principal')->nullable();
        $t->string('tipo_logradouro')->nullable();
        $t->string('logradouro')->nullable();
        $t->string('numero')->nullable();
        $t->string('complemento')->nullable();
        $t->string('bairro')->nullable();
        $t->string('cep')->nullable();
        $t->string('municipio')->nullable();
        $t->string('uf')->nullable();
    });
    $schema->create('empresas', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('razao_social')->nullable();
    });
    $schema->create('socios', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('nome_socio')->nullable();
        $t->string('nome_norm')->nullable();
        $t->string('cnpj_cpf_do_socio')->nullable();
        $t->string('identificador_de_socio')->nullable();
        $t->string('qualificacao_do_socio')->nullable();
        $t->string('data_entrada_sociedade')->nullable();
    });
    $schema->create('municipios', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });
    $schema->create('cnaes', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });
    $schema->create('qualificacoes_socios', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });

    DB::connection('cnpj')->table('qualificacoes_socios')->insert(['codigo' => '49', 'descricao' => 'Sócio-Administrador']);

    $matriz = fn (string $b, string $dv, string $sit, string $abertura, string $razao): array => [
        'cnpj_basico' => $b, 'cnpj_ordem' => '0001', 'cnpj_dv' => $dv,
        'situacao_cadastral' => $sit, 'data_inicio_atividade' => $abertura,
    ];

    DB::connection('cnpj')->table('estabelecimentos')->insert([
        $matriz('10000001', '01', '02', '2015-01-01', 'ALFA'),   // ATIVA
        $matriz('10000002', '02', '02', '2020-01-01', 'BETA'),   // ATIVA (mais nova)
        $matriz('10000003', '03', '08', '2018-01-01', 'GAMA'),   // BAIXADA
        $matriz('10000004', '04', '02', '2019-01-01', 'DELTA'),  // ATIVA (outra pessoa)
        $matriz('10000005', '05', '02', '2012-01-01', 'EPSILON'), // ATIVA (nome com acento)
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '10000001', 'razao_social' => 'ALFA LTDA'],
        ['cnpj_basico' => '10000002', 'razao_social' => 'BETA LTDA'],
        ['cnpj_basico' => '10000003', 'razao_social' => 'GAMA LTDA'],
        ['cnpj_basico' => '10000004', 'razao_social' => 'DELTA LTDA'],
        ['cnpj_basico' => '10000005', 'razao_social' => 'EPSILON LTDA'],
    ]);

    $socio = fn (string $b, string $nome): array => [
        'cnpj_basico' => $b, 'nome_socio' => $nome, 'nome_norm' => NomeSocio::norm($nome),
        'cnpj_cpf_do_socio' => '***000**', 'identificador_de_socio' => '2', 'qualificacao_do_socio' => '49',
    ];

    DB::connection('cnpj')->table('socios')->insert([
        $socio('10000001', 'CARLOS EDUARDO BRANDT'),
        $socio('10000002', 'CARLOS EDUARDO BRANDT'),
        $socio('10000003', 'CARLOS EDUARDO BRANDT'),
        $socio('10000004', 'MARIA SOUZA'),
        $socio('10000005', 'JOSÉ DA SILVA'),
    ]);
}

/** Token OSINT (ability `vigilancia:osint`). */
function socioToken(): string
{
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();

    return $user->createToken('osint', ['vigilancia:osint'])->plainTextToken;
}

it('normalizes names (accent, case, spaces)', function () {
    expect(NomeSocio::norm('  José   Carlos '))->toBe('JOSE CARLOS')
        ->and(NomeSocio::norm('MARIA DA CONCEIÇÃO'))->toBe('MARIA DA CONCEICAO');
});

it('rejects an unauthenticated request', function () {
    $this->postJson('/api/v1/vigilancia/empresas/por-socio', ['nomes' => ['x']])->assertUnauthorized();
});

it('rejects a token without the vigilancia:osint ability', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $token = $user->createToken('comum')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/v1/vigilancia/empresas/por-socio', ['nomes' => ['x']])
        ->assertForbidden()
        ->assertJsonPath('error.code', 'sem_permissao');
});

it('finds the companies where a partner appears, newest first, with QSA', function () {
    $token = socioToken();
    seedSocioBase();

    $json = $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/v1/vigilancia/empresas/por-socio', ['nomes' => ['Carlos Eduardo Brandt']])
        ->assertOk()
        ->assertJsonPath('meta.total', 3)
        ->json();

    $cnpjs = array_column($json['data'], 'cnpj');

    // Ordem por abertura desc: BETA (2020) > GAMA (2018) > ALFA (2015).
    expect($cnpjs)->toBe(['10000002000102', '10000003000103', '10000001000101'])
        ->and($json['data'][0]['razao_social'])->toBe('BETA LTDA')
        ->and($json['data'][0]['socios'][0]['nome'])->toBe('CARLOS EDUARDO BRANDT');
});

it('filters by situacao', function () {
    $token = socioToken();
    seedSocioBase();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/v1/vigilancia/empresas/por-socio', ['nomes' => ['Carlos Eduardo Brandt'], 'situacao' => 'ATIVA'])
        ->assertOk()
        ->assertJsonPath('meta.total', 2) // exclui a GAMA (BAIXADA)
        ->assertJsonPath('data.0.cnpj', '10000002000102')
        ->assertJsonPath('data.1.cnpj', '10000001000101');
});

it('matches accent/case-insensitively', function () {
    $token = socioToken();
    seedSocioBase();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/v1/vigilancia/empresas/por-socio', ['nomes' => ['josé da silva']])
        ->assertOk()
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.cnpj', '10000005000105');
});

it('unions multiple names', function () {
    $token = socioToken();
    seedSocioBase();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/v1/vigilancia/empresas/por-socio', ['nomes' => ['Carlos Eduardo Brandt', 'Maria Souza']])
        ->assertOk()
        ->assertJsonPath('meta.total', 4); // 3 do Carlos + 1 da Maria
});

it('respects the limite', function () {
    $token = socioToken();
    seedSocioBase();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/v1/vigilancia/empresas/por-socio', ['nomes' => ['Carlos Eduardo Brandt'], 'limite' => 1])
        ->assertOk()
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.cnpj', '10000002000102');
});

it('returns an empty list for empty nomes', function () {
    $token = socioToken();
    seedSocioBase();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/v1/vigilancia/empresas/por-socio', ['nomes' => []])
        ->assertOk()
        ->assertJsonPath('meta.total', 0)
        ->assertExactJson(['data' => [], 'meta' => ['total' => 0, 'base_referencia' => null]]);
});

it('returns 503 when the base is unavailable', function () {
    $token = socioToken();

    // Conexão sqlite sem tabelas → a consulta lança QueryException → 503.
    config()->set('database.connections.cnpj', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
        'foreign_key_constraints' => false,
    ]);
    DB::purge('cnpj');

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/v1/vigilancia/empresas/por-socio', ['nomes' => ['Carlos Eduardo Brandt']])
        ->assertStatus(503)
        ->assertJsonPath('error.code', 'base_indisponivel');
});

it('rejects an invalid situacao', function () {
    $token = socioToken();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/v1/vigilancia/empresas/por-socio', ['nomes' => ['x'], 'situacao' => 'FOO'])
        ->assertStatus(422)
        ->assertJsonPath('error.code', 'situacao_invalida');
});

it('audits the queried names (LGPD)', function () {
    $token = socioToken();
    seedSocioBase();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/v1/vigilancia/empresas/por-socio', ['nomes' => ['Carlos Eduardo Brandt']])
        ->assertOk();

    $log = DB::table('api_access_logs')->where('path', 'api/v1/vigilancia/empresas/por-socio')->first();
    expect($log)->not->toBeNull()
        ->and($log->method)->toBe('POST')
        ->and($log->params)->toContain('Carlos Eduardo Brandt'); // nome fica na trilha
});
