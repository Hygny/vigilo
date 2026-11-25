<?php

declare(strict_types=1);

use App\Livewire\Pessoas\Busca;
use App\Models\Organization;
use App\Models\PessoaConsulta;
use App\Models\User;
use App\Services\Vigilancia\BuscaPessoas;
use App\Support\BuscaNome;
use App\Support\Cnpj;
use App\Support\NomeSocio;
use App\Support\PessoaChave;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

/**
 * Base CNPJ (sqlite) da busca de pessoas: MARIA APARECIDA SOUZA (***111222**) em
 * 2 empresas; um homônimo dela com outro CPF; uma xará com o MESMO CPF mascarado
 * mas outro nome (não entra nas empresas dela); MARIA SOUZA (outra pessoa) e um
 * estrangeiro sem documento.
 */
function seedPessoas(): void
{
    useCnpjSqlite();
    $schema = Schema::connection('cnpj');
    $schema->create('busca_pessoa', function (Blueprint $t): void {
        $t->string('chave')->primary();
        $t->string('nome');
        $t->string('nome_exibicao');
        $t->string('documento');
        $t->string('tipo')->nullable();
        $t->integer('empresas');
    });
    $schema->create('socios', function (Blueprint $t): void {
        foreach (['cnpj_basico', 'nome_socio', 'nome_norm', 'cnpj_cpf_do_socio', 'identificador_de_socio', 'qualificacao_do_socio', 'data_entrada_sociedade'] as $c) {
            $t->string($c)->nullable();
        }
    });
    $schema->create('empresas', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('razao_social')->nullable();
    });
    $schema->create('estabelecimentos', function (Blueprint $t): void {
        foreach (['cnpj_basico', 'cnpj_ordem', 'cnpj_dv', 'situacao_cadastral', 'uf'] as $c) {
            $t->string($c)->nullable();
        }
    });
    $schema->create('qualificacoes_socios', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });

    $db = DB::connection('cnpj');
    $socio = function (string $basico, string $nome, ?string $doc, string $tipo = '2') use ($db): void {
        $db->table('socios')->insert([
            'cnpj_basico' => $basico, 'nome_socio' => $nome, 'nome_norm' => NomeSocio::norm($nome), 'cnpj_cpf_do_socio' => $doc,
            'identificador_de_socio' => $tipo, 'qualificacao_do_socio' => '49', 'data_entrada_sociedade' => '2021-04-01',
        ]);
    };
    $socio('11111111', 'MARIA APARECIDA SOUZA', '***111222**');
    $socio('22222222', 'MARIA APARECIDA SOUZA', '***111222**');
    $socio('33333333', 'JOANA PEREIRA', '***111222**');          // xará de CPF, outro nome
    $socio('44444444', 'MARIA APARECIDA SOUZA', '***999888**');  // homônimo
    $socio('55555555', 'MARIA SOUZA', '***555666**');
    $socio('66666666', 'JOHN SMITH', null, '3');                 // estrangeiro sem documento

    foreach (['11111111' => ['PADARIA AURORA LTDA', '02'], '22222222' => ['MERCADO CENTRAL LTDA', '08'], '33333333' => ['OUTRA LTDA', '02'],
        '44444444' => ['LOJA DO HOMONIMO', '02'], '55555555' => ['MARIA SOUZA 12345678901', '02'], '66666666' => ['SMITH IMPORTS', '02']] as $b => [$razao, $sit]) {
        $db->table('empresas')->insert(['cnpj_basico' => $b, 'razao_social' => $razao]);
        $db->table('estabelecimentos')->insert(['cnpj_basico' => $b, 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => $sit, 'uf' => 'SP']);
    }
    $db->table('qualificacoes_socios')->insert(['codigo' => '49', 'descricao' => 'Sócio-Administrador']);

    // busca_pessoa como o pessoas-popular.sh monta (uma linha por nome+documento).
    foreach ([['MARIA APARECIDA SOUZA', '***111222**', '2', 2], ['JOANA PEREIRA', '***111222**', '2', 1], ['MARIA APARECIDA SOUZA', '***999888**', '2', 1],
        ['MARIA SOUZA', '***555666**', '2', 1], ['JOHN SMITH', '', '3', 1]] as [$nome, $doc, $tipo, $n]) {
        $db->table('busca_pessoa')->insert([
            'chave' => PessoaChave::para($doc, $nome), 'nome' => BuscaNome::chave($nome), 'nome_exibicao' => $nome,
            'documento' => $doc, 'tipo' => $tipo, 'empresas' => $n,
        ]);
    }
}

