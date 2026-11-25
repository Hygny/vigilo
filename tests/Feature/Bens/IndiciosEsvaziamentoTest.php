<?php

declare(strict_types=1);

use App\DTO\Esvaziamento\PossivelSucessora;
use App\Livewire\Companies\Show;
use App\Livewire\Empresas\IndiciosEsvaziamento;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;
use App\Services\Bens\IndiciosEsvaziamento as IndiciosService;
use App\Support\Cnpj;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Schema;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;

/** Base CNPJ mínima em SQLite (estabelecimentos, empresas, sócios, PGFN, histórico). */
function esvBase(): void
{
    useCnpjSqlite();
    $schema = Schema::connection('cnpj');
    $schema->create('estabelecimentos', function (Blueprint $t): void {
        foreach (['cnpj_basico', 'cnpj_ordem', 'cnpj_dv', 'situacao_cadastral', 'data_situacao_cadastral', 'data_inicio_atividade', 'cnae_fiscal_principal',
            'cep', 'numero', 'complemento', 'ddd_1', 'telefone_1', 'correio_eletronico', 'uf'] as $c) {
            $t->string($c)->nullable();
        }
    });
    $schema->create('empresas', function (Blueprint $t): void {
        $t->string('cnpj_basico');
        $t->string('razao_social')->nullable();
    });
    $schema->create('socios', function (Blueprint $t): void {
        foreach (['cnpj_basico', 'nome_socio', 'cnpj_cpf_do_socio', 'identificador_de_socio', 'qualificacao_do_socio', 'data_entrada_sociedade'] as $c) {
            $t->string($c)->nullable();
        }
    });
    $schema->create('qualificacoes_socios', function (Blueprint $t): void {
        $t->string('codigo');
        $t->string('descricao')->nullable();
    });
    createPgfnTables();
    createHistoricoTables();
    DB::connection('cnpj')->table('qualificacoes_socios')->insert(['codigo' => '49', 'descricao' => 'Sócio-Administrador']);
}

/** Empresa com matriz (endereço/CNAE/contato vazios por padrão). */
function esvEmpresa(string $basico, string $razao, array $matriz = []): void
{
    DB::connection('cnpj')->table('empresas')->insert(['cnpj_basico' => $basico, 'razao_social' => $razao]);
    DB::connection('cnpj')->table('estabelecimentos')->insert($matriz + [
        'cnpj_basico' => $basico, 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'situacao_cadastral' => '02',
        'data_inicio_atividade' => '2022-01-01', 'cnae_fiscal_principal' => '4711302', 'cep' => '09999000', 'numero' => '1', 'uf' => 'SP',
    ]);
}

function esvSocio(string $basico, string $nome, string $doc, string $entrada, string $identificador = '2', string $qualificacao = '49'): void
{
    DB::connection('cnpj')->table('socios')->insert([
        'cnpj_basico' => $basico, 'nome_socio' => $nome, 'cnpj_cpf_do_socio' => $doc,
        'identificador_de_socio' => $identificador, 'qualificacao_do_socio' => $qualificacao, 'data_entrada_sociedade' => $entrada,
    ]);
}

function esvPgfn(string $data, float $valor): void
{
    DB::connection('cnpj')->table('pgfn_dividas')->insert([
        'cnpj' => '11222333000181', 'cnpj_basico' => '11222333', 'tipo' => 'nao-previdenciario', 'situacao' => 'ATIVA',
        'data_inscricao' => $data, 'ajuizado' => false, 'valor' => $valor,
    ]);
}

/**
 * A devedora (PADARIA AURORA, 1ª inscrição em 01/03/2020, baixada em 2024) e as
 * empresas em volta: uma sucessora forte, duas médias, ruído que não deve listar.
 */
