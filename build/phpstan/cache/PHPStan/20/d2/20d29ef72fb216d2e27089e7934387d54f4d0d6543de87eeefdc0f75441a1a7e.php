<?php declare(strict_types = 1);

// odsl-C:\laragon\www\vigilo\app\Models\PortfolioScheduledRun.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\PortfolioScheduledRun
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-10d7f72bb437420f90e3a81b151e3b453181842e6cfcfc9225670a3b86c3dd25',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\PortfolioScheduledRun',
        'filename' => 'C:/laragon/www/vigilo/app/Models/PortfolioScheduledRun.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\PortfolioScheduledRun',
    'shortName' => 'PortfolioScheduledRun',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Registro de uma execução agendada mensal de um portfólio.
 *
 * @property int $id
 * @property int $portfolio_id
 * @property int $scheduled_day
 * @property Carbon $ran_on
 * @property int $companies_count
 * @property int $dispatched_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Portfolio $portfolio
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 26,
    'endLine' => 51,
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
        'declaringClassName' => 'App\\Models\\PortfolioScheduledRun',
        'implementingClassName' => 'App\\Models\\PortfolioScheduledRun',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'portfolio_id\', \'scheduled_day\', \'ran_on\', \'companies_count\', \'dispatched_count\']',
          'attributes' => 
          array (
            'startLine' => 32,
            'endLine' => 32,
            'startTokenPos' => 67,
            'startFilePos' => 836,
            'endTokenPos' => 81,
            'endFilePos' => 917,
          ),
        ),
        'docComment' => '/** @var list<string> */',
        'attributes' => 
        array (
        ),
        'startLine' => 32,
        'endLine' => 32,
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
        'startLine' => 37,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PortfolioScheduledRun',
        'implementingClassName' => 'App\\Models\\PortfolioScheduledRun',
        'currentClassName' => 'App\\Models\\PortfolioScheduledRun',
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
        'startLine' => 47,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PortfolioScheduledRun',
        'implementingClassName' => 'App\\Models\\PortfolioScheduledRun',
        'currentClassName' => 'App\\Models\\PortfolioScheduledRun',
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