function pessoaUser(): User
{
    return User::factory()->for(Organization::factory())->create();
}

it('searches people by name and surname, the exact name first', function () {
    seedPessoas();
    $r = app(BuscaPessoas::class)->buscar('maria souza');

    expect(array_map(fn ($p) => [$p->nome, $p->documento, $p->empresas], $r->itens))->toBe([
        ['MARIA SOUZA', '***555666**', 1],              // nome idêntico ao digitado
        ['MARIA APARECIDA SOUZA', '***111222**', 2],    // depois, mais empresas
        ['MARIA APARECIDA SOUZA', '***999888**', 1],
    ])->and($r->truncado)->toBeFalse();

    expect(app(BuscaPessoas::class)->buscar('maria')->itens)->toBe([]); // uma palavra só: não busca
});

it('marks the result as truncated when the candidates hit the ceiling', function () {
    seedPessoas();
    $busca = new BuscaPessoas('cnpj', candidatos: 1);

    expect($busca->buscar('maria souza')->truncado)->toBeTrue();
});

it('lists the companies of the probable person (same masked CPF AND same name) and the homonyms', function () {
    seedPessoas();
    $p = app(BuscaPessoas::class)->pessoa(PessoaChave::para('***111222**', 'MARIA APARECIDA SOUZA'));

    expect($p)->not->toBeNull()
        ->and($p->nome)->toBe('MARIA APARECIDA SOUZA')
        ->and($p->totalEmpresas)->toBe(2)
        ->and(array_map(fn ($e) => $e->nome, $p->empresas))->toBe(['PADARIA AURORA LTDA', 'MERCADO CENTRAL LTDA']) // ativa primeiro; sem a JOANA (xará de CPF)
        ->and($p->empresas[1]->situacao)->toBe('BAIXADA')
        ->and($p->empresas[0]->cnpj)->toBe(Cnpj::matrizFromBasico('11111111'))
        ->and($p->empresas[0]->qualificacao)->toBe('Sócio-Administrador')
        ->and(array_map(fn ($h) => $h->documento, $p->homonimos))->toBe(['***999888**']);
});

it('finds the companies of a foreigner without document by the normalized name', function () {
    seedPessoas();
    $p = app(BuscaPessoas::class)->pessoa(PessoaChave::para('', 'JOHN SMITH'));

    expect($p?->estrangeiro)->toBeTrue()
        ->and($p?->documento)->toBeNull()
        ->and(array_map(fn ($e) => $e->nome, $p->empresas))->toBe(['SMITH IMPORTS']);
});

it('returns null for an invalid or unknown key', function () {
    seedPessoas();

    expect(app(BuscaPessoas::class)->pessoa('nao-e-uuid'))->toBeNull()
        ->and(app(BuscaPessoas::class)->pessoa('00000000-0000-0000-0000-000000000000'))->toBeNull();
});

it('searches from the screen, keeps the name out of the URL and records every search', function () {
    seedPessoas();
    $user = pessoaUser();

    Livewire::actingAs($user)->test(Busca::class)
        ->set('termo', 'maria aparecida souza')
        ->call('buscar')
        ->assertSee('MARIA APARECIDA SOUZA')
        ->assertSee('***111222**')
        ->assertSee(route('pessoas.perfil', PessoaChave::para('***111222**', 'MARIA APARECIDA SOUZA')), false)
        ->assertSee('pessoa provável');

    $log = PessoaConsulta::query()->sole();
    expect($log->user_id)->toBe($user->id)
        ->and($log->organization_id)->toBe($user->organization_id)
        ->and($log->tipo)->toBe('busca')
        ->and($log->termo)->toBe('maria aparecida souza');

    $this->actingAs($user)->get(route('pessoas.busca'))->assertOk()->assertSee('Busca de pessoas');
    expect((new ReflectionProperty(Busca::class, 'termo'))->getAttributes(Livewire\Attributes\Url::class))->toBe([]);
});

it('refuses CPF and single-word searches without querying or logging', function () {
    seedPessoas();

    Livewire::actingAs(pessoaUser())->test(Busca::class)
        ->set('termo', '123.456.789-01')->call('buscar')->assertSee('Busca por CPF não é permitida')
        ->set('termo', 'maria')->call('buscar')->assertSee('Digite nome e sobrenome');

    expect(PessoaConsulta::query()->count())->toBe(0);
});