function esvCenario(): void
{
    esvBase();
    $aurora = ['cep' => '01001000', 'numero' => '10', 'complemento' => 'SALA 1', 'cnae_fiscal_principal' => '1091102'];

    esvEmpresa('11222333', 'PADARIA AURORA LTDA', $aurora + [
        'situacao_cadastral' => '08', 'data_situacao_cadastral' => '2024-06-30', 'data_inicio_atividade' => '2012-01-01',
        'ddd_1' => '11', 'telefone_1' => '33334444', 'correio_eletronico' => 'contato@aurora.com.br',
    ]);
    esvPgfn('2021-05-01', 500);
    esvPgfn('2020-03-01', 1000);
    esvSocio('11222333', 'MARIA SOUZA', '***456789**', '2012-01-01');
    esvSocio('11222333', 'JOAO LARANJA', '***111222**', '2023-01-10'); // entrou depois da dívida
    esvSocio('11222333', 'CARLOS DIRETOR', '***333444**', '2023-02-01', qualificacao: '05'); // administrador: troca de diretoria não conta
    esvEmpresa('12121212', 'DIRETOR PAES LTDA', ['cnae_fiscal_principal' => '1091102', 'data_inicio_atividade' => '2023-05-01']);
    esvSocio('12121212', 'CARLOS DIRETOR', '***333444**', '2023-05-01'); // empresa do administrador: não é procurada

    // Forte: mesmo endereço (sem complemento = compatível), mesma classe CNAE (10911), sócia em comum.
    esvEmpresa('22333444', 'AURORA PAES LTDA', ['cep' => '01001000', 'numero' => '10', 'cnae_fiscal_principal' => '1091101', 'data_inicio_atividade' => '2022-02-01']);
    esvSocio('22333444', 'MARIA SOUZA', '***456789**', '2022-02-01');
    // Médio: mesma sala ("Nº 10" / "SALA 01") + mesmo CNAE, sem sócio em comum (laranja?).
    esvEmpresa('44555666', 'CONFEITARIA DO BAIRRO LTDA', ['cep' => '01001000', 'numero' => 'Nº 10', 'complemento' => 'SALA 01', 'cnae_fiscal_principal' => '1091102', 'data_inicio_atividade' => '2023-03-01']);
    // Médio: o sócio que SAIU depois da dívida abriu uma do mesmo ramo noutro lugar.
    esvEmpresa('88999000', 'PEDRO PAES LTDA', ['cnae_fiscal_principal' => '1091102', 'data_inicio_atividade' => '2025-01-01']);
    esvSocio('88999000', 'PEDRO ANTIGO', '***999888**', '2025-01-01');
    // Só sócio em comum (um sinal): não lista, conta.
    esvEmpresa('33444555', 'MARIA HOLDING LTDA', ['cnae_fiscal_principal' => '6462000', 'data_inicio_atividade' => '2021-01-01']);
    esvSocio('33444555', 'MARIA SOUZA', '***456789**', '2021-01-01');
    // Escritório de contabilidade: mesmo endereço + mesmo e-mail, outro ramo, sem sócio — não lista, conta.
    esvEmpresa('77888999', 'CLIENTE DO CONTADOR LTDA', ['cep' => '01001000', 'numero' => '10', 'correio_eletronico' => 'CONTATO@aurora.com.br', 'data_inicio_atividade' => '2022-06-01']);
    // Antes da dívida: ignorada.
    esvEmpresa('55666777', 'VELHA LTDA', ['cnae_fiscal_principal' => '1091102', 'data_inicio_atividade' => '2010-01-01']);
    esvSocio('55666777', 'MARIA SOUZA', '***456789**', '2010-01-01');
    // Mesmo CPF mascarado, outro nome: outra pessoa.
    esvEmpresa('66777888', 'HOMONIMA PAES LTDA', ['cnae_fiscal_principal' => '1091102', 'data_inicio_atividade' => '2022-01-01']);
    esvSocio('66777888', 'MARIA SOUZA SILVA', '***456789**', '2022-01-01');
    // Participação: a devedora entrou como sócia depois da dívida.
    esvEmpresa('99000111', 'INVESTIDA NOVA LTDA', ['data_inicio_atividade' => '2022-05-05']);
    esvSocio('99000111', 'PADARIA AURORA LTDA', '11222333', '2022-05-05', identificador: '1');

    // Histórico: saída de sócio e redução de capital depois da dívida.
    DB::connection('cnpj')->table('historico_execucoes')->insert(['referencia' => '2026-10', 'status' => 'ok']);
    seedHistorico('11222333', 'socio_saida', json_encode(['nome' => 'PEDRO ANTIGO', 'documento' => '***999888**', 'entrada' => '2015-01-01', 'qualificacao' => '49']), null, '2026-10-19 06:00:00', '2026-10');
    seedHistorico('11222333', 'capital_social', '100000', '1000', '2026-11-23 06:00:00', '2026-11');
}

