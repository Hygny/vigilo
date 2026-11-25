<?php

declare(strict_types=1);

use App\DTO\Bens\Marca;
use App\Livewire\Companies\Show;
use App\Livewire\Empresas\BensRecebiveis;
use App\Models\MaintenanceRun;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Services\Admin\StatusRotinas;
use App\Services\Bens\AeronavesLookup;
use App\Services\Contratos\ContratosPublicos;
use App\Support\Cnpj;
use App\Support\PessoaChave;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

/**
 * CSV do RAB como a ANAC publica: 1ª linha "Atualizado em", cabeçalho, `;`,
 * proprietários/operadores em JSON.
 *
 * @param  list<array<string, mixed>>  $aeronaves  [marca, modelo, gravame, canc, props, ops]
 */
function rabCsv(array $aeronaves, string $data = '2026-11-25'): string
{
    $q = fn (string $v): string => '"'.str_replace('"', '""', $v).'"';
    $linhas = ["Atualizado em: {$data}", implode(';', array_map($q, ['MARCAS', 'PROPRIETARIOS', 'OPERADORES', 'DS_MODELO', 'NM_FABRICANTE', 'NR_ANO_FABRICACAO', 'DS_GRAVAME', 'DT_MATRICULA', 'DT_CANC', 'DS_MOTIVO_CANC']))];

    foreach ($aeronaves as $a) {
        $linhas[] = implode(';', array_map($q, [
            $a['marca'], $a['props_raw'] ?? json_encode($a['props'] ?? [], JSON_UNESCAPED_UNICODE), $a['ops_raw'] ?? json_encode($a['ops'] ?? [], JSON_UNESCAPED_UNICODE),
            $a['modelo'] ?? 'EMB-505', 'EMBRAER', (string) ($a['ano'] ?? 2015), $a['gravame'] ?? 'NENHUM GRAVAME REGISTRADO',
            '2015-03-01 00:00:00', $a['canc'] ?? '', ($a['canc'] ?? '') !== '' ? 'PERECIMENTO' : '',
        ]));
    }

    return "\u{FEFF}".implode("\n", $linhas)."\n";
}

function rabAeronaves(): array
{
    return [
        ['marca' => 'PRAAA', 'props' => [['NOME' => 'PADARIA AURORA LTDA', 'DOCUMENTO' => '11222333000181', 'PERCENTUAL' => '100.00', 'UF' => 'SP']], 'ops' => [['NOME' => 'PADARIA AURORA LTDA', 'DOCUMENTO' => '11222333000181', 'UF' => 'SP']]],
        ['marca' => 'PRBBB', 'gravame' => 'ALIENAÇÃO FIDUCIÁRIA', 'props' => [['NOME' => 'PADARIA AURORA FILIAL', 'DOCUMENTO' => '11222333000262', 'PERCENTUAL' => '50.00', 'UF' => 'SP']]],
        ['marca' => 'PRDDD', 'props' => [['NOME' => 'PADARIA AURORA LTDA', 'DOCUMENTO' => '11222333000181', 'PERCENTUAL' => '50.00', 'UF' => 'SP']], 'ops' => [['NOME' => 'PADARIA AURORA FILIAL', 'DOCUMENTO' => '11222333000262', 'UF' => 'RJ']]],
        ['marca' => 'PRCCC', 'canc' => '01/02/2010', 'props' => [['NOME' => 'PADARIA AURORA LTDA', 'DOCUMENTO' => '11222333000181', 'PERCENTUAL' => '100.00', 'UF' => 'SP']]],
        ['marca' => 'PSPFA', 'modelo' => 'P2010', 'props' => [['NOME' => 'MARIA SOUZA', 'DOCUMENTO' => '148XXXXXX43', 'PERCENTUAL' => '100.00', 'UF' => 'PR']]],
        ['marca' => 'PSIND', 'props' => [['NOME' => 'Indisponível', 'DOCUMENTO' => 'Indisponível', 'PERCENTUAL' => 'Indisponível', 'UF' => 'Indisponível']]],
    ];
}

/** CSV do RAB num arquivo local (--arquivo), para cargas sucessivas sem acumular Http::fake. */
function rabArquivo(array $aeronaves, string $data): string
{
    $caminho = (string) tempnam(sys_get_temp_dir(), 'rabt-');
    file_put_contents($caminho, rabCsv($aeronaves, $data));

    return $caminho;
}

