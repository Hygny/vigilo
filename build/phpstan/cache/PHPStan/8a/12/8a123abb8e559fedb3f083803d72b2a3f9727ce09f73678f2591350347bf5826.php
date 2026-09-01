<?php declare(strict_types = 1);

// odsl-C:\laragon\www\vigilo\app\Console\Commands\IssueApiToken.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\IssueApiToken
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-bbbcfbedaa8905b8e29a8f2f961c9530bb1bfeb9d49708bad79ada94cb500ae3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\IssueApiToken',
        'filename' => 'C:/laragon/www/vigilo/app/Console/Commands/IssueApiToken.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands',
    'name' => 'App\\Console\\Commands\\IssueApiToken',
    'shortName' => 'IssueApiToken',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Emite um token Bearer (Sanctum) para a API pública de CNPJ. O token herda a
 * organização do usuário informado — é o que escopa o que a API pode devolver.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 43,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\IssueApiToken',
        'implementingClassName' => 'App\\Console\\Commands\\IssueApiToken',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'vigilo:api-token
        {email : E-mail do usuário dono do token (define a organização/escopo)}
        {--name=integracao : Nome do token, para você reconhecê-lo/revogá-lo depois}\'',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 18,
            'startTokenPos' => 45,
            'startFilePos' => 365,
            'endTokenPos' => 45,
            'endFilePos' => 553,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 89,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\IssueApiToken',
        'implementingClassName' => 'App\\Console\\Commands\\IssueApiToken',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Emite um token Bearer da API de CNPJ, no escopo da organização do usuário.\'',
          'attributes' => 
          array (
            'startLine' => 20,
            'endLine' => 20,
            'startTokenPos' => 54,
            'startFilePos' => 586,
            'endTokenPos' => 54,
            'endFilePos' => 664,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 20,
        'endLine' => 20,
        'startColumn' => 5,
        'endColumn' => 109,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 22,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands',
        'declaringClassName' => 'App\\Console\\Commands\\IssueApiToken',
        'implementingClassName' => 'App\\Console\\Commands\\IssueApiToken',
        'currentClassName' => 'App\\Console\\Commands\\IssueApiToken',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));