function esvUser(): User
{
    return User::factory()->for(Organization::factory())->create();
}

it('finds possible successors after the first PGFN inscription, ranked by matching signals', function () {
    esvCenario();

    $r = app(IndiciosService::class)->para('11222333000181');

    expect($r->marco)->toBe('2020-03-01')
        ->and($r->origemMarco)->toBe('pgfn')
        ->and($r->dividaTotal)->toEqualWithDelta(1500.0, 0.001)
        ->and($r->inscricoes)->toBe(2)
        ->and(array_map(fn (PossivelSucessora $s) => $s->nome, $r->sucessoras ?? []))->toBe(['AURORA PAES LTDA', 'PEDRO PAES LTDA', 'CONFEITARIA DO BAIRRO LTDA'])
        ->and($r->sucessoras[0]->sinais)->toBe(['endereco', 'cnae', 'socio'])
        ->and($r->sucessoras[0]->forte())->toBeTrue()
        ->and($r->sucessoras[0]->sociosEmComum)->toBe(['MARIA SOUZA'])
        ->and($r->sucessoras[0]->cnpj)->toBe(Cnpj::matrizFromBasico('22333444'))
        ->and($r->sucessoras[1]->sinais)->toBe(['cnae', 'socio'])
        ->and($r->sucessoras[1]->sociosEmComum)->toBe(['PEDRO ANTIGO']) // quem saiu também é procurado
        ->and($r->sucessoras[2]->sinais)->toBe(['endereco', 'cnae'])
        ->and($r->sucessoras[2]->forte())->toBeFalse()
        ->and($r->totalSucessoras)->toBe(3)
        ->and($r->outrasLigadas)->toBe(2) // holding (só sócio) e cliente do contador (endereço + e-mail)
        ->and($r->encerradaSituacao)->toBe('BAIXADA')
        ->and($r->encerradaEm)->toBe('2024-06-30')
        ->and(array_map(fn ($m) => $m->nome, $r->entradas ?? []))->toBe(['JOAO LARANJA'])
        ->and($r->quadroTodoAposMarco)->toBeFalse()
        ->and(array_map(fn ($m) => $m->nome, $r->saidas ?? []))->toBe(['PEDRO ANTIGO'])
        ->and($r->reducoesCapital[0]->de ?? null)->toBe(100000.0)
        ->and($r->reducoesCapital[0]->para ?? null)->toBe(1000.0)
        ->and(array_map(fn ($p) => $p->nome, $r->participacoes ?? []))->toBe(['INVESTIDA NOVA LTDA'])
        ->and($r->historicoDesde)->toBe('2026-10')
        ->and($r->nivel())->toBe('alto')
        ->and($r->completo())->toBeTrue();
});

it('in a crowded building only the same room counts as the same address', function () {
    esvCenario();
    for ($i = 1; $i <= 11; $i++) { // 11 vizinhos sem sala no mesmo CEP+número, mesmo ramo
        esvEmpresa(sprintf('5%07d', $i), "LOJA {$i} LTDA", ['cep' => '01001000', 'numero' => '10', 'cnae_fiscal_principal' => '1091102', 'data_inicio_atividade' => '2022-01-01']);
    }

    $r = app(IndiciosService::class)->para('11222333000181');
    $porNome = collect($r->sucessoras)->keyBy('nome');

    expect($porNome->keys()->all())->toEqualCanonicalizing(['AURORA PAES LTDA', 'PEDRO PAES LTDA', 'CONFEITARIA DO BAIRRO LTDA'])
        ->and($porNome['AURORA PAES LTDA']->sinais)->toBe(['cnae', 'socio']) // prédio lotado: sem sala, endereço não conta
        ->and($porNome['CONFEITARIA DO BAIRRO LTDA']->sinais)->toBe(['endereco', 'cnae']) // mesma sala conta
        ->and($r->outrasLigadas)->toBe(1); // só a holding: no prédio lotado, vizinho sem a mesma sala nem vira candidata (nem o cliente do contador)
});

