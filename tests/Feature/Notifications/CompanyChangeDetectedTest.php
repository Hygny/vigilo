<?php

declare(strict_types=1);

use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Models\ChangeEvent;
use App\Models\MonitoredCompany;
use App\Models\User;
use App\Notifications\CompanyChangeDetected;
use Illuminate\Database\Eloquent\Collection;

function changeEvents(): Collection
{
    return new Collection([
        new ChangeEvent([
            'type' => ChangeType::SituacaoChanged,
            'field' => 'situacao_cadastral',
            'old_value' => 'ATIVA',
            'new_value' => 'BAIXADA',
            'severity' => Severity::Critical,
        ]),
        new ChangeEvent([
            'type' => ChangeType::CnaeChanged,
            'field' => 'cnae_principal',
            'old_value' => '6201501',
            'new_value' => '4751201',
            'severity' => Severity::Medium,
        ]),
    ]);
}

it('builds the in-app (database) payload with the highest severity', function () {
    $company = MonitoredCompany::factory()->create(['cnpj' => '11222333000181', 'label' => 'Cliente X']);

    $notification = new CompanyChangeDetected($company, 'EMPRESA EXEMPLO LTDA', changeEvents());
    $data = $notification->toArray(new User);

    expect($data['changes_count'])->toBe(2)
        ->and($data['highest_severity'])->toBe('critical')
        ->and($data['cnpj'])->toBe('11222333000181')
        ->and($data['razao_social'])->toBe('EMPRESA EXEMPLO LTDA')
        ->and($data['label'])->toBe('Cliente X')
        ->and($data['changes'])->toHaveCount(2)
        ->and($data['changes'][0]['type'])->toBe('situacao_changed')
        ->and($data['changes'][0]['severity'])->toBe('critical');
});

it('builds a mail message with a subject, lines and an action link', function () {
    $company = MonitoredCompany::factory()->create(['cnpj' => '11222333000181']);

    $notification = new CompanyChangeDetected($company, 'EMPRESA EXEMPLO LTDA', changeEvents());
    $mail = $notification->toMail(new User);

    expect($mail->subject)->toContain('EMPRESA EXEMPLO LTDA')
        ->and($mail->introLines)->toHaveCount(3) // summary + 2 change lines
        ->and($mail->actionText)->toBe('Ver empresa')
        ->and($mail->actionUrl)->toContain("/companies/{$company->id}");
});
