<?php declare(strict_types = 1);

// odsl-E:\dev\vigilo\app\Models\CompanySnapshot.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\CompanySnapshot
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.3.30-3e0f6b0b5c1195bbbce457ff0a3249e6a9871d7fc01807224dbb526564a9694e',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\CompanySnapshot',
        'filename' => 'E:/dev/vigilo/app/Models/CompanySnapshot.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\CompanySnapshot',
    'shortName' => 'CompanySnapshot',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property int $monitored_company_id
 * @property Carbon $captured_at
 * @property string $razao_social
 * @property string|null $nome_fantasia
 * @property string $situacao_cadastral
 * @property Carbon|null $situacao_data
 * @property string|null $cnae_principal
 * @property string|null $porte
 * @property string|null $natureza_juridica
 * @property string|null $logradouro
 * @property string|null $municipio
 * @property string|null $uf
 * @property array<string, mixed> $raw_json
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MonitoredCompany $monitoredCompany
 * @property-read Collection<int, CompanyPartner> $partners
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 35,
    'endLine' => 84,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\CompanySnapshot',
        'implementingClassName' => 'App\\Models\\CompanySnapshot',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'monitored_company_id\', \'captured_at\', \'razao_social\', \'nome_fantasia\', \'situacao_cadastral\', \'situacao_data\', \'cnae_principal\', \'porte\', \'natureza_juridica\', \'logradouro\', \'municipio\', \'uf\', \'raw_json\']',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 55,
            'startTokenPos' => 77,
            'startFilePos' => 1251,
            'endTokenPos' => 118,
            'endFilePos' => 1565,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 6,
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
      'casts' => 
      array (
        'name' => 'casts',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return array<string, string>
 */',
        'startLine' => 60,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CompanySnapshot',
        'implementingClassName' => 'App\\Models\\CompanySnapshot',
        'currentClassName' => 'App\\Models\\CompanySnapshot',
        'aliasName' => NULL,
      ),
      'monitoredCompany' => 
      array (
        'name' => 'monitoredCompany',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return BelongsTo<MonitoredCompany, $this>
 */',
        'startLine' => 72,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CompanySnapshot',
        'implementingClassName' => 'App\\Models\\CompanySnapshot',
        'currentClassName' => 'App\\Models\\CompanySnapshot',
        'aliasName' => NULL,
      ),
      'partners' => 
      array (
        'name' => 'partners',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @return HasMany<CompanyPartner, $this>
 */',
        'startLine' => 80,
        'endLine' => 83,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CompanySnapshot',
        'implementingClassName' => 'App\\Models\\CompanySnapshot',
        'currentClassName' => 'App\\Models\\CompanySnapshot',
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