<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Regra de "em que dias o agendamento mensal dispara" — fonte ÚNICA consumida
 * pelo cron (App\Actions\RunDuePortfolioSchedules) e pela tela (Livewire
 * Portfolios\Show), que antes a duplicavam. Um dia configurado maior que o número
 * de dias do mês "escorrega" para o último dia (ex.: 31 configurado roda em 28/fev).
 */
final class ScheduleCalendar
{
    /**
     * O dia CONFIGURADO que dispara nesta data (day/daysInMonth), ou null. O cron
     * usa o retorno para registrar qual dia agendado casou.
     *
     * @param  list<int>  $days
     */
    public static function matchedDay(array $days, int $day, int $daysInMonth): ?int
    {
        foreach ($days as $configured) {
            $configured = (int) $configured;

            if (min($configured, $daysInMonth) === $day) {
                return $configured;
            }
        }

        return null;
    }

    /**
     * Idem, a partir de uma data.
     *
     * @param  list<int>  $days
     */
    public static function matchedDayFor(array $days, CarbonInterface $date): ?int
    {
        return self::matchedDay($days, $date->day, $date->daysInMonth);
    }

    /**
     * O agendamento dispara nesta data?
     *
     * @param  list<int>  $days
     */
    public static function firesOn(array $days, CarbonInterface $date): bool
    {
        return self::matchedDayFor($days, $date) !== null;
    }
}
