<?php

declare(strict_types=1);

use App\Support\ScheduleCalendar;
use Carbon\CarbonImmutable;

it('matches a configured day on that day', function () {
    expect(ScheduleCalendar::matchedDay([15], 15, 31))->toBe(15)
        ->and(ScheduleCalendar::matchedDay([15], 14, 31))->toBeNull();
});

it('slides an out-of-range day to the last day of the month', function () {
    // Fevereiro tem 28 dias: dia 31 casa em 28, não em 27.
    expect(ScheduleCalendar::matchedDay([31], 28, 28))->toBe(31)
        ->and(ScheduleCalendar::matchedDay([31], 27, 28))->toBeNull();
});

it('firesOn derives day/daysInMonth from the date (slippage included)', function () {
    expect(ScheduleCalendar::firesOn([31], CarbonImmutable::parse('2026-02-28')))->toBeTrue()
        ->and(ScheduleCalendar::firesOn([31], CarbonImmutable::parse('2026-02-27')))->toBeFalse()
        ->and(ScheduleCalendar::firesOn([1, 15], CarbonImmutable::parse('2026-03-15')))->toBeTrue()
        ->and(ScheduleCalendar::firesOn([1, 15], CarbonImmutable::parse('2026-03-16')))->toBeFalse();
});