function carregarRab(?array $aeronaves = null, string $data = '2026-11-25'): void
{
    config()->set('bens.aeronaves.min_aeronaves', 1);
    // Resposta nova a cada pedido: o download vai por sink, que consome o corpo.
    Http::fake(['*anac*' => fn () => Http::response(rabCsv($aeronaves ?? rabAeronaves(), $data))]);
}

/** Pessoa provável MARIA SOUZA na busca de pessoas (base CNPJ em SQLite); devolve a chave da página. */
function pessoaMariaSouza(): string
{
    useCnpjSqlite();
    $schema = Schema::connection('cnpj');
    $schema->create('busca_pessoa', function (Blueprint $t): void {
        foreach (['chave', 'nome', 'nome_exibicao', 'documento', 'tipo'] as $c) {
            $t->string($c)->nullable();
        }
        $t->integer('empresas')->default(0);
    });
    $schema->create('socios', function (Blueprint $t): void {
        foreach (['cnpj_basico', 'nome_socio', 'nome_norm', 'cnpj_cpf_do_socio', 'identificador_de_socio', 'qualificacao_do_socio', 'data_entrada_sociedade'] as $c) {
            $t->string($c)->nullable();
        }
    });
    foreach (['empresas', 'estabelecimentos', 'qualificacoes_socios'] as $tabela) {
        $schema->create($tabela, function (Blueprint $t): void {
            foreach (['cnpj_basico', 'razao_social', 'cnpj_ordem', 'situacao_cadastral', 'uf', 'codigo', 'descricao'] as $c) {
                $t->string($c)->nullable();
            }
        });
    }
    $chave = PessoaChave::para('***456789**', 'MARIA SOUZA');
    DB::connection('cnpj')->table('busca_pessoa')->insert(['chave' => $chave, 'nome' => ' MARIA SOUZA ', 'nome_exibicao' => 'MARIA SOUZA', 'documento' => '***456789**', 'tipo' => '2', 'empresas' => 0]);

    return $chave;
}

it('loads the ANAC registry: companies by CNPJ, people WITHOUT their masked document', function () {
    carregarRab();

    test()->artisan('vigilo:aeronaves-anac')->expectsOutputToContain('6 aeronaves, 7 vínculos (6 de empresas)')->assertSuccessful();

    expect(DB::table('aeronaves')->count())->toBe(6)
        ->and(DB::table('aeronave_vinculos')->where('tipo', 'PJ')->where('cnpj_basico', '11222333')->count())->toBe(6)
        ->and(DB::table('aeronave_vinculos')->where('tipo', 'PF')->value('cnpj'))->toBeNull()
        // LGPD/minimização: de PF só o nome normalizado (busca), nem o nome cru.
        ->and(DB::table('aeronave_vinculos')->where('tipo', 'PF')->value('nome'))->toBeNull()
        ->and(DB::table('aeronave_vinculos')->where('tipo', 'PF')->value('nome_norm'))->not->toBeEmpty()
        ->and(DB::table('aeronave_vinculos')->where('nome', 'like', 'Indispon%')->exists())->toBeFalse()
        ->and(DB::table('aeronaves')->where('marca', 'PRCCC')->value('data_cancelamento'))->toStartWith('2010-02-01')
        ->and(MaintenanceRun::ultima('aeronaves')?->isSuccess())->toBeTrue();

    // Nada de documento mascarado da ANAC em lugar nenhum.
    expect(DB::table('aeronave_vinculos')->where('cnpj', 'like', '%X%')->exists())->toBeFalse();
});

it('skips a file already loaded and never replaces a good copy with a tiny/broken file', function () {
    carregarRab();
    test()->artisan('vigilo:aeronaves-anac')->assertSuccessful();

    test()->artisan('vigilo:aeronaves-anac')->expectsOutputToContain('já carregado')->assertSuccessful();

    config()->set('bens.aeronaves.min_aeronaves', 100);
    test()->artisan('vigilo:aeronaves-anac', ['--forcar' => true])->expectsOutputToContain('cópia anterior mantida')->assertFailed();

    expect(DB::table('aeronaves')->count())->toBe(6)
        ->and(MaintenanceRun::ultima('aeronaves')?->isSuccess())->toBeFalse();
});

