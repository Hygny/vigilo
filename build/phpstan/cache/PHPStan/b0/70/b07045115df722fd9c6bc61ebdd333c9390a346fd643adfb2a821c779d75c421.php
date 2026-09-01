<?php declare(strict_types = 1);

// odsl-E:\dev\vigilo\app\Jobs\DispatchMonthlyRefreshJob.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Jobs\DispatchMonthlyRefreshJob
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.3.30-37370ad66a3e1cb2a90626572457384435bfd987af50a60facbac5a832cb0991',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Jobs\\DispatchMonthlyRefreshJob',
        'filename' => 'E:/dev/vigilo/app/Jobs/DispatchMonthlyRefreshJob.php',
      ),
    ),
    'namespace' => 'App\\Jobs',
    'name' => 'App\\Jobs\\DispatchMonthlyRefreshJob',
    'shortName' => 'DispatchMonthlyRefreshJob',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Fan out a refresh for every monitored company as a single batch.
 *
 * Each queued RefreshMonitoredCompanyJob is individually rate limited, so the
 * batch drains at a rate that respects the upstream provider.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 18,
    'endLine' => 39,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Foundation\\Queue\\Queueable',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 22,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Jobs',
        'declaringClassName' => 'App\\Jobs\\DispatchMonthlyRefreshJob',
        'implementingClassName' => 'App\\Jobs\\DispatchMonthlyRefreshJob',
        'currentClassName' => 'App\\Jobs\\DispatchMonthlyRefreshJob',
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