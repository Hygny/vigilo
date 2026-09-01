<?php declare(strict_types = 1);

// odsl-C:\laragon\www\vigilo\app\Http\Controllers\Api\CnpjLookupController.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Controllers\Api\CnpjLookupController
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-54b3437b0492337dfd4294b932ffd59066929e30df94e16d252e0f9fce1397c9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Controllers\\Api\\CnpjLookupController',
        'filename' => 'C:/laragon/www/vigilo/app/Http/Controllers/Api/CnpjLookupController.php',
      ),
    ),
    'namespace' => 'App\\Http\\Controllers\\Api',
    'name' => 'App\\Http\\Controllers\\Api\\CnpjLookupController',
    'shortName' => 'CnpjLookupController',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Serves this app\'s known cadastral data for a single CNPJ to authenticated
 * integrations (Bearer token via Sanctum).
 *
 * Scoped to the token owner\'s organization: only CNPJs that the organization
 * already monitors — and that have at least one collected snapshot — are
 * returned. Anything else is a 404, which the consumer treats as "pendente"
 * (não trava), per the agreed contract.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 25,
    'endLine' => 76,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'App\\Http\\Controllers\\Controller',
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
      '__invoke' => 
      array (
        'name' => '__invoke',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 27,
            'endLine' => 27,
            'startColumn' => 30,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 27,
            'endLine' => 27,
            'startColumn' => 48,
            'endColumn' => 59,
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
            'name' => 'Illuminate\\Http\\JsonResponse',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 27,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Controllers\\Api',
        'declaringClassName' => 'App\\Http\\Controllers\\Api\\CnpjLookupController',
        'implementingClassName' => 'App\\Http\\Controllers\\Api\\CnpjLookupController',
        'currentClassName' => 'App\\Http\\Controllers\\Api\\CnpjLookupController',
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