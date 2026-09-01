<?php declare(strict_types = 1);

// odsl-E:\dev\vigilo\app\Models\ChangeEvent.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\ChangeEvent
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.3.30-04cdf530b41ec96ac66f5ff38b29bac1bdf9decacaa94599188c59203f08d800',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\ChangeEvent',
        'filename' => 'E:/dev/vigilo/app/Models/ChangeEvent.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\ChangeEvent',
    'shortName' => 'ChangeEvent',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property int $monitored_company_id
 * @property int|null $from_snapshot_id
 * @property int $to_snapshot_id
 * @property ChangeType $type
 * @property string $field
 * @property string|null $old_value
 * @property string|null $new_value
 * @property Severity $severity
 * @property Carbon $detected_at
 * @property Carbon|null $acknowledged_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MonitoredCompany $monitoredCompany
 * @property-read CompanySnapshot|null $fromSnapshot
 * @property-read CompanySnapshot $toSnapshot
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 33,
    'endLine' => 93,
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
        'declaringClassName' => 'App\\Models\\ChangeEvent',
        'implementingClassName' => 'App\\Models\\ChangeEvent',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'monitored_company_id\', \'from_snapshot_id\', \'to_snapshot_id\', \'type\', \'field\', \'old_value\', \'new_value\', \'severity\', \'detected_at\', \'acknowledged_at\']',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 50,
            'startTokenPos' => 77,
            'startFilePos' => 1090,
            'endTokenPos' => 109,
            'endFilePos' => 1327,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 50,
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
        'startLine' => 55,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ChangeEvent',
        'implementingClassName' => 'App\\Models\\ChangeEvent',
        'currentClassName' => 'App\\Models\\ChangeEvent',
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
        'declaringClassName' => 'App\\Models\\ChangeEvent',
        'implementingClassName' => 'App\\Models\\ChangeEvent',
        'currentClassName' => 'App\\Models\\ChangeEvent',
        'aliasName' => NULL,
      ),
      'fromSnapshot' => 
      array (
        'name' => 'fromSnapshot',
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
 * @return BelongsTo<CompanySnapshot, $this>
 */',
        'startLine' => 76,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ChangeEvent',
        'implementingClassName' => 'App\\Models\\ChangeEvent',
        'currentClassName' => 'App\\Models\\ChangeEvent',
        'aliasName' => NULL,
      ),
      'toSnapshot' => 
      array (
        'name' => 'toSnapshot',
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
 * @return BelongsTo<CompanySnapshot, $this>
 */',
        'startLine' => 84,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ChangeEvent',
        'implementingClassName' => 'App\\Models\\ChangeEvent',
        'currentClassName' => 'App\\Models\\ChangeEvent',
        'aliasName' => NULL,
      ),
      'isAcknowledged' => 
      array (
        'name' => 'isAcknowledged',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 89,
        'endLine' => 92,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ChangeEvent',
        'implementingClassName' => 'App\\Models\\ChangeEvent',
        'currentClassName' => 'App\\Models\\ChangeEvent',
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