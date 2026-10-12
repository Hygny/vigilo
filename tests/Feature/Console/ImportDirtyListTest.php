<?php

declare(strict_types=1);

use App\Models\DirtyListEmployer;
use App\Services\Risco\DirtyListLookup;

function dirtyListCsv(string $contents): string
{
    $path = sys_get_temp_dir().'/listasuja_'.uniqid().'.csv';
    file_put_contents($path, $contents);

    return $path;
}

it('imports the dirty list from a CSV, normalizing the document to digits', function () {
    $csv = dirtyListCsv(
        "Empregador;CNPJ/CPF;Estabelecimento;UF;Ano\n".
        "EMPRESA RUIM LTDA;11.222.333/0001-81;Fazenda X;PA;2022\n".
        "OUTRA EMPRESA;99.888.777/0001-66;Obra Y;SP;2021\n"
    );

    $this->artisan('vigilo:importar-lista-suja', ['path' => $csv])->assertSuccessful();

    expect(DirtyListEmployer::query()->count())->toBe(2);

    $entry = DirtyListEmployer::query()->where('documento', '11222333000181')->first();

    expect($entry)->not->toBeNull()
        ->and($entry->nome)->toBe('EMPRESA RUIM LTDA')
        ->and($entry->uf)->toBe('PA')
        ->and($entry->ano)->toBe('2022');

    @unlink($csv);
});

it('is a full reload: re-importing replaces the whole list', function () {
    $first = dirtyListCsv("Empregador;CNPJ/CPF\nA;11.222.333/0001-81\nB;99.888.777/0001-66\n");
    $this->artisan('vigilo:importar-lista-suja', ['path' => $first])->assertSuccessful();
    expect(DirtyListEmployer::query()->count())->toBe(2);

    $second = dirtyListCsv("Empregador;CNPJ/CPF\nC;44.555.666/0001-00\n");
    $this->artisan('vigilo:importar-lista-suja', ['path' => $second])->assertSuccessful();

    expect(DirtyListEmployer::query()->count())->toBe(1)
        ->and(DirtyListEmployer::query()->where('documento', '44555666000100')->exists())->toBeTrue();

    @unlink($first);
    @unlink($second);
});

it('aborts without touching the list when the file has no valid document', function () {
    DirtyListEmployer::query()->create(['documento' => '11222333000181', 'imported_at' => now()]); // lista boa
    $csv = dirtyListCsv("Empregador;CNPJ/CPF\n"); // só cabeçalho

    $this->artisan('vigilo:importar-lista-suja', ['path' => $csv])->assertFailed();

    expect(DirtyListEmployer::query()->count())->toBe(1); // lista preservada

    @unlink($csv);
});

it('aborts on a wrong delimiter instead of wiping the list', function () {
    DirtyListEmployer::query()->create(['documento' => '11222333000181', 'imported_at' => now()]);
    // Arquivo com vírgula; o comando usa ";" por padrão → cada linha vira uma só
    // célula e os dígitos de várias colunas concatenam num "documento" inválido.
    $csv = dirtyListCsv("Empregador,CNPJ/CPF,Ano\nX,11.222.333/0001-81,2022\n");

    $this->artisan('vigilo:importar-lista-suja', ['path' => $csv])->assertFailed();

    expect(DirtyListEmployer::query()->count())->toBe(1); // preservada

    @unlink($csv);
});

it('fails when the document column is missing', function () {
    $csv = dirtyListCsv("Empregador;UF\nX;PA\n");

    $this->artisan('vigilo:importar-lista-suja', ['path' => $csv])->assertFailed();

    @unlink($csv);
});

it('fails when the file does not exist', function () {
    $this->artisan('vigilo:importar-lista-suja', ['path' => sys_get_temp_dir().'/nope_'.uniqid().'.csv'])
        ->assertFailed();
});

it('finds a company on the list by CNPJ, ignoring formatting', function () {
    DirtyListEmployer::query()->create(['documento' => '11222333000181', 'imported_at' => now()]);

    $lookup = app(DirtyListLookup::class);

    expect($lookup->consta('11.222.333/0001-81'))->not->toBeNull()
        ->and($lookup->consta('00000000000000'))->toBeNull()
        ->and($lookup->consta('123'))->toBeNull(); // documento inválido
});
