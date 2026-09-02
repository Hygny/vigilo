<?php

declare(strict_types=1);

namespace App\Enums;

enum ChangeType: string
{
    case SituacaoChanged = 'situacao_changed';
    case PartnerAdded = 'partner_added';
    case PartnerRemoved = 'partner_removed';
    case AddressChanged = 'address_changed';
    case CnaeChanged = 'cnae_changed';
    case PorteChanged = 'porte_changed';
    case NameChanged = 'name_changed';

    public function label(): string
    {
        return match ($this) {
            self::SituacaoChanged => 'Situação cadastral alterada',
            self::PartnerAdded => 'Sócio adicionado',
            self::PartnerRemoved => 'Sócio removido',
            self::AddressChanged => 'Endereço alterado',
            self::CnaeChanged => 'CNAE principal alterado',
            self::PorteChanged => 'Porte alterado',
            self::NameChanged => 'Razão social / nome fantasia alterado',
        };
    }

    /**
     * Ícone Material Symbols para o tipo de mudança.
     */
    public function icon(): string
    {
        return match ($this) {
            self::SituacaoChanged => 'gpp_maybe',
            self::PartnerAdded => 'person_add',
            self::PartnerRemoved => 'person_remove',
            self::AddressChanged => 'location_on',
            self::CnaeChanged => 'category',
            self::PorteChanged => 'straighten',
            self::NameChanged => 'badge',
        };
    }
}
