<?php

declare(strict_types=1);

use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\PgfnDebt;
use App\Models\Portfolio;
use App\Services\Risco\PgfnLookup;

function pgfnCsv(string $contents): string
{
    $path = sys_get_temp_dir().'/pgfn_'.uniqid().'.csv';
    file_put_contents($path, $contents);

    return $path;
}

function pgfnMonitor(string $cnpj = '11222333000181'): void
{
    $org = Organization::factory()->create();
    MonitoredCompany::factory()->for(Portfolio::factory()->for($org))->create(['cnpj' => $cnpj]);
}

/** Cria uma pasta temporária com vários CSVs (um "por UF"). */
function pgfnDir(array $filesByName): string
{
    $dir = sys_get_temp_dir().'/pgfn_dir_'.uniqid();
    mkdir($dir);

    foreach ($filesByName as $name => $contents) {
        file_put_contents($dir.'/'.$name, $contents);
    }

    return $dir;
}

it('imports only the carteira CNPJs, parsing BR currency', function () {
    pgfnMonitor('11222333000181'); // na carteira
    $csv = pgfnCsv(
        "CPF_CNPJ;SITUACAO_INSCRICAO;RECEITA_PRINCIPAL;VALOR_CONSOLIDADO;DATA_INSCRICAO\n".
        "11.222.333/0001-81;EM COBRANCA;IRPJ;1.234,56;01/03/2023\n".
        "11.222.333/0001-81;AJUIZADA;COFINS;500,00;10/01/2024\n".
        "99.999.999/0001-99;EM COBRANCA;IRPJ;9.999,99;01/01/2020\n" // fora da carteira → descartado
    );

    $this->artisan('vigilo:importar-pgfn', ['path' => $csv, '--tipo' => 'nao-previdenciario'])->assertSuccessful();

    expect(PgfnDebt::query()->count())->toBe(2); // só as do CNPJ monitorado

    $summary = app(PgfnLookup::class)->para('11.222.333/0001-81');

    expect($summary)->not->toBeNull()
        ->and($summary->inscricoes)->toBe(2)
        ->and($summary->total)->toEqualWithDelta(1734.56, 0.001); // 1.234,56 + 500,00

    @unlink($csv);
});

it('replaces only the given tipo on re-import', function () {
    pgfnMonitor('11222333000181');

    $prev = pgfnCsv("CPF_CNPJ;VALOR_CONSOLIDADO\n11222333000181;100,00\n");
    $this->artisan('vigilo:importar-pgfn', ['path' => $prev, '--tipo' => 'previdenciario'])->assertSuccessful();

    $fgts = pgfnCsv("CPF_CNPJ;VALOR_CONSOLIDADO\n11222333000181;200,00\n");
    $this->artisan('vigilo:importar-pgfn', ['path' => $fgts, '--tipo' => 'fgts'])->assertSuccessful();

    expect(PgfnDebt::query()->count())->toBe(2); // os dois tipos coexistem

    // Reimporta previdenciário sem nenhum da carteira → limpa só esse tipo.
    $vazio = pgfnCsv("CPF_CNPJ;VALOR_CONSOLIDADO\n99999999000199;50,00\n"); // doc válido, fora da carteira
    $this->artisan('vigilo:importar-pgfn', ['path' => $vazio, '--tipo' => 'previdenciario'])->assertSuccessful();

    expect(PgfnDebt::query()->where('tipo', 'previdenciario')->count())->toBe(0)
        ->and(PgfnDebt::query()->where('tipo', 'fgts')->count())->toBe(1);

    @unlink($prev);
    @unlink($fgts);
    @unlink($vazio);
});

it('aborts without touching the table when no valid document is read', function () {
    pgfnMonitor('11222333000181');
    PgfnDebt::query()->create(['documento' => '11222333000181', 'tipo' => 'geral', 'imported_at' => now()]);

    $csv = pgfnCsv("CPF_CNPJ\n"); // só cabeçalho → nenhum documento válido
    $this->artisan('vigilo:importar-pgfn', ['path' => $csv, '--tipo' => 'geral'])->assertFailed();

    expect(PgfnDebt::query()->count())->toBe(1); // preservado

    @unlink($csv);
});

it('picks SITUACAO_INSCRICAO over the TIPO_SITUACAO_INSCRICAO decoy column', function () {
    pgfnMonitor('11222333000181');
    // A coluna "tipo" vem ANTES e também contém "situacao" — o match exato tem de vencer.
    $csv = pgfnCsv(
        "CPF_CNPJ;TIPO_SITUACAO_INSCRICAO;SITUACAO_INSCRICAO;VALOR_CONSOLIDADO\n".
        "11222333000181;PARCELADA;EM COBRANCA;100,00\n"
    );

    $this->artisan('vigilo:importar-pgfn', ['path' => $csv, '--tipo' => 'geral'])->assertSuccessful();

    expect(PgfnDebt::query()->value('situacao'))->toBe('EM COBRANCA'); // não "PARCELADA"

    @unlink($csv);
});

it('reads a plain decimal value without inflating it 100x', function () {
    pgfnMonitor('11222333000181');
    $csv = pgfnCsv("CPF_CNPJ;VALOR_CONSOLIDADO\n11222333000181;1234.56\n"); // ponto decimal, sem vírgula

    $this->artisan('vigilo:importar-pgfn', ['path' => $csv, '--tipo' => 'geral'])->assertSuccessful();

    expect(app(PgfnLookup::class)->para('11222333000181')->total)->toEqualWithDelta(1234.56, 0.001);

    @unlink($csv);
});