it('rate-limits the person search per user', function () {
    seedPessoas();
    config()->set('cnpj.pessoas.per_minute', 1);
    $user = pessoaUser();
    RateLimiter::clear('busca-pessoas:'.$user->getKey());

    Livewire::actingAs($user)->test(Busca::class)
        ->set('termo', 'maria souza')->call('buscar')->assertSee('MARIA SOUZA')
        ->set('termo', 'joana pereira')->call('buscar')->assertSee('Muitas buscas em pouco tempo')->assertDontSee('JOANA PEREIRA');
});

it('says the base is down instead of "no one found"', function () {
    useCnpjSqlite(); // sem tabelas

    Livewire::actingAs(pessoaUser())->test(Busca::class)
        ->set('termo', 'maria souza')->call('buscar')
        ->assertSee('Base CNPJ indisponível')
        ->assertDontSee('Nenhum sócio encontrado');
});

it('opens the person page by the opaque key, warns about the probable link and records it', function () {
    seedPessoas();
    $user = pessoaUser();
    $chave = PessoaChave::para('***111222**', 'MARIA APARECIDA SOUZA');

    $this->actingAs($user)->get(route('pessoas.perfil', $chave))
        ->assertOk()
        ->assertSee('MARIA APARECIDA SOUZA')
        ->assertSee('Vínculo provável')
        ->assertSee('PADARIA AURORA LTDA')
        ->assertSee(route('empresas.perfil', Cnpj::matrizFromBasico('11111111')), false)
        ->assertSee('Homônimos')
        ->assertSee(route('pessoas.perfil', PessoaChave::para('***999888**', 'MARIA APARECIDA SOUZA')), false)
        ->assertDontSee('JOANA PEREIRA');

    expect(PessoaConsulta::query()->where('tipo', 'perfil')->value('termo'))->toBe($chave);

    $this->actingAs($user)->get('/pessoas/isto-nao-e-uma-chave')->assertNotFound();
    $this->actingAs($user)->get(route('pessoas.perfil', '00000000-0000-0000-0000-000000000000'))->assertOk()->assertSee('Pessoa não encontrada');
    $this->actingAs(User::factory()->create(['organization_id' => null]))->get(route('pessoas.perfil', $chave))->assertForbidden();
});

it('counts the person page in the profile ceiling (and does not record a blocked view)', function () {
    seedPessoas();
    config()->set('cnpj.perfil.per_minute', 1);
    $user = pessoaUser();
    RateLimiter::clear('perfil-empresa:'.$user->getKey());
    $chave = PessoaChave::para('***111222**', 'MARIA APARECIDA SOUZA');

    $this->actingAs($user)->get(route('pessoas.perfil', $chave))->assertSee('PADARIA AURORA LTDA');
    $this->actingAs($user)->get(route('pessoas.perfil', $chave))->assertSee('Muitos perfis abertos')->assertDontSee('PADARIA AURORA LTDA');

    expect(PessoaConsulta::query()->count())->toBe(1);
});

it('prunes the audit trail past the retention', function () {
    config()->set('cnpj.pessoas.retencao_dias', 30);
    $user = pessoaUser();
    PessoaConsulta::registrar($user, PessoaConsulta::TIPO_BUSCA, 'antiga');
    PessoaConsulta::query()->update(['created_at' => now()->subDays(31)]);
    PessoaConsulta::registrar($user, PessoaConsulta::TIPO_BUSCA, 'recente');

    $this->artisan('model:prune', ['--model' => [PessoaConsulta::class]])->assertSuccessful();

    expect(PessoaConsulta::query()->pluck('termo')->all())->toBe(['recente']);
});

it('never loses the exact name behind the candidates ceiling (common name)', function () {
    seedPessoas();
    $db = DB::connection('cnpj');
    // 60 "ANA MARIA SILVA N" (casam os termos e vêm antes na tabela) + o nome exato e um "começa com".
    for ($i = 0; $i < 60; $i++) {
        $nome = "ANA MARIA SILVA {$i}X";
        $db->table('busca_pessoa')->insert(['chave' => PessoaChave::para('***000'.str_pad((string) $i, 3, '0', STR_PAD_LEFT).'**', $nome), 'nome' => BuscaNome::chave($nome), 'nome_exibicao' => $nome, 'documento' => '***000'.str_pad((string) $i, 3, '0', STR_PAD_LEFT).'**', 'tipo' => '2', 'empresas' => 9]);
    }
    foreach ([['MARIA SILVA', '***700800**', 1], ['MARIA SILVA SANTOS', '***700900**', 1]] as [$nome, $doc, $n]) {
        $db->table('busca_pessoa')->insert(['chave' => PessoaChave::para($doc, $nome), 'nome' => BuscaNome::chave($nome), 'nome_exibicao' => $nome, 'documento' => $doc, 'tipo' => '2', 'empresas' => $n]);
    }

    $r = (new BuscaPessoas('cnpj', candidatos: 50))->buscar('maria silva', 10);

    expect($r->itens[0]->nome)->toBe('MARIA SILVA')          // idêntico
        ->and($r->itens[1]->nome)->toBe('MARIA SILVA SANTOS') // começa com o digitado
        ->and($r->truncado)->toBeTrue();
});

