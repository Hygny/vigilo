<?php declare(strict_types = 1);

// osfsl-C:/laragon/www/vigilo/vendor/composer/../laravel/framework/src/Illuminate/Queue/Middleware/RateLimited.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Queue\Middleware\RateLimited
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-8a11f1f409c6f008dc50dbcba0f0587ced220141c30715b5823cad53cabf1b47-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'filename' => 'C:/laragon/www/vigilo/vendor/composer/../laravel/framework/src/Illuminate/Queue/Middleware/RateLimited.php',
      ),
    ),
    'namespace' => 'Illuminate\\Queue\\Middleware',
    'name' => 'Illuminate\\Queue\\Middleware\\RateLimited',
    'shortName' => 'RateLimited',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 12,
    'endLine' => 168,
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
      'limiter' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'implementingClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'name' => 'limiter',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The rate limiter instance.
 *
 * @var \\Illuminate\\Cache\\RateLimiter
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 19,
        'startColumn' => 5,
        'endColumn' => 23,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'limiterName' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'implementingClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'name' => 'limiterName',
        'modifiers' => 2,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The name of the rate limiter.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 27,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'releaseAfter' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'implementingClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'name' => 'releaseAfter',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The number of seconds before a job should be available again if the limit is exceeded.
 *
 * @var \\DateTimeInterface|int|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 25,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'shouldRelease' => 
      array (
        'declaringClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'implementingClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'name' => 'shouldRelease',
        'modifiers' => 1,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'true',
          'attributes' => 
          array (
            'startLine' => 40,
            'endLine' => 40,
            'startTokenPos' => 69,
            'startFilePos' => 820,
            'endTokenPos' => 69,
            'endFilePos' => 823,
          ),
        ),
        'docComment' => '/**
 * Indicates if the job should be released if the limit is exceeded.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 33,
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
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'limiterName' => 
          array (
            'name' => 'limiterName',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 47,
            'endLine' => 47,
            'startColumn' => 33,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Create a new middleware instance.
 *
 * @param  \\UnitEnum|string  $limiterName
 */',
        'startLine' => 47,
        'endLine' => 52,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue\\Middleware',
        'declaringClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'implementingClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'currentClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'aliasName' => NULL,
      ),
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
          'job' => 
          array (
            'name' => 'job',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 28,
            'endColumn' => 31,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'next' => 
          array (
            'name' => 'next',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 34,
            'endColumn' => 38,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Process the job.
 *
 * @param  mixed  $job
 * @param  callable  $next
 * @return mixed
 */',
        'startLine' => 61,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue\\Middleware',
        'declaringClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'implementingClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'currentClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'aliasName' => NULL,
      ),
      'handleJob' => 
      array (
        'name' => 'handleJob',
        'parameters' => 
        array (
          'job' => 
          array (
            'name' => 'job',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 94,
            'endLine' => 94,
            'startColumn' => 34,
            'endColumn' => 37,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'next' => 
          array (
            'name' => 'next',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 94,
            'endLine' => 94,
            'startColumn' => 40,
            'endColumn' => 44,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'limits' => 
          array (
            'name' => 'limits',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 94,
            'endLine' => 94,
            'startColumn' => 47,
            'endColumn' => 59,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Handle a rate limited job.
 *
 * @param  mixed  $job
 * @param  callable  $next
 * @param  array  $limits
 * @return mixed
 */',
        'startLine' => 94,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Middleware',
        'declaringClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'implementingClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'currentClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'aliasName' => NULL,
      ),
      'releaseAfter' => 
      array (
        'name' => 'releaseAfter',
        'parameters' => 
        array (
          'releaseAfter' => 
          array (
            'name' => 'releaseAfter',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 115,
            'endLine' => 115,
            'startColumn' => 34,
            'endColumn' => 46,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Set the delay (in seconds) to release the job back to the queue.
 *
 * @param  \\DateTimeInterface|int  $releaseAfter
 * @return $this
 */',
        'startLine' => 115,
        'endLine' => 120,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue\\Middleware',
        'declaringClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'implementingClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'currentClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'aliasName' => NULL,
      ),
      'dontRelease' => 
      array (
        'name' => 'dontRelease',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Do not release the job back to the queue if the limit is exceeded.
 *
 * @return $this
 */',
        'startLine' => 127,
        'endLine' => 132,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue\\Middleware',
        'declaringClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'implementingClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'currentClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'aliasName' => NULL,
      ),
      'getTimeUntilNextRetry' => 
      array (
        'name' => 'getTimeUntilNextRetry',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 140,
            'endLine' => 140,
            'startColumn' => 46,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Get the number of seconds that should elapse before the job is retried.
 *
 * @param  string  $key
 * @return int
 */',
        'startLine' => 140,
        'endLine' => 143,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Queue\\Middleware',
        'declaringClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'implementingClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'currentClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'aliasName' => NULL,
      ),
      '__sleep' => 
      array (
        'name' => '__sleep',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prepare the object for serialization.
 *
 * @return array
 */',
        'startLine' => 150,
        'endLine' => 157,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue\\Middleware',
        'declaringClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'implementingClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'currentClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'aliasName' => NULL,
      ),
      '__wakeup' => 
      array (
        'name' => '__wakeup',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Prepare the object after unserialization.
 *
 * @return void
 */',
        'startLine' => 164,
        'endLine' => 167,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Queue\\Middleware',
        'declaringClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'implementingClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
        'currentClassName' => 'Illuminate\\Queue\\Middleware\\RateLimited',
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