<?php

declare(strict_types=1);

namespace App\Enums;

use App\Services\Risco\RiscoService;

/**
 * Cadastros de sanção da CGU expostos pela API de Dados do Portal da
 * Transparência. Cada um é uma lista pública de punições a pessoas jurídicas.
 */
enum FonteSancao: string
{
    /** Cadastro de Empresas Inidôneas e Suspensas. */
    case Ceis = 'ceis';

    /** Cadastro Nacional de Empresas Punidas (Lei Anticorrupção). */
    case Cnep = 'cnep';

    /** Cadastro de Entidades Privadas Sem Fins Lucrativos Impedidas. */
    case Cepim = 'cepim';

    /** Sigla exibida (CEIS/CNEP/CEPIM). */
    public function sigla(): string
    {
        return strtoupper($this->value);
    }

    public function label(): string
    {
        return match ($this) {
            self::Ceis => 'Inidôneas e suspensas',
            self::Cnep => 'Punidas (Lei Anticorrupção)',
            self::Cepim => 'Entidades impedidas',
        };
    }

    /** Caminho (rota) do cadastro na API de Dados. */
    public function endpoint(): string
    {
        return match ($this) {
            self::Ceis => 'ceis',
            self::Cnep => 'cnep',
            self::Cepim => 'cepim',
        };
    }

    /**
     * Nome do parâmetro de filtro por sancionado. O CEPIM usa `cnpjSancionado`;
     * os demais, `codigoSancionado`. O filtro da API é pouco confiável, então o
     * resultado é SEMPRE revalidado pelo CNPJ no código ({@see RiscoService}).
     */
    public function parametroSancionado(): string
    {
        return $this === self::Cepim ? 'cnpjSancionado' : 'codigoSancionado';
    }
}
