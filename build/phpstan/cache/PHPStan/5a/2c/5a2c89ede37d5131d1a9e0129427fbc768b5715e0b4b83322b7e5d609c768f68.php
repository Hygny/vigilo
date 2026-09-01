<?php declare(strict_types = 1);

// odsl-E:\dev\vigilo\app\Services\CompanyImporter.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\CompanyImporter
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.3.30-770bffb7951bff4848ec71d22b0b0b598220c547d943fd694291d50b01886885',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\CompanyImporter',
        'filename' => 'E:/dev/vigilo/app/Services/CompanyImporter.php',
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
    'startLine' => 20,
    'endLine' => 156,
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
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 53,
            'startFilePos' => 571,
            'endTokenPos' => 53,
            'endFilePos' => 582,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
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
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 64,
            'startFilePos' => 620,
            'endTokenPos' => 64,
            'endFilePos' => 634,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
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
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 75,
            'startFilePos' => 674,
            'endTokenPos' => 75,
            'endFilePos' => 684,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
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
            'startLine' => 31,
            'endLine' => 31,
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
            'startLine' => 31,
            'endLine' => 31,
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
        'startLine' => 31,
        'endLine' => 85,
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
            'startLine' => 87,
            'endLine' => 87,
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
            'startLine' => 87,
            'endLine' => 87,
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
        'startLine' => 87,
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
            'startLine' => 99,
            'endLine' => 99,
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
        'startLine' => 99,
        'endLine' => 148,
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
            'startLine' => 150,
            'endLine' => 150,
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
        'startLine' => 150,
        'endLine' => 155,
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