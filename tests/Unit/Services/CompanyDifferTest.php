<?php

declare(strict_types=1);

use App\DTO\ChangeEventData;
use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Models\CompanyPartner;
use App\Models\CompanySnapshot;
use App\Services\CompanyDiffer;
use Illuminate\Support\Collection;

/**
 * Build an in-memory snapshot (no DB) with an optional partners relation.
 *
 * @param  array<string, mixed>  $overrides
 * @param  list<array<string, mixed>>  $partners
 */
function snap(array $overrides = [], array $partners = []): CompanySnapshot
{
    $snapshot = new CompanySnapshot(array_merge([
        'razao_social' => 'EMPRESA EXEMPLO LTDA',
        'nome_fantasia' => 'EXEMPLO',
        'situacao_cadastral' => 'ATIVA',
        'cnae_principal' => '6201501',
        'porte' => 'DEMAIS',
        'logradouro' => 'RUA A, 100',
        'municipio' => 'SAO PAULO',
        'uf' => 'SP',
    ], $overrides));

    $snapshot->setRelation('partners', new Collection(array_map(
        fn (array $attributes): CompanyPartner => new CompanyPartner($attributes),
        $partners,
    )));

    return $snapshot;
}

function diffOf(?CompanySnapshot $previous, CompanySnapshot $current): array
{
    return (new CompanyDiffer)->diff($previous, $current);
}

it('returns no events for the first snapshot (baseline)', function () {
    expect(diffOf(null, snap()))->toBe([]);
});

it('returns no events when nothing changed', function () {
    expect(diffOf(snap(), snap()))->toBe([]);
});

it('flags a degraded cadastral status as critical', function (string $situacao) {
    $events = diffOf(snap(), snap(['situacao_cadastral' => $situacao]));

    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(ChangeEventData::class)
        ->and($events[0]->type)->toBe(ChangeType::SituacaoChanged)
        ->and($events[0]->field)->toBe('situacao_cadastral')
        ->and($events[0]->oldValue)->toBe('ATIVA')
        ->and($events[0]->newValue)->toBe($situacao)
        ->and($events[0]->severity)->toBe(Severity::Critical);
})->with(['BAIXADA', 'INAPTA', 'SUSPENSA', 'NULA']);

it('flags a status change back to a healthy state as high (not critical)', function () {
    $events = diffOf(snap(['situacao_cadastral' => 'SUSPENSA']), snap(['situacao_cadastral' => 'ATIVA']));

    expect($events)->toHaveCount(1)
        ->and($events[0]->type)->toBe(ChangeType::SituacaoChanged)
        ->and($events[0]->severity)->toBe(Severity::High);
});

it('detects an added partner as high severity', function () {
    $previous = snap([], [
        ['nome' => 'FULANO', 'documento' => '11111111111', 'qualificacao' => 'Sócio'],
    ]);
    $current = snap([], [
        ['nome' => 'FULANO', 'documento' => '11111111111', 'qualificacao' => 'Sócio'],
        ['nome' => 'BELTRANO', 'documento' => '22222222222', 'qualificacao' => 'Sócio'],
    ]);

    $events = diffOf($previous, $current);

    expect($events)->toHaveCount(1)
        ->and($events[0]->type)->toBe(ChangeType::PartnerAdded)
        ->and($events[0]->field)->toBe('socio')
        ->and($events[0]->oldValue)->toBeNull()
        ->and($events[0]->newValue)->toBe('BELTRANO')
        ->and($events[0]->severity)->toBe(Severity::High);
});

it('detects a removed partner as high severity', function () {
    $previous = snap([], [
        ['nome' => 'FULANO', 'documento' => '11111111111'],
        ['nome' => 'BELTRANO', 'documento' => '22222222222'],
    ]);
    $current = snap([], [
        ['nome' => 'FULANO', 'documento' => '11111111111'],
    ]);

    $events = diffOf($previous, $current);

    expect($events)->toHaveCount(1)
        ->and($events[0]->type)->toBe(ChangeType::PartnerRemoved)
        ->and($events[0]->oldValue)->toBe('BELTRANO')
        ->and($events[0]->newValue)->toBeNull()
        ->and($events[0]->severity)->toBe(Severity::High);
});