it('finds the aircraft of a company by its CNPJ root and of a person by name only', function () {
    carregarRab();
    test()->artisan('vigilo:aeronaves-anac')->assertSuccessful();
    $lookup = app(AeronavesLookup::class);

    $daEmpresa = $lookup->daEmpresa('11222333000181');
    expect(array_map(fn ($a) => $a->marca, $daEmpresa))->toBe(['PRAAA', 'PRBBB', 'PRDDD', 'PRCCC']) // ativas primeiro
        ->and($daEmpresa[0]->papeis)->toBe(['proprietario', 'operador'])
        ->and($daEmpresa[0]->percentual)->toBe(100.0)
        ->and($daEmpresa[0]->livre())->toBeTrue()
        // O % é o do vínculo de proprietário: a linha de operador (sem %, outra UF) não sobrescreve.
        ->and($daEmpresa[2]->percentual)->toBe(50.0)
        ->and($daEmpresa[2]->uf)->toBe('SP')
        ->and($daEmpresa[2]->papeis)->toBe(['proprietario', 'operador'])
        ->and($daEmpresa[1]->onus())->toBe('ALIENAÇÃO FIDUCIÁRIA')
        ->and($daEmpresa[1]->livre())->toBeFalse()
        ->and($daEmpresa[3]->cancelada())->toBeTrue();

    expect(array_map(fn ($a) => $a->marca, $lookup->daPessoa('Maria Souza')))->toBe(['PSPFA'])
        ->and($lookup->daPessoa('MARIA SOUZA SILVA'))->toBe([]);
});

it('says "not checked" (never "none") before the registry copy is loaded', function () {
    expect(app(AeronavesLookup::class)->disponivel())->toBeFalse();

    Http::fake(); // contratos: sem chave configurada → não consultado
    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(BensRecebiveis::class, ['cnpj' => '11222333000181'])
        ->assertSee('Cópia do registro de aeronaves indisponível no momento')
        ->assertDontSee('Nenhuma aeronave registrada');
});

it('shows aircraft, liens and public contracts to receive on the company card', function () {
    carregarRab();
    test()->artisan('vigilo:aeronaves-anac')->assertSuccessful();
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(['*portaldatransparencia*' => Http::response([[
        'id' => 9, 'numero' => '1', 'objeto' => 'Pães', 'dataAssinatura' => '2025-01-01', 'dataInicioVigencia' => '2025-01-01',
        'dataFimVigencia' => '2099-01-01', 'valorInicialCompra' => 5000, 'valorFinalCompra' => 5000,
        'fornecedor' => ['cnpjFormatado' => '11.222.333/0001-81'],
    ]])]);

    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(BensRecebiveis::class, ['cnpj' => '11222333000181'])
        ->assertSee('Bens e recebíveis')
        ->assertSee('3 aeronave(s) · 2 sem ônus')
        ->assertSee('PRAAA')
        ->assertSeeInOrder(['PRAAA', 'proprietária e operadora', '(100%)', 'PRBBB', '(50%)', 'PRDDD', 'proprietária e operadora', '(50%)'])
        ->assertSee('ALIENAÇÃO FIDUCIÁRIA')
        ->assertSee('matrícula cancelada')
        ->assertSee('1 contrato(s) federal(is) vigente(s) · R$ 5.000,00')
        ->assertSee('Imóveis, veículos e contas bancárias não têm fonte aberta');
});

it('lists possible aircraft on the person page by name, never the ANAC document', function () {
    carregarRab();
    test()->artisan('vigilo:aeronaves-anac')->assertSuccessful();
    $chave = pessoaMariaSouza();

    test()->actingAs(User::factory()->for(Organization::factory())->create())->get(route('pessoas.perfil', $chave))
        ->assertOk()
        ->assertSee('Possíveis aeronaves')
        ->assertSee('PSPFA')
        ->assertSee('Vínculo')
        ->assertDontSee('148XXXXXX43');
});

it('is a routine on the admin card and runs daily at 07:15 Brasília', function () {
    MaintenanceRun::registrar('aeronaves', true, '35078 aeronaves');
    $linha = collect(app(StatusRotinas::class)->todas())->firstWhere('nome', 'Registro de aeronaves (ANAC)');
    expect($linha?->situacao)->toBe('ok');

    $evento = collect(app(Schedule::class)->events())
        ->first(fn ($e) => str_contains((string) $e->command, 'vigilo:aeronaves-anac'));
    expect($evento?->expression)->toBe('15 7 * * *')
        ->and($evento?->timezone)->toBe('America/Sao_Paulo');
});

