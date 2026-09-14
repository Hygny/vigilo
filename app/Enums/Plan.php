<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Planos de assinatura da organização. Limites e preços vivem em config/plans.php
 * (ajustáveis por env), para o negócio calibrar sem tocar em código. A cobrança
 * de fato (Asaas) entra numa fatia posterior; aqui o plano só define a quota.
 */
enum Plan: string
{
    case Free = 'free';
    case Pro = 'pro';
    case Business = 'business';

    public function label(): string
    {
        return match ($this) {
            self::Free => 'Free',
            self::Pro => 'Pro',
            self::Business => 'Business',
        };
    }

    /**
     * Teto de CNPJs monitorados por organização neste plano.
     */
    public function maxCompanies(): int
    {
        return (int) config("plans.{$this->value}.max_companies", 0);
    }

    /**
     * Preço mensal em reais (exibição; usado depois na integração de cobrança).
     */
    public function monthlyPrice(): float
    {
        return (float) config("plans.{$this->value}.price", 0);
    }
}
