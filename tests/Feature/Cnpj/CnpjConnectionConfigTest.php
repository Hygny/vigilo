<?php

declare(strict_types=1);

/**
 * Em produção a conexão `cnpj` é remota (VM Oracle — docs/vigilo-cnpj-oracle-migracao.md):
 * TLS verificado por uma CA própria e connect timeout curto, para a base fora do
 * ar não pendurar a tela (o padrão do pdo_pgsql é 30 s).
 */

/**
 * Relê config/database.php com as variáveis dadas (null = ausente), isolado do
 * .env de quem roda: o env() do Laravel lê $_SERVER, depois $_ENV e só então o
 * getenv — por isso as três fontes são trocadas e restauradas.
 *
 * @param  array<string, string|null>  $vars
 * @return array<string, mixed>
 */
function cnpjConnectionWithEnv(array $vars): array
{
    $saved = [];

    foreach ($vars as $name => $value) {
        $saved[$name] = [$_SERVER[$name] ?? null, $_ENV[$name] ?? null, getenv($name)];

        if ($value === null) {
            unset($_SERVER[$name], $_ENV[$name]);
            putenv($name);
        } else {
            $_SERVER[$name] = $_ENV[$name] = $value;
            putenv("{$name}={$value}");
        }
    }

    try {
        return (require base_path('config/database.php'))['connections']['cnpj'];
    } finally {
        foreach ($saved as $name => [$server, $env, $getenv]) {
            unset($_SERVER[$name], $_ENV[$name]);
            putenv($name);

            if ($server !== null) {
                $_SERVER[$name] = $server;
            }
            if ($env !== null) {
                $_ENV[$name] = $env;
            }
            if ($getenv !== false) {
                putenv("{$name}={$getenv}");
            }
        }
    }
}

it('defaults the CNPJ connection to a short connect timeout and no pinned CA', function () {
    $cnpj = cnpjConnectionWithEnv([
        'CNPJ_DB_SSLROOTCERT' => null,
        'CNPJ_DB_CONNECT_TIMEOUT' => null,
    ]);

    expect($cnpj['options'][PDO::ATTR_TIMEOUT])->toBe(5)
        ->and($cnpj['sslrootcert'])->toBeNull();
});

it('reads sslmode, the CA path and the connect timeout of the remote base from env', function () {
    $cnpj = cnpjConnectionWithEnv([
        'CNPJ_DB_SSLMODE' => 'verify-full',
        'CNPJ_DB_SSLROOTCERT' => '/var/www/storage/app/private/cnpj-db-ca.crt',
        'CNPJ_DB_CONNECT_TIMEOUT' => '3',
    ]);

    expect($cnpj['sslmode'])->toBe('verify-full')
        ->and($cnpj['sslrootcert'])->toBe('/var/www/storage/app/private/cnpj-db-ca.crt')
        ->and($cnpj['options'][PDO::ATTR_TIMEOUT])->toBe(3);
});

it('never turns an empty connect timeout into an infinite wait', function () {
    // No libpq, connect_timeout=0 significa esperar para sempre.
    $cnpj = cnpjConnectionWithEnv(['CNPJ_DB_CONNECT_TIMEOUT' => '']);

    expect($cnpj['options'][PDO::ATTR_TIMEOUT])->toBe(1);
});
