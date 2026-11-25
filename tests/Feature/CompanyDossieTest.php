<?php

declare(strict_types=1);

use App\DTO\Risco\EmpresaLigadaRisco;
use App\DTO\Risco\RiscoAoRedorReport;
use App\DTO\Vigilancia\DetalhesEmpresa;
use App\DTO\Vigilancia\RegimeSimples;
use App\Models\CompanyPartner;
use App\Models\CompanySnapshot;
use App\Models\DirtyListEmployer;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

// Por padrão a base CNPJ (grafo) fica indisponível → o dossiê gera sem as seções
// do grafo (beneficiários/grupo/filiais), que degradam. O teste dedicado abaixo
// popula a base para exercitar essas seções.
beforeEach(function (): void {
    config()->set('database.connections.cnpj', [
        'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => false,
    ]);
    DB::purge('cnpj');
});

/** Base CNPJ (sqlite) com centro + sócio PJ (grupo) + sócio PF (beneficiário) + filial. */
function bootDossieCnpjBase(): void
{
    $schema = Schema::connection('cnpj');
    $schema->create('estabelecimentos', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('cnpj_ordem');
        $t->string('cnpj_dv');
        $t->string('situacao_cadastral')->nullable();
        $t->string('nome_fantasia')->nullable();
        $t->string('cep')->nullable();
        $t->string('numero')->nullable();
        $t->string('complemento')->nullable();
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
    $schema->create('qualificacoes_socios', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });
    $schema->create('municipios', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });

    DB::connection('cnpj')->table('estabelecimentos')->insert([
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0001', 'cnpj_dv' => '81', 'situacao_cadastral' => '02', 'nome_fantasia' => null, 'uf' => null],
        ['cnpj_basico' => '11222333', 'cnpj_ordem' => '0002', 'cnpj_dv' => '62', 'situacao_cadastral' => '08', 'nome_fantasia' => 'FILIAL RJ', 'uf' => 'RJ'],
    ]);
    DB::connection('cnpj')->table('empresas')->insert([
        ['cnpj_basico' => '11222333', 'razao_social' => 'EMPRESA CENTRO'],
        ['cnpj_basico' => '44555666', 'razao_social' => 'EMPRESA GRUPO'],
    ]);
    DB::connection('cnpj')->table('socios')->insert([
        ['cnpj_basico' => '11222333', 'nome_socio' => 'MARIA', 'cnpj_cpf_do_socio' => '***111**', 'identificador_de_socio' => '2'],
        ['cnpj_basico' => '11222333', 'nome_socio' => 'HOLDING', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
        ['cnpj_basico' => '44555666', 'nome_socio' => 'HOLDING', 'cnpj_cpf_do_socio' => '99888777000166', 'identificador_de_socio' => '1'],
    ]);
}

it('downloads the due-diligence PDF for the company owner', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);
    CompanySnapshot::factory()->create([
        'monitored_company_id' => $company->id,
        'razao_social' => 'MATRIZ INDUSTRIAL LTDA',
    ]);

    $this->actingAs($user)
        ->get(route('companies.dossie', $company))
        ->assertOk()
        ->assertDownload('dossie-11222333000181.pdf');
});

it('generates the PDF even before any snapshot exists', function () {
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    $this->actingAs($user)
        ->get(route('companies.dossie', $company))
        ->assertOk();
});

it('renders the rich risk sections in the PDF (sanction, dirty list, PGFN and partners)', function () {
    // Exercita as ramificações "caras" do Blade do PDF (box de risco avaliado,
    // lista de sanções, seção Lista Suja, seção PGFN, QSA) — um erro de Blade
    // ali derruba o render do dompdf e falha o teste.
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake(function (Request $request) {
        if (str_contains($request->url(), '/ceis')) {
            return Http::response([[
                'pessoa' => ['cnpjFormatado' => '11.222.333/0001-81'],
                'tipoSancao' => ['descricaoResumida' => 'Empresa inidônea'],
                'orgaoSancionador' => ['nome' => 'TCU'],
                'dataInicioSancao' => '01/03/2024',
            ]]);
        }

        return Http::response([]);
    });

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);
    $snapshot = CompanySnapshot::factory()->create([
        'monitored_company_id' => $company->id,
        'razao_social' => 'MATRIZ INDUSTRIAL LTDA',
    ]);
    CompanyPartner::factory()->create([
        'company_snapshot_id' => $snapshot->id,
        'nome' => 'FULANO DE TAL',
        'qualificacao' => 'Sócio-Administrador',
    ]);
    DirtyListEmployer::query()->create([
        'documento' => '11222333000181',
        'nome' => 'MATRIZ INDUSTRIAL LTDA',
        'uf' => 'PA',
        'ano' => '2022',
        'imported_at' => now(),
    ]);
    createPgfnTables();
    seedPgfnDivida('11222333000181', 12345.67, ajuizado: true);

    $this->actingAs($user)
        ->get(route('companies.dossie', $company))
        ->assertOk()
        ->assertDownload('dossie-11222333000181.pdf');
});

