<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

const CEIS_HEADER = '"CADASTRO";"CÓDIGO DA SANÇÃO";"TIPO DE PESSOA";"CPF OU CNPJ DO SANCIONADO";"NOME DO SANCIONADO";"NOME INFORMADO PELO ÓRGÃO SANCIONADOR";"RAZÃO SOCIAL - CADASTRO RECEITA";"NOME FANTASIA - CADASTRO RECEITA";"NÚMERO DO PROCESSO";"CATEGORIA DA SANÇÃO";"DATA INÍCIO SANÇÃO";"DATA FINAL SANÇÃO";"DATA PUBLICAÇÃO";"PUBLICAÇÃO";"DETALHAMENTO DO MEIO DE PUBLICAÇÃO";"DATA DO TRÂNSITO EM JULGADO";"ABRAGÊNCIA DA SANÇÃO";"ÓRGÃO SANCIONADOR";"UF ÓRGÃO SANCIONADOR";"ESFERA ÓRGÃO SANCIONADOR";"FUNDAMENTAÇÃO LEGAL";"DATA ORIGEM INFORMAÇÃO";"ORIGEM INFORMAÇÕES";"OBSERVAÇÕES"';

const CNEP_HEADER = '"CADASTRO";"CÓDIGO DA SANÇÃO";"TIPO DE PESSOA";"CPF OU CNPJ DO SANCIONADO";"NOME DO SANCIONADO";"NOME INFORMADO PELO ÓRGÃO SANCIONADOR";"RAZÃO SOCIAL - CADASTRO RECEITA";"NOME FANTASIA - CADASTRO RECEITA";"NÚMERO DO PROCESSO";"CATEGORIA DA SANÇÃO";"VALOR DA MULTA";"DATA INÍCIO SANÇÃO";"DATA FINAL SANÇÃO";"DATA PUBLICAÇÃO";"PUBLICAÇÃO";"DETALHAMENTO DO MEIO DE PUBLICAÇÃO";"DATA DO TRÂNSITO EM JULGADO";"ABRAGÊNCIA DA SANÇÃO";"ÓRGÃO SANCIONADOR";"UF ÓRGÃO SANCIONADOR";"ESFERA ÓRGÃO SANCIONADOR";"FUNDAMENTAÇÃO LEGAL";"DATA ORIGEM INFORMAÇÃO";"ORIGEM INFORMAÇÕES";"OBSERVAÇÕES"';

const CEPIM_HEADER = '"CNPJ ENTIDADE";"NOME ENTIDADE";"NÚMERO CONVÊNIO";"ÓRGÃO CONCEDENTE";"MOTIVO DO IMPEDIMENTO"';

/** Zip com UM CSV em Latin-1 (como o da CGU). Devolve os bytes. */
function cguZip(string $nomeCsv, string $header, array $linhas): string
{
    $csv = mb_convert_encoding($header."\r\n".implode("\r\n", $linhas)."\r\n", 'Windows-1252', 'UTF-8');
    $path = tempnam(sys_get_temp_dir(), 'cgutest');
    $zip = new ZipArchive;
    $zip->open($path, ZipArchive::OVERWRITE);
    $zip->addFromString($nomeCsv, $csv);
    $zip->close();
    $bytes = (string) file_get_contents($path);
    @unlink($path);

    return $bytes;
}

/** Linha CEIS: CNPJ/CPF, tipo J/F, órgão, datas dd/mm/aaaa. */
function ceisLinha(string $doc, string $tipo = 'J', string $orgao = 'Ministério da Saúde', string $inicio = '01/02/2024', string $fim = ''): string
{
    return implode(';', array_map(fn (string $v): string => '"'.$v.'"', [
        'CEIS', '123', $tipo, $doc, 'EMPRESA X', 'EMPRESA X', 'EMPRESA X LTDA', 'X', 'PROC-1', 'Impedimento/proibição de contratar',
        $inicio, $fim, '', '', '', '', '', $orgao, 'DF', 'FEDERAL', 'LEI 8666', '', '', '',
    ]));
}

/**
 * Fake do download: cada fonte publica num dia (hoje = 403, ontem = zip).
 *
 * @param  array<string, string>  $zips  fonte => bytes
 */
