<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Nível de risco de uma empresa num semáforo simples e EXPLICÁVEL (baixo/médio/
 * alto) — não um score numérico (0–100 aparenta uma precisão que os poucos
 * sinais atuais não sustentam e convida a disputa). A regra combina os sinais
 * que já temos (sanções da CGU + situação cadastral) e cresce quando entrarem
 * dívida ativa / protestos / processos.
 */
enum NivelRisco: string
{
    case Baixo = 'baixo';
    case Medio = 'medio';
    case Alto = 'alto';

    /**
     * Classifica a partir dos sinais disponíveis:
     *  - sanção VIGENTE → alto;
     *  - sanção já expirada OU situação cadastral negativa → médio;
     *  - ativa e sem sanção → baixo.
     */
    public static function avaliar(bool $sancaoAtiva, bool $algumaSancao, bool $situacaoNegativa): self
    {
        if ($sancaoAtiva) {
            return self::Alto;
        }

        if ($algumaSancao || $situacaoNegativa) {
            return self::Medio;
        }

        return self::Baixo;
    }

    public function label(): string
    {
        return match ($this) {
            self::Baixo => 'Baixo',
            self::Medio => 'Médio',
            self::Alto => 'Alto',
        };
    }

    /** Tom do design system (mapeia para <x-ui.badge :tone>). */
    public function tone(): string
    {
        return match ($this) {
            self::Baixo => 'ok',
            self::Medio => 'med',
            self::Alto => 'crit',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Baixo => 'verified_user',
            self::Medio => 'warning',
            self::Alto => 'gpp_bad',
        };
    }
}
