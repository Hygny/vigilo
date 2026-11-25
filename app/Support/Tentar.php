<?php

declare(strict_types=1);

namespace App\Support;

use Throwable;

/**
 * Degradação por bloco: roda uma consulta e, se ela falhar, registra o erro
 * (`report`) e devolve null — quem chama mostra "indisponível" naquele bloco em
 * vez de derrubar a página inteira. Fonte única (perfil, risco, risco ao redor).
 */
final class Tentar
{
    /**
     * @template T
     *
     * @param  callable(): T  $consulta
     * @return T|null
     */
    public static function ouNull(callable $consulta): mixed
    {
        try {
            return $consulta();
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }
}
