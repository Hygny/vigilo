<?php

declare(strict_types=1);

use App\Enums\ChangeType;
use App\Enums\Severity;
use App\Models\ChangeEvent;

it('orders severities by weight (critical > high > medium > low)', function () {
    expect(Severity::Critical->weight())->toBeGreaterThan(Severity::High->weight())
        ->and(Severity::High->weight())->toBeGreaterThan(Severity::Medium->weight())
        ->and(Severity::Medium->weight())->toBeGreaterThan(Severity::Low->weight());
});

it('gives every severity a non-empty label and badge classes', function (Severity $severity) {
    expect($severity->label())->not->toBe('')
        ->and($severity->badgeClasses())->not->toBe('');
})->with(Severity::cases());

it('gives every change type a non-empty label', function (ChangeType $type) {
    expect($type->label())->not->toBe('');
})->with(ChangeType::cases());

it('reflects acknowledgement state on a change event', function () {
    expect((new ChangeEvent(['acknowledged_at' => now()]))->isAcknowledged())->toBeTrue()
        ->and((new ChangeEvent)->isAcknowledged())->toBeFalse();
});