it('does not flag a partner whose name changed but document stayed the same', function () {
    // Same person (same documento) — a name correction is not a partner change.
    $previous = snap([], [['nome' => 'FULANO DE TAL', 'documento' => '11111111111']]);
    $current = snap([], [['nome' => 'FULANO DE TAL SILVA', 'documento' => '11111111111']]);

    expect(diffOf($previous, $current))->toBe([]);
});

it('keys partners without a document by name', function () {
    $previous = snap([], [['nome' => 'FULANO', 'documento' => null]]);
    $current = snap([], [['nome' => 'BELTRANO', 'documento' => null]]);

    $events = diffOf($previous, $current);

    $types = array_map(fn (ChangeEventData $e): ChangeType => $e->type, $events);

    expect($events)->toHaveCount(2)
        ->and($types)->toContain(ChangeType::PartnerAdded)
        ->and($types)->toContain(ChangeType::PartnerRemoved);
});

it('detects address changes as medium severity', function (string $field, string $newValue) {
    $events = diffOf(snap(), snap([$field => $newValue]));

    expect($events)->toHaveCount(1)
        ->and($events[0]->type)->toBe(ChangeType::AddressChanged)
        ->and($events[0]->field)->toBe($field)
        ->and($events[0]->newValue)->toBe($newValue)
        ->and($events[0]->severity)->toBe(Severity::Medium);
})->with([
    'municipio' => ['municipio', 'RIO DE JANEIRO'],
    'uf' => ['uf', 'RJ'],
    'logradouro' => ['logradouro', 'AVENIDA B, 200'],
]);

it('detects a CNAE change as medium severity', function () {
    $events = diffOf(snap(), snap(['cnae_principal' => '4751201']));

    expect($events)->toHaveCount(1)
        ->and($events[0]->type)->toBe(ChangeType::CnaeChanged)
        ->and($events[0]->field)->toBe('cnae_principal')
        ->and($events[0]->severity)->toBe(Severity::Medium);
});

it('detects a porte change as medium severity', function () {
    $events = diffOf(snap(['porte' => 'ME']), snap(['porte' => 'DEMAIS']));

    expect($events)->toHaveCount(1)
        ->and($events[0]->type)->toBe(ChangeType::PorteChanged)
        ->and($events[0]->severity)->toBe(Severity::Medium);
});

it('detects name changes as low severity', function (string $field, string $newValue) {
    $events = diffOf(snap(), snap([$field => $newValue]));

    expect($events)->toHaveCount(1)
        ->and($events[0]->type)->toBe(ChangeType::NameChanged)
        ->and($events[0]->field)->toBe($field)
        ->and($events[0]->severity)->toBe(Severity::Low);
})->with([
    'razao_social' => ['razao_social', 'EMPRESA NOVA LTDA'],
    'nome_fantasia' => ['nome_fantasia', 'NOVA MARCA'],
]);

it('treats null and empty string as equal (no spurious event)', function () {
    expect(diffOf(snap(['nome_fantasia' => '']), snap(['nome_fantasia' => null])))->toBe([]);
});

it('reports every change when many fields move at once', function () {
    $previous = snap(['situacao_cadastral' => 'ATIVA', 'cnae_principal' => '6201501'], [
        ['nome' => 'FULANO', 'documento' => '11111111111'],
    ]);
    $current = snap(['situacao_cadastral' => 'BAIXADA', 'cnae_principal' => '4751201'], [
        ['nome' => 'FULANO', 'documento' => '11111111111'],
        ['nome' => 'BELTRANO', 'documento' => '22222222222'],
    ]);

    $events = collect(diffOf($previous, $current));

    expect($events)->toHaveCount(3)
        ->and($events->firstWhere('type', ChangeType::SituacaoChanged)?->severity)->toBe(Severity::Critical)
        ->and($events->firstWhere('type', ChangeType::PartnerAdded)?->severity)->toBe(Severity::High)
        ->and($events->firstWhere('type', ChangeType::CnaeChanged)?->severity)->toBe(Severity::Medium);
});