it('mounts the card lazily on the portfolio company page and 403/404s outside an organization', function () {
    $org = Organization::factory()->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    Livewire::actingAs(User::factory()->for($org)->create())->test(Show::class, ['company' => $company])
        ->assertSeeLivewire(BensRecebiveis::class);

    Livewire::actingAs(User::factory()->create(['organization_id' => null]))->test(BensRecebiveis::class, ['cnpj' => '11222333000181'])->assertForbidden();
    Livewire::actingAs(User::factory()->for($org)->create())->test(BensRecebiveis::class, ['cnpj' => '11222333000182'])->assertNotFound();
});

it('keeps the previous copy when the owners JSON breaks or the links vanish (never a false "none")', function () {
    config()->set('bens.aeronaves.min_aeronaves', 1);
    test()->artisan('vigilo:aeronaves-anac', ['--arquivo' => rabArquivo(rabAeronaves(), '2026-11-25')])->assertSuccessful();

    // A ANAC muda o formato de PROPRIETARIOS/OPERADORES: JSON inválido em massa.
    $torto = array_map(fn (array $a): array => ['props_raw' => '{NOME: '.$a['marca'], 'ops_raw' => '[{'] + $a, rabAeronaves());
    test()->artisan('vigilo:aeronaves-anac', ['--arquivo' => rabArquivo($torto, '2026-11-26')])
        ->expectsOutputToContain('JSON inválido')->assertFailed();

    // JSON válido mas sem ninguém: vínculos abaixo do piso.
    $vazio = array_map(fn (array $a): array => ['props' => [], 'ops' => []] + $a, rabAeronaves());
    test()->artisan('vigilo:aeronaves-anac', ['--arquivo' => rabArquivo($vazio, '2026-11-27')])
        ->expectsOutputToContain('vínculos para 6 aeronaves')->assertFailed();

    // Rollback: a cópia boa segue inteira, e a referência continua a de antes.
    expect(DB::table('aeronaves')->count())->toBe(6)
        ->and(DB::table('aeronave_vinculos')->count())->toBe(7)
        ->and(app(AeronavesLookup::class)->referencia())->toBe('2026-11-25')
        ->and(MaintenanceRun::ultima('aeronaves')?->isSuccess())->toBeFalse();
});

it('caps the list per aircraft (not per link row) and says "mostrando N de M"', function () {
    $total = AeronavesLookup::LIMITE + 1;
    $aeronaves = [];
    $vinculos = [];
    for ($i = 1; $i <= $total; $i++) {
        $marca = sprintf('PT%03d', $i);
        $aeronaves[] = ['marca' => $marca, 'modelo' => 'A320', 'gravame' => null, 'data_cancelamento' => $i > $total - 3 ? '2010-01-01' : null];
        foreach (['proprietario', 'operador'] as $papel) {
            $vinculos[] = ['marca' => $marca, 'papel' => $papel, 'tipo' => 'PJ', 'cnpj' => '11222333000181', 'cnpj_basico' => '11222333', 'nome' => 'AURORA LINHAS AEREAS', 'nome_norm' => 'AURORA LINHAS AEREAS', 'percentual' => $papel === 'proprietario' ? 100 : null];
        }
    }
    foreach (array_chunk($aeronaves, 100) as $lote) {
        DB::table('aeronaves')->insert($lote);
    }
    foreach (array_chunk($vinculos, 100) as $lote) {
        DB::table('aeronave_vinculos')->insert($lote);
    }
    DB::table('aeronave_cargas')->insert(['data_arquivo' => '2026-11-25', 'aeronaves' => $total, 'carregado_em' => now()]);

    $lookup = app(AeronavesLookup::class);
    $lista = $lookup->daEmpresa('11222333000181');
    expect($lista)->toHaveCount(AeronavesLookup::LIMITE)
        ->and($lista[0]->papeis)->toBe(['proprietario', 'operador'])
        ->and($lookup->contagemDaEmpresa('11222333000181'))->toBe(['total' => $total, 'ativas' => $total - 3]);

    Http::fake();
    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(BensRecebiveis::class, ['cnpj' => '11222333000181'])
        ->assertSee(($total - 3).' aeronave(s) · ao menos '.($total - 3).' sem ônus')
        ->assertSee('Mostrando '.AeronavesLookup::LIMITE." de {$total} (ativas primeiro)");
});