it('imports every CSV in a folder under a single per-tipo reload (UFs do not wipe each other)', function () {
    pgfnMonitor('11222333000181');
    // Simula o ZIP real: um CSV por UF, o mesmo CNPJ inscrito em UFs diferentes.
    $dir = pgfnDir([
        'Dados_abertos_Nao_Previdenciario_RJ.csv' => "CPF_CNPJ;VALOR_CONSOLIDADO\n11222333000181;100,00\n",
        'Dados_abertos_Nao_Previdenciario_SP.csv' => "CPF_CNPJ;VALOR_CONSOLIDADO\n11222333000181;200,00\n99999999000199;999,00\n",
    ]);

    $this->artisan('vigilo:importar-pgfn', ['path' => $dir, '--tipo' => 'nao-previdenciario'])->assertSuccessful();

    // As duas UFs do CNPJ monitorado coexistem (nenhuma apagou a outra); o de fora ficou fora.
    expect(PgfnDebt::query()->count())->toBe(2)
        ->and(app(PgfnLookup::class)->para('11222333000181')->total)->toEqualWithDelta(300.00, 0.001);

    array_map('unlink', glob($dir.'/*') ?: []);
    rmdir($dir);
});

it('aborts the whole folder import if one CSV lacks the document column (nothing deleted)', function () {
    pgfnMonitor('11222333000181');
    PgfnDebt::query()->create(['documento' => '11222333000181', 'tipo' => 'geral', 'imported_at' => now()]);

    $dir = pgfnDir([
        'ok.csv' => "CPF_CNPJ;VALOR_CONSOLIDADO\n11222333000181;100,00\n",
        'ruim.csv' => "COLUNA_ESTRANHA;OUTRA\nx;y\n", // sem coluna de documento
    ]);

    $this->artisan('vigilo:importar-pgfn', ['path' => $dir, '--tipo' => 'geral'])->assertFailed();

    expect(PgfnDebt::query()->count())->toBe(1); // dado bom preservado — nada foi apagado

    array_map('unlink', glob($dir.'/*') ?: []);
    rmdir($dir);
});

it('clears the tipo (not abort) for a validated empty file when --allow-empty is set', function () {
    pgfnMonitor('11222333000181');
    // Dívida antiga desse tipo que precisa sumir se a carteira não tem mais nada nele.
    PgfnDebt::query()->create(['documento' => '11222333000181', 'tipo' => 'fgts', 'imported_at' => now()]);
    PgfnDebt::query()->create(['documento' => '11222333000181', 'tipo' => 'geral', 'imported_at' => now()]);

    // Arquivo "válido" mas sem nenhuma inscrição da carteira (só cabeçalho) —
    // é o que o pgfn_filter.py produz quando a carteira não tem dívida no tipo.
    $csv = pgfnCsv("CPF_CNPJ;VALOR_CONSOLIDADO\n");

    $this->artisan('vigilo:importar-pgfn', ['path' => $csv, '--tipo' => 'fgts', '--allow-empty' => true])->assertSuccessful();

    expect(PgfnDebt::query()->where('tipo', 'fgts')->count())->toBe(0)   // limpo
        ->and(PgfnDebt::query()->where('tipo', 'geral')->count())->toBe(1); // outro tipo intacto

    @unlink($csv);
});

it('converts Latin-1 text (the PGFN encoding) to valid UTF-8', function () {
    pgfnMonitor('11222333000181');
    // "dívida"/"Contribuições" com bytes Latin-1 crus (0xED, 0xE7/0xF5) — o que o
    // dump real traz e o MySQL utf8mb4 rejeita se não for convertido.
    $csv = pgfnCsv(
        "CPF_CNPJ;RECEITA_PRINCIPAL;VALOR_CONSOLIDADO\n".
        "11222333000181;Receita da d\xEDvida ativa - Contribui\xE7\xF5es;100,00\n"
    );

    $this->artisan('vigilo:importar-pgfn', ['path' => $csv, '--tipo' => 'geral'])->assertSuccessful();

    $receita = PgfnDebt::query()->value('receita');
    expect($receita)->toBe('Receita da dívida ativa - Contribuições')
        ->and(mb_check_encoding((string) $receita, 'UTF-8'))->toBeTrue();

    @unlink($csv);
});

it('--merge replaces only the documents present, keeping the rest of the tipo', function () {
    pgfnMonitor('11222333000181');
    pgfnMonitor('44555666000199');
    // Dívida antiga de 2 CNPJs no mesmo tipo.
    PgfnDebt::query()->create(['documento' => '11222333000181', 'tipo' => 'geral', 'valor' => '100.00', 'imported_at' => now()]);
    PgfnDebt::query()->create(['documento' => '44555666000199', 'tipo' => 'geral', 'valor' => '200.00', 'imported_at' => now()]);

    // Merge só do primeiro CNPJ, com valor novo — não pode tocar no segundo.
    $csv = pgfnCsv("CPF_CNPJ;VALOR_CONSOLIDADO\n11222333000181;999,00\n");
    $this->artisan('vigilo:importar-pgfn', ['path' => $csv, '--tipo' => 'geral', '--merge' => true, '--allow-empty' => true])->assertSuccessful();

    expect(app(PgfnLookup::class)->para('11222333000181')->total)->toEqualWithDelta(999.0, 0.001) // substituído
        ->and(app(PgfnLookup::class)->para('44555666000199')->total)->toEqualWithDelta(200.0, 0.001); // intacto

    @unlink($csv);
});

it('fails when the path does not exist', function () {
    $this->artisan('vigilo:importar-pgfn', ['path' => sys_get_temp_dir().'/nope_'.uniqid().'.csv'])->assertFailed();
});