it('flags a fully replaced board and says "nothing found" only when nothing is found', function () {
    esvBase();
    esvEmpresa('11222333', 'PADARIA AURORA LTDA', ['data_inicio_atividade' => '2012-01-01']);
    esvPgfn('2020-03-01', 1000);
    esvSocio('11222333', 'JOAO LARANJA', '***111222**', '2023-01-10');
    esvSocio('11222333', 'ANA CONSELHEIRA', '***555666**', '2010-01-01', qualificacao: '08'); // diretoria antiga não impede o todo trocado
    esvSocio('11222333', 'LUIZ LIQUIDANTE', '***777888**', '2024-01-01', qualificacao: '13'); // liquidante não é dono

    $r = app(IndiciosService::class)->para('11222333000181');
    expect($r->quadroTodoAposMarco)->toBeTrue()
        ->and(array_map(fn ($m) => $m->nome, $r->entradas ?? []))->toBe(['JOAO LARANJA'])
        ->and($r->nivel())->toBe('medio');

    DB::connection('cnpj')->table('socios')->update(['data_entrada_sociedade' => '2015-01-01']);
    Cache::flush();
    $limpo = app(IndiciosService::class)->para('11222333000181');
    expect($limpo->indicios())->toBe([])->and($limpo->nivel())->toBe('nenhum');

    Livewire::actingAs(esvUser())->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181'])
        ->assertSee('Nada encontrado')
        ->assertSee('Nenhuma empresa surgiu depois da dívida');
});

it('renders the card without masked CPFs and links each successor to its profile', function () {
    esvCenario();

    Livewire::actingAs(esvUser())->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181'])
        ->assertSee('Indícios fortes')
        ->assertSee('1ª inscrição em dívida ativa da União em')
        ->assertSee('01/03/2020')
        ->assertSee('AURORA PAES LTDA')
        ->assertSee(route('empresas.perfil', Cnpj::matrizFromBasico('22333444')), false)
        ->assertSeeInOrder(['AURORA PAES LTDA', 'forte', 'mesmo endereço', 'mesmo ramo (CNAE)', 'sócio em comum', 'MARIA SOUZA'])
        ->assertSee('Mais 2 empresa(s) ligada(s)')
        ->assertSee('Baixada em 30/06/2024')
        ->assertSee('JOAO LARANJA')
        ->assertSee('PEDRO ANTIGO')
        ->assertSee('Reduzido de R$ 100.000,00 para R$ 1.000,00')
        ->assertSee('INVESTIDA NOVA LTDA')
        ->assertSee('desde 10/2026')
        ->assertDontSee('***456789**')
        ->assertDontSee('***111222**');
});

it('asks for the debt date when there is no PGFN inscription and validates it', function () {
    esvCenario();
    DB::connection('cnpj')->table('pgfn_dividas')->delete();

    Livewire::actingAs(esvUser())->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181'])
        ->assertSee('Sem inscrição em dívida ativa da União para usar como marco')
        ->assertDontSee('AURORA PAES LTDA')
        ->set('data', '2999-01-01')->call('aplicarData')->assertHasErrors('data')
        ->set('data', '01/03/2020')->call('aplicarData')->assertHasErrors('data')
        ->set('data', '2020-03-01')->call('aplicarData')->assertHasNoErrors()
        ->assertSee('data informada')
        ->assertSee('AURORA PAES LTDA')
        ->call('usarPgfn')
        ->assertSee('Sem inscrição em dívida ativa da União');
});

it('never lets the client set the applied date directly', function () {
    esvCenario();

    Livewire::actingAs(esvUser())->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181'])
        ->set('marcoInformado', '1900-01-01');
})->throws(CannotUpdateLockedPropertyException::class);