function fakeCguDownload(array $zips, string $dia = '20261124'): void
{
    Http::fake(function (Request $request) use ($zips, $dia) {
        foreach ($zips as $fonte => $bytes) {
            if (str_ends_with($request->url(), "/{$fonte}/{$dia}")) {
                return Http::response($bytes, 200, ['Content-Type' => 'application/x-zip-compressed']);
            }
        }

        return Http::response('<Error>AccessDenied</Error>', 403, ['Content-Type' => 'application/xml']);
    });
}

beforeEach(function (): void {
    Carbon::setTestNow(Carbon::parse('2026-11-25 12:00:00', 'America/Sao_Paulo'));
});

afterEach(function (): void {
    Carbon::setTestNow();
});

it('downloads the most recent file of each source, keeps only companies and converts Latin-1', function () {
    fakeCguDownload([
        'ceis' => cguZip('20261124_CEIS.csv', CEIS_HEADER, [
            ceisLinha('11222333000181'),
            ceisLinha('73554405868', 'F'), // pessoa física: fora
            ceisLinha('99888777000166', 'J', 'Tribunal de Contas', '10/05/2018', '10/05/2028'),
        ]),
        'cnep' => cguZip('20261124_CNEP.csv', CNEP_HEADER, [
            '"CNEP";"133076";"J";"14286903000195";"SALOC";"SALOC";"SALOC LTDA";"SALOC";"PAR-1";"Multa";"137260,07";"09/03/2022";"";"09/03/2022";"";"";"";"";"Petróleo Brasileiro S.A.";"";"FEDERAL";"LEI 12846";"";"";""',
        ]),
        'cepim' => cguZip('20261124_CEPIM.csv', CEPIM_HEADER, [
            '"04177720000188";"ASSOCIACAO DOS HOTEIS";"702842";"Ministério do Turismo";"ATRASO NA DEVOLUCAO DE RECURSOS"',
        ]),
    ]);

    $this->artisan('vigilo:sancoes-cgu')->assertSuccessful();

    expect(DB::table('cgu_sancoes')->where('fonte', 'ceis')->count())->toBe(2)
        ->and(DB::table('cgu_sancoes')->where('cnpj', '73554405868')->exists())->toBeFalse();

    $ceis = DB::table('cgu_sancoes')->where('cnpj', '99888777000166')->first();
    expect($ceis->cnpj_basico)->toBe('99888777')
        ->and($ceis->orgao)->toBe('Tribunal de Contas')
        ->and(substr((string) $ceis->data_inicio, 0, 10))->toBe('2018-05-10')
        ->and(substr((string) $ceis->data_fim, 0, 10))->toBe('2028-05-10');

    expect(DB::table('cgu_sancoes')->where('cnpj', '11222333000181')->value('orgao'))->toBe('Ministério da Saúde'); // Latin-1 → UTF-8

    $cnep = DB::table('cgu_sancoes')->where('fonte', 'cnep')->first();
    expect((float) $cnep->valor_multa)->toEqualWithDelta(137260.07, 0.001)
        ->and($cnep->orgao)->toBe('Petróleo Brasileiro S.A.');

    $cepim = DB::table('cgu_sancoes')->where('fonte', 'cepim')->first();
    expect($cepim->categoria)->toBe('ATRASO NA DEVOLUCAO DE RECURSOS')
        ->and($cepim->orgao)->toBe('Ministério do Turismo')
        ->and($cepim->data_inicio)->toBeNull();

    expect(DB::table('cgu_cargas')->pluck('registros', 'fonte')->all())->toEqual(['ceis' => 2, 'cnep' => 1, 'cepim' => 1])
        ->and(substr((string) DB::table('cgu_cargas')->where('fonte', 'ceis')->value('data_arquivo'), 0, 10))->toBe('2026-11-24');
});

it('skips a source whose most recent file is already loaded, unless forced', function () {
    $zip = cguZip('20261124_CEIS.csv', CEIS_HEADER, [ceisLinha('11222333000181')]);
    fakeCguDownload(['ceis' => $zip]);

    $this->artisan('vigilo:sancoes-cgu', ['--fonte' => ['ceis']])->assertSuccessful();
    DB::table('cgu_sancoes')->update(['orgao' => 'MARCADOR']);

    $this->artisan('vigilo:sancoes-cgu', ['--fonte' => ['ceis']])
        ->expectsOutputToContain('já carregado')
        ->assertSuccessful();
    expect(DB::table('cgu_sancoes')->value('orgao'))->toBe('MARCADOR'); // não recarregou

    $this->artisan('vigilo:sancoes-cgu', ['--fonte' => ['ceis'], '--forcar' => true])->assertSuccessful();
    expect(DB::table('cgu_sancoes')->value('orgao'))->toBe('Ministério da Saúde');
});