it('lists the participations (quotas/shares) with links and "mostrando N de M"', function () {
    useCnpjSqlite();
    $schema = Schema::connection('cnpj');
    $schema->create('socios', function (Blueprint $t): void {
        foreach (['cnpj_basico', 'nome_socio', 'cnpj_cpf_do_socio', 'identificador_de_socio', 'qualificacao_do_socio', 'data_entrada_sociedade'] as $c) {
            $t->string($c)->nullable();
        }
    });
    $schema->create('empresas', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('razao_social')->nullable();
    });
    $schema->create('estabelecimentos', function (Blueprint $t): void {
        foreach (['cnpj_basico', 'cnpj_ordem', 'situacao_cadastral', 'uf'] as $c) {
            $t->string($c)->nullable();
        }
    });
    $schema->create('qualificacoes_socios', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });
    $db = DB::connection('cnpj');
    for ($i = 1; $i <= 12; $i++) {
        $basico = sprintf('%08d', 30000000 + $i);
        $db->table('socios')->insert(['cnpj_basico' => $basico, 'nome_socio' => 'PADARIA AURORA LTDA', 'cnpj_cpf_do_socio' => '11222333', 'identificador_de_socio' => '1', 'qualificacao_do_socio' => '22']);
        $db->table('empresas')->insert(['cnpj_basico' => $basico, 'razao_social' => sprintf('INVESTIDA %02d LTDA', $i)]);
        $db->table('estabelecimentos')->insert(['cnpj_basico' => $basico, 'cnpj_ordem' => '0001', 'situacao_cadastral' => '02', 'uf' => 'SP']);
    }

    Http::fake();
    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(BensRecebiveis::class, ['cnpj' => '11222333000181'])
        ->assertSee('sócia de 12 empresa(s)')
        ->assertSee('INVESTIDA 01 LTDA')
        ->assertSee(route('empresas.perfil', Cnpj::matrizFromBasico('30000001')), false)
        ->assertDontSee('INVESTIDA 11 LTDA')
        ->assertSee('Mostrando 10 de 12');
});

it('says "ao menos" when the public contracts hit the page ceiling', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    config()->set('risco.contratos.max_paginas', 1);
    app()->forgetInstance(ContratosPublicos::class);
    $cheia = array_map(fn (int $i) => [
        'id' => $i, 'numero' => (string) $i, 'objeto' => 'Pães', 'dataAssinatura' => '2025-01-01', 'dataInicioVigencia' => '2025-01-01',
        'dataFimVigencia' => '2099-01-01', 'valorInicialCompra' => 100, 'valorFinalCompra' => 100,
        'fornecedor' => ['cnpjFormatado' => '11.222.333/0001-81'],
    ], range(1, 15));
    Http::fake(['*portaldatransparencia*' => Http::response($cheia)]);

    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(BensRecebiveis::class, ['cnpj' => '11222333000181'])
        ->assertSee('ao menos 15 contrato(s) federal(is) vigente(s)')
        ->assertSee('Ao menos 15 contrato(s) vigente(s), somando');
});

it('treats reserved marks and "MATRICULA CANCELADA" without a date as not active (never "sem ônus")', function () {
    config()->set('bens.aeronaves.min_aeronaves', 1);
    $dona = [['NOME' => 'PADARIA AURORA LTDA', 'DOCUMENTO' => '11222333000181', 'PERCENTUAL' => '100.00', 'UF' => 'SP']];
    $arquivo = rabArquivo([
        ['marca' => 'PRAAA', 'props' => $dona],
        ['marca' => 'PRRES', 'gravame' => 'RESERVADAS AS MARCAS', 'props' => $dona],
        ['marca' => 'PRMCX', 'gravame' => 'ARRENDAMENTO OPERACIONAL / MATRICULA CANCELADA', 'props' => $dona], // sem DT_CANC
    ], '2026-11-25');
    test()->artisan('vigilo:aeronaves-anac', ['--arquivo' => $arquivo])->assertSuccessful();

    $lookup = app(AeronavesLookup::class);
    $lista = collect($lookup->daEmpresa('11222333000181'))->keyBy('marca');
    expect($lista->keys()->first())->toBe('PRAAA') // ativa primeiro
        ->and($lista['PRRES']->reservada())->toBeTrue()
        ->and($lista['PRRES']->ativa())->toBeFalse()
        ->and($lista['PRRES']->livre())->toBeFalse()
        ->and($lista['PRMCX']->cancelada())->toBeTrue()
        ->and($lista['PRMCX']->onus())->toBe('ARRENDAMENTO OPERACIONAL')
        ->and($lookup->contagemDaEmpresa('11222333000181'))->toBe(['total' => 3, 'ativas' => 1]);

    Http::fake();
    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(BensRecebiveis::class, ['cnpj' => '11222333000181'])
        ->assertSee('1 aeronave(s) · 1 sem ônus')
        ->assertSee('marca reservada · não matriculada')
        ->assertSee('matrícula cancelada');
});

