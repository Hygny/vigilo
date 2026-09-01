<?php declare(strict_types = 1);

// odsl-C:\laragon\www\vigilo\app\Contracts\CnpjDataProvider.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Contracts\CnpjDataProvider
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-2fefb735de786c9e694c916b23fd229fcd4f9a1c60ca4bbedf04ab9b2e9b6c18',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Contracts\\CnpjDataProvider',
        'filename' => 'C:/laragon/www/vigilo/app/Contracts/CnpjDataProvider.php',
      ),
    ),
    'namespace' => 'App\\Contracts',
    'name' => 'App\\Contracts\\CnpjDataProvider',
    'shortName' => 'CnpjDataProvider',
    'isInterface' => true,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 24,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
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
    ),
    'immediateMethods' => 
    array (
      'fetch' => 
      array (
        'name' => 'fetch',
        'parameters' => 
        array (
          'cnpj' => 
          array (
            'name' => 'cnpj',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 23,
            'endLine' => 23,
            'startColumn' => 27,
            'endColumn' => 38,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'App\\DTO\\CompanyData',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Fetch normalized cadastral data for a CNPJ.
 *
 * Returns null when the company is not found (HTTP 404). Throws when the
 * input is not a valid CNPJ, or when the upstream service fails after the
 * configured retries (timeout, rate limit, 5xx).
 *
 * @throws InvalidCnpjException
 * @throws CnpjProviderException
 */',
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 54,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Contracts',
        'declaringClassName' => 'App\\Contracts\\CnpjDataProvider',
        'implementingClassName' => 'App\\Contracts\\CnpjDataProvider',
        'currentClassName' => 'App\\Contracts\\CnpjDataProvider',
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