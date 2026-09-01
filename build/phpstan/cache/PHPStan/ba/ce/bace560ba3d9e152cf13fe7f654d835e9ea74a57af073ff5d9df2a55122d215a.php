<?php declare(strict_types = 1);

// odsl-E:\dev\vigilo\app\Models\CompanyPartner.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\CompanyPartner
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.3.30-b2b4bed1748daf292554ea1ddf6baef7391a50f66ff128cfe9d3856f3f7f3e76',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\CompanyPartner',
        'filename' => 'E:/dev/vigilo/app/Models/CompanyPartner.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\CompanyPartner',
    'shortName' => 'CompanyPartner',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @property int $id
 * @property int $company_snapshot_id
 * @property string|null $documento
 * @property string $nome
 * @property string|null $qualificacao
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CompanySnapshot $companySnapshot
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 23,
    'endLine' => 38,
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
        'declaringClassName' => 'App\\Models\\CompanyPartner',
        'implementingClassName' => 'App\\Models\\CompanyPartner',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'company_snapshot_id\', \'documento\', \'nome\', \'qualificacao\']',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 29,
            'startTokenPos' => 67,
            'startFilePos' => 739,
            'endTokenPos' => 78,
            'endFilePos' => 798,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 87,
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
      'companySnapshot' => 
      array (
        'name' => 'companySnapshot',
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
        'startLine' => 34,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\CompanyPartner',
        'implementingClassName' => 'App\\Models\\CompanyPartner',
        'currentClassName' => 'App\\Models\\CompanyPartner',
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