<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Estado de triagem de um alerta (ChangeEvent): o fluxo que a equipe segue ao
 * tratar uma mudança detectada. Novo → Em análise → (Descartado | Virou caso).
 */
enum TriageStatus: string
{
    case Novo = 'novo';
    case EmAnalise = 'em_analise';
    case Descartado = 'descartado';
    case Caso = 'caso';

    public function label(): string
    {
        return match ($this) {
            self::Novo => 'Novo',
            self::EmAnalise => 'Em análise',
            self::Descartado => 'Descartado',
            self::Caso => 'Virou caso',
        };
    }

    /** Tom do design system (mapeia para <x-ui.badge :tone>). */
    public function tone(): string
    {
        return match ($this) {
            self::Novo => 'high',
            self::EmAnalise => 'med',
            self::Descartado => 'muted',
            self::Caso => 'crit',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Novo => 'fiber_new',
            self::EmAnalise => 'search',
            self::Descartado => 'block',
            self::Caso => 'flag',
        };
    }

    /** Em aberto = ainda demanda ação (não foi resolvido como descartado nem caso). */
    public function isOpen(): bool
    {
        return $this === self::Novo || $this === self::EmAnalise;
    }

    /**
     * Valores "em aberto", para filtros/escopos de query.
     *
     * @return list<string>
     */
    public static function openValues(): array
    {
        return [self::Novo->value, self::EmAnalise->value];
    }

    /**
     * Estados "em aberto" como casos do enum (para validar transições).
     *
     * @return list<TriageStatus>
     */
    public static function openCases(): array
    {
        return [self::Novo, self::EmAnalise];
    }

    /**
     * Estados "resolvidos" (descartado/virou caso) como casos do enum.
     *
     * @return list<TriageStatus>
     */
    public static function resolvedCases(): array
    {
        return [self::Descartado, self::Caso];
    }
}
