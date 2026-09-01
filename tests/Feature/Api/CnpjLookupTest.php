<?php

declare(strict_types=1);

use App\Models\ChangeEvent;
use App\Models\CompanySnapshot;
use App\Models\MonitoredCompany;
use App\Models\Organization;
use App\Models\Portfolio;
use App\Models\User;

/**
 * @return array{0: Organization, 1: string}
 */
function orgWithApiToken(): array
{
    $org = Organization::factory()->create();
    $user = User::factory()->for($org)->create();

    return [$org, $user->createToken('test')->plainTextToken];
}

/**
 * @param  array<string, mixed>|null  $snapshot  null = empresa monitorada sem coleta
 */
function monitor(Organization $org, string $cnpj, ?array $snapshot = ['razao_social' => 'COMERCIO XYZ LTDA']): MonitoredCompany
{
    $company = MonitoredCompany::factory()
        ->for(Portfolio::factory()->for($org))
        ->create(['cnpj' => $cnpj]);

    if ($snapshot !== null) {
        CompanySnapshot::factory()->create(['monitored_company_id' => $company->id, ...$snapshot]);
    }

    return $company;
}

it('returns the cadastral data and history for a monitored CNPJ in the contract shape', function () {
    [$org, $token] = orgWithApiToken();
    $company = monitor($org, '12345678000190', [
        'situacao_cadastral' => 'BAIXADA',
        'cnae_principal' => '4713004',
        'razao_social' => 'COMERCIO XYZ LTDA',
    ]);
    ChangeEvent::factory()->create([
        'monitored_company_id' => $company->id,
        'field' => 'situacao_cadastral',
        'old_value' => 'ATIVA',
        'new_value' => 'BAIXADA',
        'detected_at' => '2025-02-10 09:00:00',
    ]);

    $this->withHeaders(['Authorization' => 'Bearer '.$token])
        ->getJson('/api/cnpj/12345678000190')
        ->assertOk()
        ->assertExactJson([
            'cnpj' => '12345678000190',
            'situacao_cadastral' => 'BAIXADA',
            'cnae_principal' => '4713004',
            'razao_social' => 'COMERCIO XYZ LTDA',
            'historico' => [
                ['data' => '2025-02-10', 'campo' => 'situacao_cadastral', 'de' => 'ATIVA', 'para' => 'BAIXADA'],
            ],
        ]);
});

it('returns the most recent changes first, capped by the configured limit', function () {
    config(['cnpj.api.history_limit' => 1]);

    [$org, $token] = orgWithApiToken();
    $company = monitor($org, '12345678000190');
    ChangeEvent::factory()->create([
        'monitored_company_id' => $company->id,
        'field' => 'cnae_principal',
        'detected_at' => '2025-01-01 10:00:00',
    ]);
    ChangeEvent::factory()->create([
        'monitored_company_id' => $company->id,
        'field' => 'situacao_cadastral',
        'detected_at' => '2025-03-20 10:00:00', // mais recente
    ]);

    $this->withHeaders(['Authorization' => 'Bearer '.$token])
        ->getJson('/api/cnpj/12345678000190')
        ->assertOk()
        ->assertJsonCount(1, 'historico')
        ->assertJsonPath('historico.0.campo', 'situacao_cadastral')
        ->assertJsonPath('historico.0.data', '2025-03-20');
});

it('rejects a request without a token', function () {
    $this->getJson('/api/cnpj/12345678000190')->assertUnauthorized();
});

it('rejects a request with an invalid token', function () {
    $this->withHeaders(['Authorization' => 'Bearer nope-not-real'])
        ->getJson('/api/cnpj/12345678000190')
        ->assertUnauthorized();
});

it('returns 404 (pending) for a CNPJ the organization does not monitor', function () {
    [, $token] = orgWithApiToken();

    $this->withHeaders(['Authorization' => 'Bearer '.$token])
        ->getJson('/api/cnpj/12345678000190')
        ->assertNotFound();
});

it('returns 404 (pending) for a monitored CNPJ with no snapshot yet', function () {
    [$org, $token] = orgWithApiToken();
    monitor($org, '12345678000190', null);

    $this->withHeaders(['Authorization' => 'Bearer '.$token])
        ->getJson('/api/cnpj/12345678000190')
        ->assertNotFound();
});

it('returns 422 for a malformed CNPJ', function () {
    [, $token] = orgWithApiToken();

    $this->withHeaders(['Authorization' => 'Bearer '.$token])
        ->getJson('/api/cnpj/123')
        ->assertStatus(422);
});

it('does not leak a CNPJ monitored by another organization', function () {
    [, $tokenA] = orgWithApiToken();
    $orgB = Organization::factory()->create();
    monitor($orgB, '12345678000190', ['razao_social' => 'EMPRESA DA ORG B']);

    $this->withHeaders(['Authorization' => 'Bearer '.$tokenA])
        ->getJson('/api/cnpj/12345678000190')
        ->assertNotFound();
});
