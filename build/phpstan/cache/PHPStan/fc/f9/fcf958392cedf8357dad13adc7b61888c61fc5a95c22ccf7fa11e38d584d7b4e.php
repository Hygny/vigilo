<?php declare(strict_types = 1);

// odsl-C:\laragon\www\vigilo\app\Models\MonitoredCompany.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\MonitoredCompany
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-28b64c40543042a2b2b0f9dc32d4a909b707311297561d1a5a4e1d7a89a16a51',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\MonitoredCompany',
        'filename' => 'C:/laragon/www/vigilo/app/Models/MonitoredCompany.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\MonitoredCompany',
    'shortName' => 'MonitoredCompany',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property int $portfolio_id
 * @property string $cnpj
 * @property string|null $label
 * @property Carbon|null $last_refreshed_at
 * @property RefreshStatus|null $last_refresh_status
 * @property string|null $last_refresh_error
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Portfolio $portfolio
 * @property-read Collection<int, CompanySnapshot> $snapshots
 * @property-read CompanySnapshot|null $latestSnapshot
 * @property-read Collection<int, ChangeEvent> $changeEvents
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 33,
    'endLine' => 90,
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
        'declaringClassName' => 'App\\Models\\MonitoredCompany',
        'implementingClassName' => 'App\\Models\\MonitoredCompany',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'portfolio_id\', \'cnpj\', \'label\', \'last_refreshed_at\', \'last_refresh_status\', \'last_refresh_error\']',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 39,
            'startTokenPos' => 92,
            'startFilePos' => 1202,
            'endTokenPos' => 109,
            'endFilePos' => 1300,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 126,
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
        'startLine' => 44,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MonitoredCompany',
        'implementingClassName' => 'App\\Models\\MonitoredCompany',
        'currentClassName' => 'App\\Models\\MonitoredCompany',
        'aliasName' => NULL,
      ),
      'formattedCnpj' => 
      array (
        'name' => 'formattedCnpj',
        'parameters' => 
        array (
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
        'startLine' => 52,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MonitoredCompany',
        'implementingClassName' => 'App\\Models\\MonitoredCompany',
        'currentClassName' => 'App\\Models\\MonitoredCompany',
        'aliasName' => NULL,
      ),
      'portfolio' => 
      array (
        'name' => 'portfolio',
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
 * @return BelongsTo<Portfolio, $this>
 */',
        'startLine' => 60,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MonitoredCompany',
        'implementingClassName' => 'App\\Models\\MonitoredCompany',
        'currentClassName' => 'App\\Models\\MonitoredCompany',
        'aliasName' => NULL,
      ),
      'snapshots' => 
      array (
        'name' => 'snapshots',
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
 * @return HasMany<CompanySnapshot, $this>
 */',
        'startLine' => 68,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MonitoredCompany',
        'implementingClassName' => 'App\\Models\\MonitoredCompany',
        'currentClassName' => 'App\\Models\\MonitoredCompany',
        'aliasName' => NULL,
      ),
      'latestSnapshot' => 
      array (
        'name' => 'latestSnapshot',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Most recent snapshot captured for this company.
 *
 * @return HasOne<CompanySnapshot, $this>
 */',
        'startLine' => 78,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MonitoredCompany',
        'implementingClassName' => 'App\\Models\\MonitoredCompany',
        'currentClassName' => 'App\\Models\\MonitoredCompany',
        'aliasName' => NULL,
      ),
      'changeEvents' => 
      array (
        'name' => 'changeEvents',
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
 * @return HasMany<ChangeEvent, $this>
 */',
        'startLine' => 86,
        'endLine' => 89,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\MonitoredCompany',
        'implementingClassName' => 'App\\Models\\MonitoredCompany',
        'currentClassName' => 'App\\Models\\MonitoredCompany',
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