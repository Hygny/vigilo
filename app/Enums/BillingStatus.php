<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Ciclo de vida da cobrança de uma organização, espelhando a assinatura no
 * Asaas. Dirigido pelos webhooks de pagamento (fatia 22). É separado de
 * `suspended_at`: o status descreve a cobrança; a suspensão corta o acesso.
 */
enum BillingStatus: string
{
    /** Sem cobrança ativa (plano Free ou nunca assinou). */
    case None = 'none';

    /** Em período de teste grátis, antes da primeira cobrança. */
    case Trialing = 'trialing';

    /** Assinatura paga em dia. */
    case Active = 'active';

    /** Pagamento vencido — organização suspensa até regularizar. */
    case PastDue = 'past_due';

    /** Assinatura cancelada (rebaixada para Free). */
    case Canceled = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Sem cobrança',
            self::Trialing => 'Em teste',
            self::Active => 'Ativa',
            self::PastDue => 'Pagamento vencido',
            self::Canceled => 'Cancelada',
        };
    }
}
