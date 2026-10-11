<?php

declare(strict_types=1);

use App\DTO\Risco\RiscoReport;
use App\DTO\Risco\Sancao;
use App\Enums\FonteSancao;
use App\Enums\NivelRisco;

function sancaoComFim(?string $dataFim): Sancao
{
    return new Sancao(FonteSancao::Ceis, 'Tipo', 'Órgão', '2020-01-01', $dataFim, null);
}

it('classifies high risk when a sanction is active', function () {
    expect(NivelRisco::avaliar(sancaoAtiva: true, algumaSancao: true, situacaoNegativa: false))
        ->toBe(NivelRisco::Alto);
});

it('classifies medium risk for an expired sanction or a negative status', function () {
    expect(NivelRisco::avaliar(sancaoAtiva: false, algumaSancao: true, situacaoNegativa: false))->toBe(NivelRisco::Medio)
        ->and(NivelRisco::avaliar(sancaoAtiva: false, algumaSancao: false, situacaoNegativa: true))->toBe(NivelRisco::Medio);
});

it('classifies low risk when active and clean', function () {
    expect(NivelRisco::avaliar(sancaoAtiva: false, algumaSancao: false, situacaoNegativa: false))
        ->toBe(NivelRisco::Baixo);
});

it('treats a sanction without an end date (or future end) as active', function () {
    expect(RiscoReport::ok('11222333000181', [sancaoComFim(null)])->temSancaoAtiva())->toBeTrue()
        ->and(RiscoReport::ok('11222333000181', [sancaoComFim('2999-12-31')])->temSancaoAtiva())->toBeTrue();
});

it('treats an expired sanction as not active (but still counts as a sanction)', function () {
    $report = RiscoReport::ok('11222333000181', [sancaoComFim('2000-01-01')]);

    expect($report->temSancaoAtiva())->toBeFalse()
        ->and($report->temSancoes())->toBeTrue();
});
