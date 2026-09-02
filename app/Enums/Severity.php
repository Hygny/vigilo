<?php

declare(strict_types=1);

namespace App\Enums;

enum Severity: string
{
    case Critical = 'critical';
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';

    /**
     * Higher weight = more severe. Useful for ordering alerts.
     */
    public function weight(): int
    {
        return match ($this) {
            self::Critical => 4,
            self::High => 3,
            self::Medium => 2,
            self::Low => 1,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Critical => 'Crítico',
            self::High => 'Alto',
            self::Medium => 'Médio',
            self::Low => 'Baixo',
        };
    }

    /**
     * Tom do design system (mapeia para os componentes <x-ui.badge :tone>).
     */
    public function tone(): string
    {
        return match ($this) {
            self::Critical => 'crit',
            self::High => 'high',
            self::Medium => 'med',
            self::Low => 'low',
        };
    }
}
