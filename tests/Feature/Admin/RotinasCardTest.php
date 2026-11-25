<?php

declare(strict_types=1);

use App\DTO\Admin\RotinaStatus;
use App\Livewire\Admin\Organizations\Index;
use App\Models\MaintenanceRun;
use App\Models\User;
use App\Services\Admin\StatusRotinas;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;

/** Base CNPJ (sqlite) com o que a rotina da busca de pessoas deixa de rastro. */
function seedRastroPessoas(?string $assinaturaMontada, int $arquivos = 2): void
{
    useCnpjSqlite();
    $schema = Schema::connection('cnpj');
    $schema->create('processed_files', function (Blueprint $t): void {
        $t->string('directory');
        $t->string('filename');
        $t->string('processed_at')->nullable();
    });
    $schema->create('busca_pessoa_cargas', function (Blueprint $t): void {
        $t->integer('id')->primary();
        $t->string('assinatura');
        $t->bigInteger('pessoas');
        $t->string('montado_em');
    });

    for ($i = 1; $i <= $arquivos; $i++) {
        DB::connection('cnpj')->table('processed_files')->insert(['directory' => '2026-11', 'filename' => "Socios{$i}.zip", 'processed_at' => "2026-11-23 15:30:2{$i}"]);
    }

    if ($assinaturaMontada !== null) {
        DB::connection('cnpj')->table('busca_pessoa_cargas')->insert(['id' => 1, 'assinatura' => $assinaturaMontada, 'pessoas' => 18399469, 'montado_em' => '2026-11-25 12:02:08']);
    }
}

function rotina(string $nome): RotinaStatus
{
    return collect(app(StatusRotinas::class)->todas())->firstOrFail(fn ($r) => $r->nome === $nome);
}

it('records an external routine (backup) and rejects unknown type or result', function () {
    $this->artisan('vigilo:rotina', ['tipo' => 'backup', 'resultado' => 'ok', '--message' => 'db-2026-11-25.sql.gz (3.8M) + cópia na VM'])->assertSuccessful();
    $this->artisan('vigilo:rotina', ['tipo' => 'backup', 'resultado' => 'fail', '--message' => 'mysqldump falhou'])->assertSuccessful();

    expect(MaintenanceRun::query()->where('kind', 'backup')->pluck('status')->all())->toBe(['success', 'failed'])
        ->and(MaintenanceRun::ultima('backup')?->message)->toBe('mysqldump falhou');

    $this->artisan('vigilo:rotina', ['tipo' => 'cnpj_mensal', 'resultado' => 'ok'])->assertExitCode(2);
    $this->artisan('vigilo:rotina', ['tipo' => 'backup', 'resultado' => 'talvez'])->assertExitCode(2);
});

it('tells never / ok / failed / late for a daily routine', function () {
    expect(rotina('Backup do banco')->situacao)->toBe('nunca');

    MaintenanceRun::registrar('backup', true, 'ok');
    expect(rotina('Backup do banco')->situacao)->toBe('ok');

    $this->travel(27)->hours();
    expect(rotina('Backup do banco')->situacao)->toBe('atrasada'); // o cron parou em silêncio

    MaintenanceRun::registrar('backup', false, 'cópia na VM falhou');
    expect(rotina('Backup do banco')->situacao)->toBe('falhou')
        ->and(rotina('Backup do banco')->detalhe)->toBe('cópia na VM falhou');
});

it('records the sanctions sweep result (ok, and failed when the breaker aborts)', function () {
    config()->set('risco.portal.api_key', 'chave-teste');
    Http::fake();

    $this->artisan('vigilo:varrer-sancoes')->assertSuccessful();

    expect(MaintenanceRun::ultima('varredura_sancoes')?->isSuccess())->toBeTrue()
        ->and(MaintenanceRun::ultima('varredura_sancoes')?->message)->toContain('0 empresa(s) checada(s)');
});

it('marks the CGU copy late when the copy is too old, even after an "ok" run', function () {
    MaintenanceRun::registrar('sancoes_cgu', true, 'CEIS: arquivo de 24/11 já carregado');
    foreach (['ceis', 'cnep', 'cepim'] as $f) {
        DB::table('cgu_cargas')->insert(['fonte' => $f, 'data_arquivo' => now()->subDays(10)->toDateString(), 'registros' => 1, 'carregado_em' => now()]);
    }

    $r = rotina('Cópia das sanções da CGU');

    expect($r->situacao)->toBe('atrasada')
        ->and($r->detalhe)->toContain('Cópia: CEIS');
});

it('says whether the people index reflects the loaded dump', function () {
    seedRastroPessoas('2|2026-11-23 15:30:22');
    expect(rotina('Busca de pessoas (índice)')->situacao)->toBe('ok')
        ->and(rotina('Busca de pessoas (índice)')->detalhe)->toBe('18.399.469 pessoas — reflete o dump atual');

    seedRastroPessoas('2|2026-11-23 15:30:22', arquivos: 3); // chegou dump novo, índice ainda não remontado
    expect(rotina('Busca de pessoas (índice)')->situacao)->toBe('atrasada');

    seedRastroPessoas(null);
    expect(rotina('Busca de pessoas (índice)')->situacao)->toBe('nunca');

    useCnpjSqlite(); // sem tabelas = base fora
    expect(rotina('Busca de pessoas (índice)')->situacao)->toBe('indisponivel');
});

it('shows the routines card to the super-admin with what needs attention', function () {
    MaintenanceRun::registrar('backup', false, 'mysqldump falhou');
    MaintenanceRun::registrar('varredura_sancoes', true, '10 empresa(s) checada(s)');

    Livewire::actingAs(User::factory()->superAdmin()->create())->test(Index::class)
        ->assertSee('Rotinas')
        ->assertSee('Backup do banco')
        ->assertSee('mysqldump falhou')
        ->assertSee('Falhou')
        ->assertSee('Varredura de sanções da carteira')
        ->assertSee('Cópia das sanções da CGU')
        ->assertSee('Busca de pessoas (índice)')
        ->assertSee('com atenção');
});
