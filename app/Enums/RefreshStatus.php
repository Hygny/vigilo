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

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Ok => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
            self::NotFound => 'bg-slate-100 text-slate-600 ring-slate-500/20',
            self::Error => 'bg-red-100 text-red-800 ring-red-600/20',
        };
    }
}
