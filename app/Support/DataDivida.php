<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Data da dívida informada pelo usuário (marco dos indícios de esvaziamento e
 * do monitoramento de devedora): fonte única das regras de validação — o card
 * de indícios e o card de recuperação aceitam exatamente as mesmas datas.
 */
final class DataDivida
{
    /** @var list<string> */
    public const REGRAS = ['date_format:Y-m-d', 'after_or_equal:1990-01-01', 'before_or_equal:today'];
}