it('never wipes a good copy with a file without any valid company', function () {
    // Um fake só (o Http::fake acumula callbacks): o "publicado" troca entre as rodadas.
    $publicado = ['dia' => '20261123', 'zip' => cguZip('20261123_CEIS.csv', CEIS_HEADER, [ceisLinha('11222333000181')])];
    Http::fake(function (Request $request) use (&$publicado) {
        return str_ends_with($request->url(), '/ceis/'.$publicado['dia'])
            ? Http::response($publicado['zip'], 200)
            : Http::response('negado', 403);
    });
    $this->artisan('vigilo:sancoes-cgu', ['--fonte' => ['ceis']])->assertSuccessful();

    // novo arquivo só com pessoa física → falha e mantém a cópia anterior
    $publicado = ['dia' => '20261124', 'zip' => cguZip('20261124_CEIS.csv', CEIS_HEADER, [ceisLinha('73554405868', 'F')])];
    $this->artisan('vigilo:sancoes-cgu', ['--fonte' => ['ceis']])
        ->expectsOutputToContain('cópia anterior mantida')
        ->assertFailed();

    expect(DB::table('cgu_sancoes')->where('cnpj', '11222333000181')->exists())->toBeTrue()
        ->and(substr((string) DB::table('cgu_cargas')->where('fonte', 'ceis')->value('data_arquivo'), 0, 10))->toBe('2026-11-23');
});

it('fails a source with nothing published in the window, still loading the others', function () {
    config()->set('risco.cgu_download.dias', 3);
    fakeCguDownload(['cnep' => cguZip('20261124_CNEP.csv', CNEP_HEADER, [
        '"CNEP";"1";"J";"14286903000195";"S";"S";"S";"S";"P";"Multa";"10,00";"09/03/2022";"";"";"";"";"";"";"CGU";"";"FEDERAL";"";"";"";""',
    ])]);

    $this->artisan('vigilo:sancoes-cgu', ['--fonte' => ['ceis', 'cnep']])
        ->expectsOutputToContain('nenhum arquivo publicado nos últimos 3 dias')
        ->assertFailed();

    expect(DB::table('cgu_cargas')->pluck('fonte')->all())->toBe(['cnep']);
    Http::assertSentCount(3 + 2); // ceis: 3 dias; cnep: hoje (403) + ontem
});

it('loads a local file with --arquivo, taking the date from its name', function () {
    Http::fake();
    $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'20261120_CEIS.zip';
    file_put_contents($path, cguZip('20261120_CEIS.csv', CEIS_HEADER, [ceisLinha('11222333000181')]));

    try {
        $this->artisan('vigilo:sancoes-cgu', ['--fonte' => ['ceis'], '--arquivo' => $path])->assertSuccessful();
    } finally {
        @unlink($path);
    }

    expect(substr((string) DB::table('cgu_cargas')->where('fonte', 'ceis')->value('data_arquivo'), 0, 10))->toBe('2026-11-20');
    Http::assertNothingSent();

    $this->artisan('vigilo:sancoes-cgu', ['--arquivo' => $path])->expectsOutputToContain('exige exatamente uma --fonte')->assertFailed();
    $this->artisan('vigilo:sancoes-cgu', ['--fonte' => ['xyz']])->expectsOutputToContain('Fonte desconhecida')->assertFailed();
});

it('is scheduled twice a day in Brasília time', function () {
    $evento = collect(app(Schedule::class)->events())
        ->first(fn ($e) => str_contains((string) $e->command, 'vigilo:sancoes-cgu'));

    expect($evento)->not->toBeNull()
        ->and($evento->expression)->toBe('30 8,14 * * *')
        ->and($evento->timezone)->toBe('America/Sao_Paulo')
        ->and($evento->withoutOverlapping)->toBeTrue();
});
