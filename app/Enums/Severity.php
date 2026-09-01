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
     * Tailwind badge classes used across the UI.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Critical => 'bg-red-100 text-red-800 ring-red-600/20',
            self::High => 'bg-orange-100 text-orange-800 ring-orange-600/20',
            self::Medium => 'bg-amber-100 text-amber-800 ring-amber-600/20',
            self::Low => 'bg-slate-100 text-slate-700 ring-slate-500/20',
        };
    }
}