it('degrades block by block and only caches a complete report', function () {
    esvCenario();
    Schema::connection('cnpj')->drop('historico_cadastral');

    $r = app(IndiciosService::class)->para('11222333000181');
    expect($r->saidas)->toBeNull()
        ->and($r->reducoesCapital)->toBeNull()
        ->and($r->totalSucessoras)->toBe(2) // sem o histórico, o sócio que saiu não é procurado
        ->and($r->completo())->toBeFalse()
        ->and(Cache::has('esvaziamento:11222333000181:pgfn'))->toBeFalse();

    Livewire::actingAs(esvUser())->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181'])
        ->assertSee('Histórico de saídas de sócio indisponível no momento')
        ->assertSee('AURORA PAES LTDA');
});

it('serves the complete report from a SERIALIZING cache (as Redis in production)', function () {
    config()->set('cache.stores.array.serialize', true);
    app('cache')->forgetDriver('array');
    esvCenario();
    $primeiro = app(IndiciosService::class)->para('11222333000181');
    Schema::connection('cnpj')->drop('socios');

    $segundo = app(IndiciosService::class)->para('11222333000181');
    expect($segundo->totalSucessoras)->toBe($primeiro->totalSucessoras)
        ->and($segundo->sucessoras[0] ?? null)->toBeInstanceOf(PossivelSucessora::class) // nada de __PHP_Incomplete_Class
        ->and($segundo->participacoes[0]->nome ?? null)->toBe('INVESTIDA NOVA LTDA')
        ->and($segundo->saidas[0]->nome ?? null)->toBe('PEDRO ANTIGO');
});

it('says the PGFN is unavailable (never "no debt") when the base is down', function () {
    // sem useCnpjSqlite: a conexão cnpj dos testes recusa (porta 1)
    Livewire::actingAs(esvUser())->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181'])
        ->assertSee('Dívida ativa da União indisponível agora')
        ->assertDontSee('Sem inscrição em dívida ativa');
});

it('mounts lazily on the portfolio company page and 403/404s outside an organization', function () {
    $org = Organization::factory()->create();
    $company = MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => '11222333000181']);

    Livewire::actingAs(User::factory()->for($org)->create())->test(Show::class, ['company' => $company])
        ->assertSeeLivewire(IndiciosEsvaziamento::class);

    Livewire::actingAs(User::factory()->create(['organization_id' => null]))->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181'])->assertForbidden();
    Livewire::actingAs(User::factory()->for($org)->create())->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000182'])->assertNotFound();
});

it('never says "nothing found" when a block did not answer', function () {
    // Base fora do ar + data informada: tudo indisponível → "consulta parcial".
    Livewire::actingAs(esvUser())->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181'])
        ->set('data', '2020-03-01')->call('aplicarData')
        ->assertSee('Consulta parcial')
        ->assertSee('a consulta não foi completa')
        ->assertDontSee('Nada encontrado');

    // Empresa limpa, mas o quadro societário não pôde ser lido.
    esvBase();
    esvEmpresa('11222333', 'PADARIA AURORA LTDA', ['data_inicio_atividade' => '2012-01-01']);
    esvPgfn('2020-03-01', 1000);
    Schema::connection('cnpj')->drop('socios');

    $r = app(IndiciosService::class)->para('11222333000181');
    expect($r->indicios())->toBe([])->and($r->nivel())->toBe('parcial');
});

it('finds the companies of a corporate partner by its CNPJ root', function () {
    esvBase();
    esvEmpresa('11222333', 'PADARIA AURORA LTDA', ['data_inicio_atividade' => '2012-01-01', 'cnae_fiscal_principal' => '1091102']);
    esvPgfn('2020-03-01', 1000);
    esvSocio('11222333', 'HOLDING AURORA SA', '55555555', '2012-01-01', identificador: '1', qualificacao: '22');
    esvEmpresa('56565656', 'NOVA AURORA LTDA', ['cnae_fiscal_principal' => '1091102', 'data_inicio_atividade' => '2022-01-01', 'cep' => '04000000', 'numero' => '99']);
    esvSocio('56565656', 'HOLDING AURORA SA', '55555555', '2022-01-01', identificador: '1', qualificacao: '22');

    $r = app(IndiciosService::class)->para('11222333000181');

    expect($r->sucessoras[0]->nome ?? null)->toBe('NOVA AURORA LTDA')
        ->and($r->sucessoras[0]->sinais)->toBe(['cnae', 'socio'])
        ->and($r->sucessoras[0]->sociosEmComum)->toBe(['HOLDING AURORA SA']);
});