it('refuses a file much smaller than the previous load (partial file)', function () {
    config()->set('bens.aeronaves.min_aeronaves', 1);
    test()->artisan('vigilo:aeronaves-anac', ['--arquivo' => rabArquivo(rabAeronaves(), '2026-11-25')])->assertSuccessful();

    $parcial = array_slice(rabAeronaves(), 0, 4); // 4 de 6 = queda de 33%
    test()->artisan('vigilo:aeronaves-anac', ['--arquivo' => rabArquivo($parcial, '2026-11-26')])
        ->expectsOutputToContain('contra 6 na carga anterior')->assertFailed();
    expect(DB::table('aeronaves')->count())->toBe(6);

    config()->set('bens.aeronaves.queda_maxima', 0.5); // 33% < 50%: aceita
    test()->artisan('vigilo:aeronaves-anac', ['--arquivo' => rabArquivo($parcial, '2026-11-26')])->assertSuccessful();
    expect(DB::table('aeronaves')->count())->toBe(4);
});

it('records a failed run and keeps the copy when the ANAC download fails', function () {
    // Um fake só (o Http::fake acumula callbacks): a ANAC responde e depois cai.
    config()->set('bens.aeronaves.min_aeronaves', 1);
    $fora = false;
    Http::fake(['*anac*' => function () use (&$fora) {
        return $fora ? Http::response('indisponível', 503) : Http::response(rabCsv(rabAeronaves()));
    }]);
    test()->artisan('vigilo:aeronaves-anac')->assertSuccessful();

    $fora = true;
    test()->artisan('vigilo:aeronaves-anac', ['--forcar' => true])->expectsOutputToContain('HTTP 503')->assertFailed();

    expect(DB::table('aeronaves')->count())->toBe(6)
        ->and(MaintenanceRun::ultima('aeronaves')?->isSuccess())->toBeFalse();
});

it('says "not checked" on the person page before the registry copy is loaded', function () {
    $chave = pessoaMariaSouza();

    test()->actingAs(User::factory()->for(Organization::factory())->create())->get(route('pessoas.perfil', $chave))
        ->assertOk()
        ->assertSee('Registro de aeronaves indisponível no momento')
        ->assertDontSee('Nenhuma aeronave em nome de pessoa física');
});

it('lists the INPI trademarks of the company: registered first, pending, extinct only counted', function () {
    useCnpjSqlite();
    createInpiTables();
    seedMarca('11222333000181', '900000001', 'AURORA');
    seedMarca('11222333000262', '900000001', 'AURORA'); // co-titular (filial): uma vez só
    seedMarca('11222333000181', '900000002', 'AURORA GOURMET', 'Aguardando prazo de apresentação de oposição', null);
    seedMarca('11222333000181', '900000003', 'AURORA VELHA', 'Pedido definitivamente arquivado', null);

    Http::fake();
    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(BensRecebiveis::class, ['cnpj' => '11222333000181'])
        ->assertSee('1 marca(s) registrada(s) em vigor')
        ->assertSeeInOrder(['Marcas registradas (INPI)', 'AURORA', 'registro em vigor', 'até 01/01/2034', 'AURORA GOURMET', 'pedido'])
        ->assertDontSee('AURORA VELHA')
        ->assertSee('+ 1 extinta(s), arquivada(s) ou indeferida(s), não listadas')
        ->assertSee('cópia de 25/11/2026');
});

