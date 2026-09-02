<?php

declare(strict_types=1);

namespace App\Enums;

enum RefreshStatus: string
{
    case Ok = 'ok';
    case NotFound = 'nao_encontrado';
    case Error = 'erro';

    public function label(): string
    {
        return match ($this) {
            self::Ok => 'Atualizado',
            self::NotFound => 'Não encontrado',
            self::Error => 'Erro',
        };
    }

    /**
     * Tom do design system (mapeia para <x-ui.badge :tone>).
     */
    public function tone(): string
    {
        return match ($this) {
            self::Ok => 'ok',
            self::NotFound => 'muted',
            self::Error => 'crit',
        };
    }

    /**
     * Ícone Material Symbols correspondente ao status.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Ok => 'check_circle',
            self::NotFound => 'help',
            self::Error => 'error',
        };
    }
}