it('renders the graph sections (beneficiaries, economic group, branches) when the base is available', function () {
    bootDossieCnpjBase(); // centro + sócio PJ (grupo) + sócio PF + filial
    createPgfnTables();
    seedPgfnDivida('44555666000100', 7777.77); // EMPRESA GRUPO: dívida na linha do grupo

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    $grupo = null;
    View::composer('dossie.pdf', function ($view) use (&$grupo): void {
        $grupo = $view->getData()['grupo'];
    });

    // Gera o PDF exercitando as 3 ramificações novas do Blade (beneficiários,
    // grupo econômico, filiais). Um erro de Blade ali derrubaria o render.
    $this->actingAs($user)
        ->get(route('companies.dossie', $company))
        ->assertOk()
        ->assertDownload('dossie-11222333000181.pdf');

    $empresaGrupo = collect($grupo)->firstWhere('label', 'EMPRESA GRUPO');
    expect($empresaGrupo['divida']?->total)->toEqualWithDelta(7777.77, 0.001);
});

it('says in the PDF that the PGFN was not checked when it is unavailable', function () {
    bootDossieCnpjBase(); // grafo de pé, mas sem as tabelas da PGFN

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    $dados = null;
    View::composer('dossie.pdf', function ($view) use (&$dados): void {
        $dados = $view->getData();
    });

    $this->actingAs($user)->get(route('companies.dossie', $company))->assertOk();

    $empresaGrupo = collect($dados['grupo'])->firstWhere('label', 'EMPRESA GRUPO');
    expect($dados['assessment']->pgfnDisponivel)->toBeFalse()
        ->and($empresaGrupo['pgfn_consultada'])->toBeFalse()
        ->and($empresaGrupo['divida'])->toBeNull();
});

it('redirects guests to login', function () {
    $company = MonitoredCompany::factory()->create(['cnpj' => '11222333000181']);

    $this->get(route('companies.dossie', $company))->assertRedirect(route('login'));
});

it('forbids downloading the dossier of a company from another organization', function () {
    $user = User::factory()->for(Organization::factory())->create();
    $foreign = MonitoredCompany::factory()->create();

    $this->actingAs($user)->get(route('companies.dossie', $foreign))->assertForbidden();
});

it('renders the cadastral history section (with the frequent-move warning)', function () {
    createHistoricoTables();
    DB::connection('cnpj')->table('historico_execucoes')->insert(['referencia' => '2026-10', 'status' => 'ok']);
    foreach ([1, 2, 3] as $i) {
        seedHistorico('11222333000181', 'endereco', json_encode(['logradouro' => 'RUA '.$i]), json_encode(['logradouro' => 'RUA '.($i + 1)]), now()->subMonths($i)->toDateTimeString());
    }
    seedHistorico('11222333', 'socio_saida', json_encode(['nome' => 'BRUNO LIMA']), null, now()->subMonth()->toDateTimeString());

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    $historico = null;
    View::composer('dossie.pdf', function ($view) use (&$historico): void {
        $historico = $view->getData()['historico'];
    });

    $this->actingAs($user)
        ->get(route('companies.dossie', $company))
        ->assertOk()
        ->assertDownload('dossie-11222333000181.pdf');

    expect($historico->eventos)->toHaveCount(4)
        ->and($historico->alertaEndereco())->toBeTrue();
});

it('lists the participations in their own PDF section, not as the economic group', function () {
    bootDossieCnpjBase();
    // o CENTRO (11222333) é sócio PJ da INVESTIDA → participação, não grupo
    DB::connection('cnpj')->table('empresas')->insert(['cnpj_basico' => '20000001', 'razao_social' => 'INVESTIDA SA']);
    DB::connection('cnpj')->table('socios')->insert([
        'cnpj_basico' => '20000001', 'nome_socio' => 'EMPRESA CENTRO', 'cnpj_cpf_do_socio' => '11222333000181', 'identificador_de_socio' => '1',
    ]);

    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    $dados = null;
    View::composer('dossie.pdf', function ($view) use (&$dados): void {
        $dados = $view->getData();
    });

    $this->actingAs($user)->get(route('companies.dossie', $company))->assertOk();

    expect(collect($dados['participacoes'])->pluck('label')->all())->toBe(['INVESTIDA SA'])
        ->and(collect($dados['grupo'])->pluck('label')->all())->toBe(['EMPRESA GRUPO']);
});