it('does not list as homonym the same masked CPF with a variant spelling', function () {
    seedPessoas();
    DB::connection('cnpj')->table('busca_pessoa')->insert([
        'chave' => PessoaChave::para('***111222**', 'MARIA APARECIDA SOUZA.'), 'nome' => BuscaNome::chave('MARIA APARECIDA SOUZA.'),
        'nome_exibicao' => 'MARIA APARECIDA SOUZA.', 'documento' => '***111222**', 'tipo' => '2', 'empresas' => 1,
    ]);

    $p = app(BuscaPessoas::class)->pessoa(PessoaChave::para('***111222**', 'MARIA APARECIDA SOUZA'));

    expect(array_map(fn ($h) => $h->documento, $p->homonimos))->toBe(['***999888**']);
});

it('shows the all-zeros document of foreigners as "sem documento" and finds their companies by name', function () {
    seedPessoas();
    $db = DB::connection('cnpj');
    $db->table('socios')->insert(['cnpj_basico' => '77777777', 'nome_socio' => 'PIERRE DUBOIS', 'nome_norm' => NomeSocio::norm('PIERRE DUBOIS'), 'cnpj_cpf_do_socio' => '00000000000000', 'identificador_de_socio' => '3']);
    $db->table('empresas')->insert(['cnpj_basico' => '77777777', 'razao_social' => 'DUBOIS PARTICIPACOES']);
    $db->table('busca_pessoa')->insert(['chave' => PessoaChave::para('00000000000000', 'PIERRE DUBOIS'), 'nome' => BuscaNome::chave('PIERRE DUBOIS'), 'nome_exibicao' => 'PIERRE DUBOIS', 'documento' => '00000000000000', 'tipo' => '3', 'empresas' => 1]);

    $p = app(BuscaPessoas::class)->pessoa(PessoaChave::para('00000000000000', 'PIERRE DUBOIS'));
    expect($p?->documento)->toBeNull()
        ->and(array_map(fn ($e) => $e->nome, $p->empresas))->toBe(['DUBOIS PARTICIPACOES']);

    Livewire::actingAs(pessoaUser())->test(Busca::class)->set('termo', 'pierre dubois')->call('buscar')
        ->assertSee('sem documento')->assertDontSee('00000000000000');
});

it('says the query took too long (timeout) instead of failing, on the search and on the person page', function () {
    seedPessoas();
    $estourar = function (): void {
        $pdo = new class('canceling statement due to statement timeout') extends PDOException
        {
            protected $code = '57014';
        };
        throw new QueryException('cnpj', 'select', [], $pdo);
    };
    DB::connection('cnpj')->beforeExecuting($estourar);
    $user = pessoaUser();

    Livewire::actingAs($user)->test(Busca::class)->set('termo', 'maria souza')->call('buscar')
        ->assertSee('Nome comum demais para responder a tempo');

    $this->actingAs($user)->get(route('pessoas.perfil', PessoaChave::para('***111222**', 'MARIA APARECIDA SOUZA')))
        ->assertOk()->assertSee('A consulta demorou demais');
});

it('shows "base unavailable" (not a 500) when the Postgres is unreachable on the timeout transaction path', function () {
    config()->set('database.connections.cnpj', [
        'driver' => 'pgsql', 'host' => '127.0.0.1', 'port' => 1, 'database' => 'cnpj', 'username' => 'x', 'password' => 'x',
        'options' => [PDO::ATTR_TIMEOUT => 1],
    ]);
    DB::purge('cnpj');
    $user = pessoaUser();

    Livewire::actingAs($user)->test(Busca::class)->set('termo', 'maria souza')->call('buscar')
        ->assertOk()->assertSee('Base CNPJ indisponível');

    $this->actingAs($user)->get(route('pessoas.perfil', PessoaChave::para('***111222**', 'MARIA APARECIDA SOUZA')))
        ->assertOk()->assertSee('Base CNPJ indisponível');

    Livewire::actingAs($user)->test(App\Livewire\Empresas\Busca::class)->set('termo', 'padaria aurora')->call('buscar')
        ->assertOk()->assertSee('Base CNPJ indisponível');
});

