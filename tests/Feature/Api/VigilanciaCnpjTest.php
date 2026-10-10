<?php

declare(strict_types=1);

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Base CNPJ local (sqlite em memória) com o schema que a API OSINT consulta.
 */
function seedVigilanciaBase(): void
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
}

/** Insere a NOVA LINGUA CURSOS (CNPJ 44555666000107) com endereço, CNAE e um sócio. */
function seedNovaLingua(): void
{
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        'cnpj_basico' => '44555666', 'cnpj_ordem' => '0001', 'cnpj_dv' => '07',
        'situacao_cadastral' => '02', 'data_situacao_cadastral' => '2026-06-01',
        'data_inicio_atividade' => '2026-06-01', 'nome_fantasia' => 'NOVA LINGUA IDIOMAS',
        'cnae_fiscal_principal' => '8593700',
        'tipo_logradouro' => 'RUA', 'logradouro' => 'XV DE NOVEMBRO', 'numero' => '100',
        'complemento' => '', 'bairro' => 'CENTRO', 'cep' => '80020310',
        'municipio' => '4106902', 'uf' => 'PR',
    ]);
    DB::connection('cnpj')->table('empresas')->insert(['cnpj_basico' => '44555666', 'razao_social' => 'NOVA LINGUA CURSOS LTDA']);
    DB::connection('cnpj')->table('cnaes')->insert(['codigo' => '8593700', 'descricao' => 'Ensino de idiomas']);
    DB::connection('cnpj')->table('municipios')->insert(['codigo' => '4106902', 'descricao' => 'CURITIBA']);
    DB::connection('cnpj')->table('qualificacoes_socios')->insert(['codigo' => '49', 'descricao' => 'Sócio-Administrador']);
    DB::connection('cnpj')->table('socios')->insert([
        'cnpj_basico' => '44555666', 'nome_socio' => 'MARIANA BRANDT SOUZA', 'cnpj_cpf_do_socio' => '***123456**',
        'identificador_de_socio' => '2', 'qualificacao_do_socio' => '49', 'data_entrada_sociedade' => '2026-06-01',
    ]);
}

/** Token OSINT (ability `vigilancia:osint`). */
function osintToken(): string
{
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();

    return $user->createToken('osint', ['vigilancia:osint'])->plainTextToken;
}

it('rejects an unauthenticated request', function () {
    $this->getJson('/api/v1/vigilancia/cnpjs/44555666000107')->assertUnauthorized();

    // 401 é barrado pelo auth (antes da auditoria) → não gera trilha.
    $this->assertDatabaseCount('api_access_logs', 0);
});

it('rejects a token without the vigilancia:osint ability (wildcard does not count)', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $token = $user->createToken('comum')->plainTextToken; // abilities = ['*']

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/cnpjs/44555666000107')
        ->assertForbidden()
        ->assertJsonPath('error.code', 'sem_permissao');

    // 403 por falta de permissão também é auditado.
    $this->assertDatabaseHas('api_access_logs', [
        'path' => 'api/v1/vigilancia/cnpjs/44555666000107',
        'status' => 403,
    ]);
});

it('returns the company in the OSINT contract shape', function () {
    config()->set('vigilancia.base_referencia', '2026-09');
    $token = osintToken();
    seedVigilanciaBase();
    seedNovaLingua();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/cnpjs/44555666000107')
        ->assertOk()
        ->assertJsonPath('data.cnpj', '44555666000107')
        ->assertJsonPath('data.razao_social', 'NOVA LINGUA CURSOS LTDA')
        ->assertJsonPath('data.nome_fantasia', 'NOVA LINGUA IDIOMAS')
        ->assertJsonPath('data.situacao', 'ATIVA')
        ->assertJsonPath('data.data_situacao', '2026-06-01')
        ->assertJsonPath('data.data_abertura', '2026-06-01')
        ->assertJsonPath('data.cnae_principal.codigo', '8593700')
        ->assertJsonPath('data.cnae_principal.descricao', 'Ensino de idiomas')
        ->assertJsonPath('data.endereco.tipo_logradouro', 'RUA')
        ->assertJsonPath('data.endereco.numero', '100')
        ->assertJsonPath('data.endereco.municipio', 'CURITIBA')
        ->assertJsonPath('data.endereco.uf', 'PR')
        ->assertJsonPath('data.endereco.cep', '80020310')
        ->assertJsonPath('data.socios.0.nome', 'MARIANA BRANDT SOUZA')
        ->assertJsonPath('data.socios.0.qualificacao', 'Sócio-Administrador')
        ->assertJsonPath('data.socios.0.documento_mascarado', '***123456**')
        ->assertJsonPath('meta.base_referencia', '2026-09');

    $this->assertDatabaseHas('api_access_logs', [
        'path' => 'api/v1/vigilancia/cnpjs/44555666000107',
        'method' => 'GET',
        'status' => 200,
    ]);
});

it('returns 404 for a CNPJ outside the base', function () {
    $token = osintToken();
    seedVigilanciaBase(); // base no ar, mas o CNPJ não existe

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/cnpjs/44555666000107')
        ->assertNotFound()
        ->assertJsonPath('error.code', 'nao_encontrado');
});

it('returns 503 when the base is unavailable', function () {
    $token = osintToken();

    // Conexão sqlite em memória, porém SEM as tabelas → a query lança
    // QueryException, que o controller mapeia para 503 (base indisponível).
    config()->set('database.connections.cnpj', [
        'driver' => 'sqlite',
        'database' => ':memory:',
        'prefix' => '',
        'foreign_key_constraints' => false,
    ]);
    DB::purge('cnpj');

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/cnpjs/44555666000107')
        ->assertStatus(503)
        ->assertJsonPath('error.code', 'base_indisponivel');
});

it('validates the CNPJ length', function () {
    $token = osintToken();

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->getJson('/api/v1/vigilancia/cnpjs/123')
        ->assertStatus(422)
        ->assertJsonPath('error.code', 'cnpj_invalido');
});