it('catches a NEW branch of an old company at the debtor address (business transfer)', function () {
    esvBase();
    esvEmpresa('11222333', 'PADARIA AURORA LTDA', ['data_inicio_atividade' => '2012-01-01', 'cnae_fiscal_principal' => '1091102', 'cep' => '01001000', 'numero' => '10', 'ddd_1' => '11', 'telefone_1' => '33334444']);
    esvPgfn('2020-03-01', 1000);
    // Empresa de 2005 (matriz noutro lugar, outro ramo) abre em 2023 uma filial no endereço, no ramo e com o telefone da devedora.
    esvEmpresa('70707070', 'REDE ANTIGA LTDA', ['data_inicio_atividade' => '2005-01-01', 'cnae_fiscal_principal' => '4711302']);
    DB::connection('cnpj')->table('estabelecimentos')->insert([
        'cnpj_basico' => '70707070', 'cnpj_ordem' => '0002', 'cnpj_dv' => '00', 'situacao_cadastral' => '02', 'data_inicio_atividade' => '2023-04-01',
        'cnae_fiscal_principal' => '1091102', 'cep' => '01001000', 'numero' => '10', 'ddd_1' => '11', 'telefone_1' => '33334444',
    ]);

    $r = app(IndiciosService::class)->para('11222333000181');

    expect($r->sucessoras[0]->nome ?? null)->toBe('REDE ANTIGA LTDA')
        ->and($r->sucessoras[0]->sinais)->toBe(['endereco', 'cnae', 'contato'])
        ->and($r->sucessoras[0]->forte())->toBeTrue()
        ->and($r->sucessoras[0]->filialNoEndereco)->toBeTrue()
        ->and($r->sucessoras[0]->inicio)->toBe('2023-04-01');

    Livewire::actingAs(esvUser())->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181'])
        ->assertSee('filial no endereço aberta em 01/04/2023');
});

it('caps the list at 20, ranks ties by the latest event and says "mostrando N de M"', function () {
    esvBase();
    esvEmpresa('11222333', 'PADARIA AURORA LTDA', ['data_inicio_atividade' => '2012-01-01', 'cnae_fiscal_principal' => '1091102']);
    esvPgfn('2020-03-01', 1000);
    esvSocio('11222333', 'MARIA SOUZA', '***456789**', '2012-01-01');
    for ($i = 1; $i <= 20; $i++) { // 20 empresas da sócia, mesmo ramo, abertas em 2021
        esvEmpresa(sprintf('6%07d', $i), sprintf('PAES %02d LTDA', $i), ['cnae_fiscal_principal' => '1091102', 'data_inicio_atividade' => '2021-01-01']);
        esvSocio(sprintf('6%07d', $i), 'MARIA SOUZA', '***456789**', '2021-01-01');
    }
    // Aberta em 2001, mas a sócia entrou em 2026: é o fato mais recente → primeiro no empate.
    esvEmpresa('69999999', 'ANTIGA DO RAMO LTDA', ['cnae_fiscal_principal' => '1091102', 'data_inicio_atividade' => '2001-01-01']);
    esvSocio('69999999', 'MARIA SOUZA', '***456789**', '2026-02-01');

    $r = app(IndiciosService::class)->para('11222333000181');

    expect($r->totalSucessoras)->toBe(21)
        ->and($r->sucessoras)->toHaveCount(IndiciosService::SUCESSORAS)
        ->and($r->sucessoras[0]->nome)->toBe('ANTIGA DO RAMO LTDA')
        ->and($r->sucessoras[0]->quando())->toBe('2026-02-01');

    Livewire::actingAs(esvUser())->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181'])
        ->assertSee('sócio entrou em 01/02/2026')
        ->assertSee('Mostrando 20 de 21');
});