/** Gera o dossiê e devolve o HTML do PDF (mesmos dados que o dompdf recebeu). */
function dossieHtml(MonitoredCompany $company, User $user): string
{
    $dados = null;
    View::composer('dossie.pdf', function ($view) use (&$dados): void {
        $dados = $view->getData();
    });

    test()->actingAs($user)->get(route('companies.dossie', $company))->assertOk();

    return view('dossie.pdf', $dados)->render();
}

it('includes the recovery sections and degrades each one when its source is down', function () {
    // base CNPJ vazia (sem tabelas): todas as fontes da base falham
    $org = Organization::factory()->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    $html = dossieHtml($company, User::factory()->for($org)->create());

    expect($html)->toContain('Risco ao redor (empresas ligadas)')
        ->toContain('Contratos públicos (fornecedora do governo federal)')
        ->toContain('Bens e recebíveis (onde procurar penhora)')
        ->toContain('Indícios de esvaziamento e sucessão')
        ->toContain('Dívida ativa da União indisponível agora')
        ->not->toContain('Nenhum contrato do Executivo Federal'); // não consultado ≠ nenhum
});

it('lists aircraft, trademarks, the full participations and the asset-stripping signals in the PDF', function () {
    bootDossieCnpjBase();
    createInpiTables();
    seedMarca('11222333000181', '900000001', 'AURORA');
    for ($i = 1; $i <= 12; $i++) { // o grafo traz só as primeiras; o dossiê, a lista inteira
        DB::connection('cnpj')->table('empresas')->insert(['cnpj_basico' => sprintf('2%07d', $i), 'razao_social' => sprintf('INVESTIDA %02d SA', $i)]);
        DB::connection('cnpj')->table('socios')->insert(['cnpj_basico' => sprintf('2%07d', $i), 'nome_socio' => 'EMPRESA CENTRO', 'cnpj_cpf_do_socio' => '11222333', 'identificador_de_socio' => '1']);
    }
    DB::table('aeronave_cargas')->insert(['data_arquivo' => '2026-11-25', 'aeronaves' => 1, 'carregado_em' => now()]);
    DB::table('aeronaves')->insert(['marca' => 'PRAAA', 'modelo' => 'EMB-505', 'fabricante' => 'EMBRAER', 'gravame' => 'NENHUM GRAVAME REGISTRADO']);
    DB::table('aeronave_vinculos')->insert(['marca' => 'PRAAA', 'papel' => 'proprietario', 'tipo' => 'PJ', 'cnpj' => '11222333000181', 'cnpj_basico' => '11222333', 'nome' => 'EMPRESA CENTRO', 'nome_norm' => 'EMPRESA CENTRO', 'percentual' => 100]);

    $org = Organization::factory()->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    $html = dossieHtml($company, User::factory()->for($org)->create());

    expect($html)->toContain('PRAAA — EMBRAER EMB-505')
        ->toContain('sem ônus')
        ->toContain('AURORA')
        ->toContain('1 registro(s) em vigor')
        ->toContain('INVESTIDA 12 SA') // a 12ª participação também está no PDF
        ->toContain('12 empresa(s)');
});

/** Dados que o dossiê entregou ao template (para trocar uma seção e renderizar de novo). */
function dossieDados(MonitoredCompany $company, User $user): array
{
    $dados = null;
    View::composer('dossie.pdf', function ($view) use (&$dados): void {
        $dados = $view->getData();
    });

    test()->actingAs($user)->get(route('companies.dossie', $company))->assertOk();

    return $dados;
}

function dossieEmpresa(array $atributos = []): array
{
    $org = Organization::factory()->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181'] + $atributos);
    CompanySnapshot::factory()->create(['monitored_company_id' => $company->id]);

    return [$company, User::factory()->for($org)->create()];
}

