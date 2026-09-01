<?php declare(strict_types = 1);

// odsl-C:\laragon\www\vigilo\app\Services\CompanyImporter.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\CompanyImporter
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-8915c8fd62f517d7a11ca8faf21e063f28f4d9c3116ce3ad97a5f4a046c89a00',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\CompanyImporter',
        'filename' => 'C:/laragon/www/vigilo/app/Services/CompanyImporter.php',
      ),
    ),
    'namespace' => 'App\\Services',
    'name' => 'App\\Services\\CompanyImporter',
    'shortName' => 'CompanyImporter',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Imports monitored companies into a portfolio from CSV rows.
 *
 * Each row is normalized and validated (mod-11), de-duplicated against both the
 * file itself and the companies already in the portfolio, and either created or
 * rejected with a reason. Nothing here does authorization — the caller must have
 * verified the user owns the portfolio.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 21,
    'endLine' => 170,
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
      'REASON_EMPTY' => 
      array (
        'declaringClassName' => 'App\\Services\\CompanyImporter',
        'implementingClassName' => 'App\\Services\\CompanyImporter',
        'name' => 'REASON_EMPTY',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'cnpj_vazio\'',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 23,
            'startTokenPos' => 58,
            'startFilePos' => 631,
            'endTokenPos' => 58,
            'endFilePos' => 642,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 45,
      ),
      'REASON_INVALID' => 
      array (
        'declaringClassName' => 'App\\Services\\CompanyImporter',
        'implementingClassName' => 'App\\Services\\CompanyImporter',
        'name' => 'REASON_INVALID',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'cnpj_invalido\'',
          'attributes' => 
          array (
            'startLine' => 25,
            'endLine' => 25,
            'startTokenPos' => 69,
            'startFilePos' => 680,
            'endTokenPos' => 69,
            'endFilePos' => 694,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 25,
        'endLine' => 25,
        'startColumn' => 5,
        'endColumn' => 50,
      ),
      'REASON_DUPLICATE' => 
      array (
        'declaringClassName' => 'App\\Services\\CompanyImporter',
        'implementingClassName' => 'App\\Services\\CompanyImporter',
        'name' => 'REASON_DUPLICATE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'duplicado\'',
          'attributes' => 
          array (
            'startLine' => 27,
            'endLine' => 27,
            'startTokenPos' => 80,
            'startFilePos' => 734,
            'endTokenPos' => 80,
            'endFilePos' => 744,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'import' => 
      array (
        'name' => 'import',
        'parameters' => 
        array (
          'portfolio' => 
          array (
            'name' => 'portfolio',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Portfolio',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 32,
            'endLine' => 32,
            'startColumn' => 28,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'rows' => 
          array (
            'name' => 'rows',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'iterable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 32,
            'endLine' => 32,
            'startColumn' => 50,
            'endColumn' => 63,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\DTO\\ImportReport',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  iterable<int, array{cnpj?: string|null, label?: string|null, line?: int}>  $rows
 */',
        'startLine' => 32,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\CompanyImporter',
        'implementingClassName' => 'App\\Services\\CompanyImporter',
        'currentClassName' => 'App\\Services\\CompanyImporter',
        'aliasName' => NULL,
      ),
      'importFromFile' => 
      array (
        'name' => 'importFromFile',
        'parameters' => 
        array (
          'portfolio' => 
          array (
            'name' => 'portfolio',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Portfolio',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 92,
            'endLine' => 92,
            'startColumn' => 36,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'path' => 
          array (
            'name' => 'path',
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
            'startLine' => 92,
            'endLine' => 92,
            'startColumn' => 58,
            'endColumn' => 69,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\DTO\\ImportReport',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 92,
        'endLine' => 95,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\CompanyImporter',
        'implementingClassName' => 'App\\Services\\CompanyImporter',
        'currentClassName' => 'App\\Services\\CompanyImporter',
        'aliasName' => NULL,
      ),
      'readCsv' => 
      array (
        'name' => 'readCsv',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
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
            'startLine' => 104,
            'endLine' => 104,
            'startColumn' => 30,
            'endColumn' => 41,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Generator',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Stream rows from a CSV file. Accepts a `cnpj[,label]` header (comma or
 * semicolon delimited); without a recognizable header the first two columns
 * are taken as cnpj and label positionally.
 *
 * @return Generator<int, array{cnpj: string, label: ?string, line: int}>
 */',
        'startLine' => 104,
        'endLine' => 153,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => true,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\CompanyImporter',
        'implementingClassName' => 'App\\Services\\CompanyImporter',
        'currentClassName' => 'App\\Services\\CompanyImporter',
        'aliasName' => NULL,
      ),
      'sniffDelimiter' => 
      array (
        'name' => 'sniffDelimiter',
        'parameters' => 
        array (
          'path' => 
          array (
            'name' => 'path',
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
            'startLine' => 155,
            'endLine' => 155,
            'startColumn' => 37,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 155,
        'endLine' => 169,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\CompanyImporter',
        'implementingClassName' => 'App\\Services\\CompanyImporter',
        'currentClassName' => 'App\\Services\\CompanyImporter',
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