it('flags a partial read when the address or the participations hit the read ceiling', function () {
    esvBase();
    esvEmpresa('11222333', 'PADARIA AURORA LTDA', ['data_inicio_atividade' => '2012-01-01', 'cep' => '01001000', 'numero' => '10']);
    esvPgfn('2020-03-01', 1000);
    $linhas = array_map(fn (int $i): array => ['cnpj_basico' => sprintf('8%07d', $i), 'cnpj_ordem' => '0001', 'cnpj_dv' => '00', 'cep' => '01001000', 'numero' => '10', 'data_inicio_atividade' => '2022-01-01'], range(1, 400));
    foreach (array_chunk($linhas, 100) as $lote) {
        DB::connection('cnpj')->table('estabelecimentos')->insert($lote);
    }

    $r = app(IndiciosService::class)->para('11222333000181');
    expect($r->parcial)->toBeTrue()->and($r->indicios())->toBe([])->and($r->nivel())->toBe('parcial');

    Livewire::actingAs(esvUser())->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181'])
        ->assertSee('Leitura parcial')
        ->assertSee('Consulta parcial')
        ->assertDontSee('Nada encontrado');
});

it('lists at most 10 new participations and flags a cut participation read', function () {
    esvBase();
    esvEmpresa('11222333', 'PADARIA AURORA LTDA', ['data_inicio_atividade' => '2012-01-01']);
    esvPgfn('2020-03-01', 1000);
    $socios = array_map(fn (int $i): array => [
        'cnpj_basico' => sprintf('9%07d', $i), 'nome_socio' => 'PADARIA AURORA LTDA', 'cnpj_cpf_do_socio' => '11222333',
        'identificador_de_socio' => '1', 'qualificacao_do_socio' => '22', 'data_entrada_sociedade' => $i <= 12 ? '2022-01-01' : '2015-01-01',
    ], range(1, 400));
    foreach (array_chunk($socios, 100) as $lote) {
        DB::connection('cnpj')->table('socios')->insert($lote);
    }

    $r = app(IndiciosService::class)->para('11222333000181');
    expect($r->participacoes)->toHaveCount(12)->and($r->parcial)->toBeTrue();

    Livewire::actingAs(esvUser())->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181'])
        ->assertSee('Mostrando 10 de 12');
});

it('limits how many debt dates a user can apply per minute', function () {
    esvCenario();
    $card = Livewire::actingAs(esvUser())->test(IndiciosEsvaziamento::class, ['cnpj' => '11222333000181']);

    for ($i = 1; $i <= 10; $i++) {
        $card->set('data', sprintf('2019-01-%02d', $i))->call('aplicarData')->assertHasNoErrors();
    }

    $card->set('data', '2019-02-01')->call('aplicarData')->assertHasErrors('data')->assertSee('Muitas datas seguidas');
    expect($card->get('marcoInformado'))->toBe('2019-01-10');
});

it('reports a failed successor scan WITHOUT the SQL (its bindings carry masked CPFs)', function () {
    Exceptions::fake();
    esvCenario();
    Schema::connection('cnpj')->drop('empresas'); // a varredura quebra ao buscar as matrizes das candidatas

    $r = app(IndiciosService::class)->para('11222333000181');

    expect($r->sucessoras)->toBeNull();
    Exceptions::assertReported(fn (RuntimeException $e): bool => str_contains($e->getMessage(), 'SQL omitido'));
    Exceptions::assertNotReported(fn (Throwable $e): bool => str_contains($e->getMessage(), '***'));
});