it('shows the Empresas × Pessoas tabs and keeps "Busca" active in the menu', function () {
    $html = $this->actingAs(pessoaUser())->get(route('pessoas.busca'))->assertOk()->getContent();

    // A aba ativa é a de Pessoas (aria-current na âncora dela), não a de Empresas.
    expect($html)->toMatch('#href="'.preg_quote(route('pessoas.busca'), '#').'"\s+wire:navigate\s+aria-current="page"#')
        ->not->toMatch('#href="'.preg_quote(route('empresas.busca'), '#').'"\s+wire:navigate\s+aria-current="page"#')
        ->toContain('bg-primary-soft text-primary'); // item "Busca" do menu ativo em pessoas.*
});

it('computes the same opaque key as the Postgres build (pinned in tests/deploy/cnpj-db-sql.test.sh)', function () {
    expect(PessoaChave::para('***924790**', 'JORGE GERDAU JOHANNPETER'))->toBe('b0c6bc3c-f28c-8354-a6e6-060ee66ecf93')
        ->and(PessoaChave::valida('B0C6BC3C-F28C-8354-A6E6-060EE66ECF93'))->toBeTrue()
        ->and(PessoaChave::valida('b0c6bc3c'))->toBeFalse();
});

it('keeps the identical names first even with more "starts with" names than the ceiling', function () {
    seedPessoas();
    $db = DB::connection('cnpj');
    // 30 "MARIA SILVA <sobrenome>" gravados ANTES dos idênticos.
    for ($i = 0; $i < 30; $i++) {
        $nome = 'MARIA SILVA '.chr(65 + ($i % 26)).$i.'X';
        $db->table('busca_pessoa')->insert(['chave' => PessoaChave::para('***1'.str_pad((string) $i, 5, '0', STR_PAD_LEFT).'**', $nome), 'nome' => BuscaNome::chave($nome), 'nome_exibicao' => $nome, 'documento' => '***1'.str_pad((string) $i, 5, '0', STR_PAD_LEFT).'**', 'tipo' => '2', 'empresas' => 5]);
    }
    foreach (['***700800**', '***700801**'] as $doc) {
        $db->table('busca_pessoa')->insert(['chave' => PessoaChave::para($doc, 'MARIA SILVA'), 'nome' => BuscaNome::chave('MARIA SILVA'), 'nome_exibicao' => 'MARIA SILVA', 'documento' => $doc, 'tipo' => '2', 'empresas' => 1]);
    }

    $r = (new BuscaPessoas('cnpj', candidatos: 10))->buscar('maria silva', 5);

    expect(array_map(fn ($p) => $p->nome, array_slice($r->itens, 0, 2)))->toBe(['MARIA SILVA', 'MARIA SILVA'])
        ->and($r->truncado)->toBeTrue();
});

it('still returns the identical names when only the loose-terms read times out', function () {
    seedPessoas();
    DB::connection('cnpj')->beforeExecuting(function (string $sql, array $bindings): void {
        if (in_array('% MARIA%', $bindings, true)) { // só a leitura por termos (GIN)
            $pdo = new class('canceling statement due to statement timeout') extends PDOException
            {
                protected $code = '57014';
            };
            throw new QueryException('cnpj', $sql, $bindings, $pdo);
        }
    });

    $r = app(BuscaPessoas::class)->buscar('maria souza');

    expect(array_map(fn ($p) => $p->nome, $r->itens))->toBe(['MARIA SOUZA'])
        ->and($r->truncado)->toBeTrue();
});

it('never writes the searched name to the error log', function () {
    seedPessoas();
    Exceptions::fake();
    DB::connection('cnpj')->beforeExecuting(function (string $sql, array $bindings): void {
        throw new QueryException('cnpj', $sql, $bindings, new PDOException('permission denied for table busca_pessoa'));
    });

    Livewire::actingAs(pessoaUser())->test(Busca::class)->set('termo', 'maria souza')->call('buscar')
        ->assertSee('Base CNPJ indisponível');

    Exceptions::assertReported(fn (RuntimeException $e): bool => str_contains($e->getMessage(), 'SQL omitido')
        && ! str_contains($e->getMessage(), 'MARIA'));
    Exceptions::assertNotReported(QueryException::class);
});
