<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\QueryException;
use PDOException;

/**
 * Roda consultas na base CNPJ com `statement_timeout` (só no Postgres; noutros
 * drivers, direto). Fonte única das buscas por nome (empresas e pessoas).
 *
 * Timeout estourado = {@see QueryException} com SQLSTATE {@see SQLSTATE_TIMEOUT}.
 * Base fora do ar pode chegar como QueryException OU como {@see PDOException}
 * crua (a falha nasce ao abrir a transação, fora do runQueryCallback) — quem
 * chama trata as duas ({@see falhaDeBase()}).
 */
final class ConsultaComTimeout
{
    /** SQLSTATE do Postgres para statement_timeout estourado. */
    public const SQLSTATE_TIMEOUT = '57014';

    /**
     * @template T
     *
     * @param  callable(): T  $consulta
     * @return T
     */
    public static function executar(ConnectionInterface $db, int $timeoutMs, callable $consulta): mixed
    {
        if (! $db instanceof Connection || $db->getDriverName() !== 'pgsql' || $timeoutMs <= 0) {
            return $consulta();
        }

        return $db->transaction(function () use ($db, $timeoutMs, $consulta): mixed {
            $db->statement('SET LOCAL statement_timeout = '.$timeoutMs);

            return $consulta();
        });
    }

    /** A exceção é "a consulta passou do tempo" (e não base fora)? */
    public static function estourou(\Throwable $e): bool
    {
        return $e instanceof QueryException && (string) $e->getCode() === self::SQLSTATE_TIMEOUT;
    }

    /**
     * `report()` sem dado pessoal: a mensagem de uma QueryException traz o SQL
     * com os bindings (o nome da pessoa buscada) e iria para o laravel.log fora
     * da retenção da trilha de auditoria. Reporta só SQLSTATE e conexão.
     */
    public static function reportarSemDados(\Throwable $e): void
    {
        if ($e instanceof QueryException) {
            report(new \RuntimeException('Falha na base CNPJ (SQLSTATE '.$e->getCode().', conexão '.$e->getConnectionName().') — SQL omitido: pode conter dado de pessoa.'));

            return;
        }

        report($e);
    }

    /** A exceção é falha da base (fora do ar, timeout, erro de SQL)? */
    public static function falhaDeBase(\Throwable $e): bool
    {
        return $e instanceof QueryException || $e instanceof PDOException;
    }
}
