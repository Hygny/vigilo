<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Base CNPJ local (sqlite) para os testes de busca por endereço. */
function seedEnderecoBase(): void
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

    DB::connection('cnpj')->table('municipios')->insert(['codigo' => '4106902', 'descricao' => 'CURITIBA']);
    DB::connection('cnpj')->table('cnaes')->insert(['codigo' => '8593700', 'descricao' => 'Ensino de idiomas']);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11111111', 'razao_social' => 'EMPRESA A'],
        ['cnpj_basico' => '22222222', 'razao_social' => 'EMPRESA B'],
        ['cnpj_basico' => '33333333', 'razao_social' => 'EMPRESA C'],
        ['cnpj_basico' => '44444444', 'razao_social' => 'EMPRESA D'],
        ['cnpj_basico' => '55555555', 'razao_social' => 'OUTRO CEP'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '22222222', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
    ]);

    $est = fn (string $b, string $dv, string $numero, string $sit, string $abertura, string $compl = ''): array => [
        'cnpj_basico' => $b, 'cnpj_ordem' => '0001', 'cnpj_dv' => $dv,
        'situacao_cadastral' => $sit, 'data_inicio_atividade' => $abertura,
        'numero' => $numero, 'complemento' => $compl, 'cep' => '49019900', 'municipio' => '4106902', 'uf' => 'SE',
    ];

    DB::connection('cnpj')->table('estabelecimentos')->insert([
        $est('11111111', '11', '320', '02', '2010-09-08', 'SALA 1'),   // ATIVA, nº 320, compl A
        $est('22222222', '22', 'Nº 320', '02', '2020-01-01', 'SALA 2'), // ATIVA, "Nº 320", compl B (mais nova)
        $est('33333333', '33', '500', '02', '2018-01-01'),             // nº 500 (não casa)
        $est('44444444', '44', '320', '08', '2015-01-01'),             // BAIXADA, nº 320
        // mesmo número, mas CEP diferente → não pode aparecer
        ['cnpj_basico' => '55555555', 'cnpj_ordem' => '0001', 'cnpj_dv' => '55', 'situacao_cadastral' => '02', 'data_inicio_atividade' => '2021-01-01', 'numero' => '320', 'complemento' => '', 'cep' => '80020310', 'municipio' => '4106902', 'uf' => 'PR'],
    ]);
}

/** Token OSINT (ability `vigilancia:osint`). */
function enderecoToken(): string
{
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();

    return $user->createToken('osint', ['vigilancia:osint'])->plainTextToken;
}

it('rejects an unauthenticated request', function () {
    $this->getJson('/api/v1/vigilancia/empresas/por-endereco?cep=49019900&numero=320')->assertUnauthorized();
});

it('rejects a token without the vigilancia:osint ability', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $token = $user->createToken('comum')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/empresas/por-endereco?cep=49019900&numero=320')
        ->assertForbidden()
        ->assertJsonPath('error.code', 'sem_permissao');
});

it('matches by CEP + normalized number, newest first, with socios', function () {
    $token = enderecoToken();
    seedEnderecoBase();

    $json = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/empresas/por-endereco?cep=49019900&numero=320')
        ->assertOk()
        ->assertJsonPath('meta.total', 3) // A (320), B ("Nº 320"), D (320) — exclui C (500) e o outro CEP
        ->json();

    $cnpjs = array_column($json['data'], 'cnpj');

    // Ordenado por data_abertura desc: B (2020) > D (2015) > A (2010).
    expect($cnpjs)->toBe(['22222222000122', '44444444000144', '11111111000111'])
        ->and($json['data'][0]['razao_social'])->toBe('EMPRESA B')
        ->and($json['data'][0]['socios'][0]['nome'])->toBe('MARIA');
});

it('filters by situacao when provided', function () {
    $token = enderecoToken();
    seedEnderecoBase();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/empresas/por-endereco?cep=49019900&numero=320&situacao=ATIVA')
        ->assertOk()
        ->assertJsonPath('meta.total', 2) // exclui a D (BAIXADA)
        ->assertJsonPath('data.0.cnpj', '22222222000122')
        ->assertJsonPath('data.1.cnpj', '11111111000111');
});

it('respects the limite', function () {
    $token = enderecoToken();
    seedEnderecoBase();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/empresas/por-endereco?cep=49019900&numero=320&limite=1')
        ->assertOk()
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.cnpj', '22222222000122'); // a mais nova
});

it('ignores complemento: same cep+numero with different complemento both return', function () {
    $token = enderecoToken();
    seedEnderecoBase();

    // A (SALA 1) e B (SALA 2): mesma CEP+número, só o complemento difere → os dois voltam.
    $json = $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/empresas/por-endereco?cep=49019900&numero=320&situacao=ATIVA')
        ->assertOk()
        ->json();

    $cnpjs = array_column($json['data'], 'cnpj');
    expect($cnpjs)->toContain('11111111000111')->toContain('22222222000122');
});

it('returns 503 when the base is unavailable', function () {
    $token = enderecoToken();

    // Conexão sqlite sem tabelas + CEP real (não zero, não curto-circuita) →
    // a consulta lança QueryException, mapeada para 503.
    config()->set('database.connections.cnpj', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
        'foreign_key_constraints' => false,
    ]);
    DB::purge('cnpj');

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/empresas/por-endereco?cep=49019900&numero=320')
        ->assertStatus(503)
        ->assertJsonPath('error.code', 'base_indisponivel');
});

it('returns an empty list for the zero CEP without touching the base', function () {
    $token = enderecoToken();
    // Sem seed: se tocasse na base, daria erro (tabela ausente) → prova o short-circuit.

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/empresas/por-endereco?cep=00000000&numero=100')
        ->assertOk()
        ->assertJsonPath('meta.total', 0)
        ->assertExactJson(['data' => [], 'meta' => ['total' => 0, 'base_referencia' => null]]);
});

it('validates the CEP', function () {
    $token = enderecoToken();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/empresas/por-endereco?cep=123&numero=320')
        ->assertStatus(422)
        ->assertJsonPath('error.code', 'cep_invalido');
});

it('requires the numero', function () {
    $token = enderecoToken();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/empresas/por-endereco?cep=49019900')
        ->assertStatus(422)
        ->assertJsonPath('error.code', 'numero_obrigatorio');
});

it('rejects an invalid situacao', function () {
    $token = enderecoToken();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/empresas/por-endereco?cep=49019900&numero=320&situacao=FOO')
        ->assertStatus(422)
        ->assertJsonPath('error.code', 'situacao_invalida');
});