it('says "not checked" when the INPI copy is missing (never "no trademarks")', function () {
    Http::fake(); // base CNPJ dos testes fora (porta 1)
    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(BensRecebiveis::class, ['cnpj' => '11222333000181'])
        ->assertSee('Cópia das marcas do INPI indisponível no momento')
        ->assertDontSee('Nenhuma marca em vigor');
});

it('classifies the INPI status into registered, pending or inactive', function (string $situacao, string $estado) {
    expect(Marca::estadoDe($situacao))->toBe($estado);
})->with([
    ['Registro de marca em vigor', 'em_vigor'],
    ['Registro', 'em_vigor'],
    ['Aguardando exame de petição de prorrogação (registro de marca em vigor)', 'em_vigor'],
    ['Registro sub júdice', 'em_vigor'],
    ['Aguardando prazo de apresentação de oposição', 'pedido'],
    ['Pedido de registro de marca indeferido (mantido em grau de recurso)', 'inativa'],
    ['Aguardando apresentação ou exame de recurso contra o indeferimento', 'pedido'],
    ['Registro de marca em vigor por decisão judicial (em sede de liminar)', 'em_vigor'],
    ['Aguardando prazo extraordinario de prorrogação', 'em_vigor'],
    ['Registro "Sub-Júdice"', 'em_vigor'],
    ['Registro com Processo Administrativo de Nulidade', 'em_vigor'],
    ['Registro com Caducidade', 'inativa'],
    ['Para extinguir registro de marca pela expiração do prazo de vigência', 'inativa'],
    ['Pedido arquivado de ofício', 'inativa'],
    ['Inviavel', 'inativa'],
    ['Pedido Deferido', 'pedido'],
    ['Pedido definitivamente arquivado', 'inativa'],
    ['Registro de marca extinto', 'inativa'],
    ['Pedido de registro de marca indeferido (sem interposição de recurso)', 'inativa'],
    ['Pedido considerado inexistente', 'inativa'],
    ['Registro de marca nulo', 'inativa'],
    ['Registro de marca cancelado de ofício', 'inativa'],
    ['Não Publicado', 'inativa'],
]);

it('the INPI copy is a weekly routine on the admin card (registered by the maintenance script)', function () {
    test()->artisan('vigilo:rotina', ['tipo' => 'marcas_inpi', 'resultado' => 'ok', '--message' => 'marcas do INPI carregadas: 4760638 de pessoa jurídica'])->assertSuccessful();
    $linha = collect(app(StatusRotinas::class)->todas())->firstWhere('nome', 'Marcas do INPI (cópia)');
    expect($linha?->situacao)->toBe('ok');

    $this->travelTo(now()->addDays(9));
    $linha = collect(app(StatusRotinas::class)->todas())->firstWhere('nome', 'Marcas do INPI (cópia)');
    expect($linha?->situacao)->toBe('atrasada');
});

it('lists at most 10 active trademarks and says "mostrando N de M"', function () {
    useCnpjSqlite();
    createInpiTables();
    for ($i = 1; $i <= 12; $i++) {
        seedMarca('11222333000181', sprintf('9100000%02d', $i), sprintf('AURORA %02d', $i));
    }

    Http::fake();
    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(BensRecebiveis::class, ['cnpj' => '11222333000181'])
        ->assertSee('12 marca(s) registrada(s) em vigor')
        ->assertSee('Mostrando 10 de 12 (em vigor primeiro)');
});

it('says "not checked" when the INPI copy was never loaded (control table empty)', function () {
    useCnpjSqlite();
    createInpiTables(); // tabelas criadas, nenhuma carga ainda

    Http::fake();
    Livewire::actingAs(User::factory()->for(Organization::factory())->create())->test(BensRecebiveis::class, ['cnpj' => '11222333000181'])
        ->assertSee('Cópia das marcas do INPI indisponível no momento')
        ->assertDontSee('Nenhuma marca em vigor');
});

it('a manual INPI load without a registered run is judged by the copy age', function () {
    useCnpjSqlite();
    createInpiTables();
    DB::connection('cnpj')->table('inpi_cargas')->insert(['versao' => 'v1', 'marcas' => 10, 'carregado_em' => now()->subDays(10)->toDateTimeString()]);

    $linha = collect(app(StatusRotinas::class)->todas())->firstWhere('nome', 'Marcas do INPI (cópia)');
    expect($linha?->situacao)->toBe('atrasada');
});