it('lists the FULL participations (beyond what the graph brings) and says "N de M" above 300', function () {
    bootDossieCnpjBase();
    config()->set('cnpj.graph.reverse_limit', 5); // o grafo para em 5
    $socios = array_map(fn (int $i): array => ['cnpj_basico' => sprintf('2%07d', $i), 'nome_socio' => 'EMPRESA CENTRO', 'cnpj_cpf_do_socio' => '11222333', 'identificador_de_socio' => '1'], range(1, 301));
    $empresas = array_map(fn (int $i): array => ['cnpj_basico' => sprintf('2%07d', $i), 'razao_social' => sprintf('INVESTIDA %03d SA', $i)], range(1, 301));
    foreach ([array_chunk($socios, 100), array_chunk($empresas, 100)] as $i => $lotes) {
        foreach ($lotes as $lote) {
            DB::connection('cnpj')->table($i === 0 ? 'socios' : 'empresas')->insert($lote);
        }
    }
    [$company, $user] = dossieEmpresa();

    $dados = dossieDados($company, $user);
    $html = view('dossie.pdf', $dados)->render();

    expect($dados['participacoes'])->toHaveCount(300)
        ->and($dados['participacoesTotal'])->toBe(301)
        ->and($html)->toContain('INVESTIDA 150 SA')
        ->toContain('Listando 300 de 301 participações');
});

it('never says "none" about the linked companies when a source was not checked (same rule as the screen)', function () {
    [$company, $user] = dossieEmpresa();
    $dados = dossieDados($company, $user);
    $ligada = new EmpresaLigadaRisco('HOLDING SA', '99888777000166', 'sócia', 'ATIVA', false, false, false, false);

    $semPgfn = view('dossie.pdf', ['aoRedor' => new RiscoAoRedorReport([$ligada], pgfnConsultada: false, sancoesConsultadas: true, sancoesDesatualizadas: true)] + $dados)->render();
    expect($semPgfn)->toContain('Nenhum sinal entre as 1 empresa(s) ligada(s) nas fontes que puderam ser consultadas')
        ->toContain('Não consultado agora: dívida ativa (PGFN)')
        ->toContain('A cópia das sanções da CGU está desatualizada')
        ->not->toContain('Nenhuma das 1');

    $limpo = view('dossie.pdf', ['aoRedor' => new RiscoAoRedorReport([$ligada], true, true)] + $dados)->render();
    expect($limpo)->toContain('Nenhuma das 1 empresa(s) ligada(s) tem Lista Suja');

    $comDivida = new EmpresaLigadaRisco('DEVEDORA SA', '55666777000100', 'participação', 'ATIVA', false, true, false, false);
    $alerta = view('dossie.pdf', ['aoRedor' => new RiscoAoRedorReport([$ligada, $comDivida], true, true)] + $dados)->render();
    expect($alerta)->toContain('1 de 2 empresas ligadas com dívida ativa')->toContain('DEVEDORA SA');
});

it('prints capital and Simples/MEI from the base details', function () {
    [$company, $user] = dossieEmpresa();
    $dados = dossieDados($company, $user);
    $detalhes = new DetalhesEmpresa('2062', 'Sociedade Empresária Limitada', '03', 15000.5, new RegimeSimples(true, '2020-01-01'), new RegimeSimples(false));

    $html = view('dossie.pdf', ['detalhes' => $detalhes] + $dados)->render();

    expect($html)->toContain('Capital social')->toContain('15.000,50')
        ->toContain('Simples Nacional')->toContain($detalhes->simples->texto())
        ->toContain('MEI');
});

it('lists the public contracts, in force first and then the ended ones', function () {
    bootDossieCnpjBase();
    config()->set('risco.portal.api_key', 'chave-teste');
    $contrato = fn (int $id, string $fim, string $objeto): array => [
        'id' => $id, 'numero' => (string) $id, 'objeto' => $objeto, 'dataAssinatura' => '2024-01-01', 'dataInicioVigencia' => '2024-01-01',
        'dataFimVigencia' => $fim, 'valorInicialCompra' => 1000, 'valorFinalCompra' => 1000, 'fornecedor' => ['cnpjFormatado' => '11.222.333/0001-81'],
    ];
    Http::fake(['*portaldatransparencia*' => Http::response([$contrato(1, '2020-01-01', 'SERVIÇO ANTIGO'), $contrato(2, '2099-01-01', 'SERVIÇO ATUAL')]), '*' => Http::response([])]);
    [$company, $user] = dossieEmpresa();

    $html = view('dossie.pdf', dossieDados($company, $user))->render();

    expect($html)->toContain('2 contrato(s)')
        ->toContain('1 vigente(s)')
        ->toMatch('/SERVIÇO ATUAL.*vigente.*SERVIÇO ANTIGO.*encerrado/s');
});

it('uses the debtor date as the asset-stripping milestone when the company has one', function () {
    bootDossieCnpjBase();
    [$company, $user] = dossieEmpresa(['recuperacao_desde' => now(), 'recuperacao_marco' => '2021-06-30']);

    $html = view('dossie.pdf', dossieDados($company, $user))->render();

    expect($html)->toContain('Marco: data da dívida informada em 30/06/2021');
});