it('counts every room when deciding the building is crowded, and keeps the newest unit of the same room', function () {
    esvBase();
    esvEmpresa('11222333', 'PADARIA AURORA LTDA', ['data_inicio_atividade' => '2012-01-01', 'cnae_fiscal_principal' => '1091102', 'cep' => '01001000', 'numero' => '10', 'complemento' => 'SALA 1', 'ddd_1' => '11', 'telefone_1' => '33334444']);
    esvPgfn('2020-03-01', 1000);
    for ($i = 2; $i <= 16; $i++) { // 15 empresas em outras salas: o prédio é lotado
        esvEmpresa(sprintf('4%07d', $i), "ESCRITORIO {$i} LTDA", ['cep' => '01001000', 'numero' => '10', 'complemento' => "SALA {$i}", 'data_inicio_atividade' => '2015-01-01']);
    }
    // Sem sala, mesmo ramo, aberta depois: no prédio lotado, endereço sem sala não conta.
    esvEmpresa('43434343', 'SEM SALA PAES LTDA', ['cep' => '01001000', 'numero' => '10', 'cnae_fiscal_principal' => '1091102', 'data_inicio_atividade' => '2022-01-01']);
    // Mesma sala: unidade nova de 2023 (ramo e telefone da devedora) e uma antiga de 2011 lida DEPOIS.
    esvEmpresa('47474747', 'REDE NA SALA LTDA', ['data_inicio_atividade' => '2000-01-01', 'cnae_fiscal_principal' => '4711302']);
    $sala1 = ['cnpj_basico' => '47474747', 'cnpj_dv' => '00', 'situacao_cadastral' => '02', 'cep' => '01001000', 'numero' => '10', 'complemento' => 'SALA 1'];
    DB::connection('cnpj')->table('estabelecimentos')->insert($sala1 + ['cnpj_ordem' => '0002', 'data_inicio_atividade' => '2023-04-01', 'cnae_fiscal_principal' => '1091102', 'ddd_1' => '11', 'telefone_1' => '33334444']);
    DB::connection('cnpj')->table('estabelecimentos')->insert($sala1 + ['cnpj_ordem' => '0003', 'data_inicio_atividade' => '2011-01-01', 'cnae_fiscal_principal' => '4711302']);

    $r = app(IndiciosService::class)->para('11222333000181');

    expect(array_map(fn (PossivelSucessora $s) => $s->nome, $r->sucessoras ?? []))->toBe(['REDE NA SALA LTDA'])
        ->and($r->sucessoras[0]->sinais)->toBe(['endereco', 'cnae', 'contato'])
        ->and($r->sucessoras[0]->inicio)->toBe('2023-04-01');
});

it('keeps the earliest entry of a partner with two rows in the same company (order-independent)', function () {
    esvBase();
    esvEmpresa('11222333', 'PADARIA AURORA LTDA', ['data_inicio_atividade' => '2012-01-01', 'cnae_fiscal_principal' => '1091102']);
    esvPgfn('2020-03-01', 1000);
    esvSocio('11222333', 'MARIA SOUZA', '***456789**', '2012-01-01');
    esvEmpresa('34343434', 'ANTIGA DA MARIA LTDA', ['cnae_fiscal_principal' => '1091102', 'data_inicio_atividade' => '2010-01-01']);
    esvSocio('34343434', 'MARIA SOUZA', '***456789**', '2024-01-01'); // requalificação recente
    esvSocio('34343434', 'MARIA SOUZA', '***456789**', '2015-01-01'); // entrou de fato em 2015, antes da dívida

    expect(app(IndiciosService::class)->para('11222333000181')->totalSucessoras)->toBe(0);
});

it('flags a cut board read instead of claiming the whole board was replaced', function () {
    esvBase();
    esvEmpresa('11222333', 'PADARIA AURORA LTDA', ['data_inicio_atividade' => '2012-01-01']);
    esvPgfn('2020-03-01', 1000);
    $socios = array_map(fn (int $i): array => [
        'cnpj_basico' => '11222333', 'nome_socio' => sprintf('SOCIO %03d', $i), 'cnpj_cpf_do_socio' => sprintf('***%06d**', $i),
        'identificador_de_socio' => '2', 'qualificacao_do_socio' => '22', 'data_entrada_sociedade' => '2023-01-01',
    ], range(1, 400));
    foreach (array_chunk($socios, 100) as $lote) {
        DB::connection('cnpj')->table('socios')->insert($lote);
    }

    expect(app(IndiciosService::class)->para('11222333000181')->parcial)->toBeTrue();
});
