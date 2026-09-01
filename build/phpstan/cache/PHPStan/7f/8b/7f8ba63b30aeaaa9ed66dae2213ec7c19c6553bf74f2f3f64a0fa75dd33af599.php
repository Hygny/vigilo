<?php declare(strict_types = 1);

// osfsl-C:/laragon/www/vigilo/vendor/composer/../laravel/framework/src/Illuminate/Support/Sleep.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Illuminate\Support\Sleep
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-70404184268a18c3887175d9d044f1f20971eb2e2847f84445e3328def57ef02-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Illuminate\\Support\\Sleep',
        'filename' => 'C:/laragon/www/vigilo/vendor/composer/../laravel/framework/src/Illuminate/Support/Sleep.php',
      ),
    ),
    'namespace' => 'Illuminate\\Support',
    'name' => 'Illuminate\\Support\\Sleep',
    'shortName' => 'Sleep',
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
    'endLine' => 555,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Support\\Traits\\Macroable',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'fakeSleepCallbacks' => 
      array (
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'name' => 'fakeSleepCallbacks',
        'modifiers' => 17,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 62,
            'startFilePos' => 349,
            'endTokenPos' => 63,
            'endFilePos' => 350,
          ),
        ),
        'docComment' => '/**
 * The fake sleep callbacks.
 *
 * @var array
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 43,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'syncWithCarbon' => 
      array (
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'name' => 'syncWithCarbon',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 28,
            'startTokenPos' => 76,
            'startFilePos' => 483,
            'endTokenPos' => 76,
            'endFilePos' => 487,
          ),
        ),
        'docComment' => '/**
 * Keep Carbon\'s "now" in sync when sleeping.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 28,
        'endLine' => 28,
        'startColumn' => 5,
        'endColumn' => 45,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'duration' => 
      array (
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'name' => 'duration',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The total duration to sleep.
 *
 * @var \\Carbon\\CarbonInterval
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 21,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'while' => 
      array (
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'name' => 'while',
        'modifiers' => 1,
        'type' => NULL,
        'default' => NULL,
        'docComment' => '/**
 * The callback that determines if sleeping should continue.
 *
 * @var \\Closure
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 42,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 18,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'pending' => 
      array (
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'name' => 'pending',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 49,
            'endLine' => 49,
            'startTokenPos' => 101,
            'startFilePos' => 850,
            'endTokenPos' => 101,
            'endFilePos' => 853,
          ),
        ),
        'docComment' => '/**
 * The pending duration to sleep.
 *
 * @var int|float|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 49,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 30,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fake' => 
      array (
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'name' => 'fake',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 56,
            'endLine' => 56,
            'startTokenPos' => 114,
            'startFilePos' => 978,
            'endTokenPos' => 114,
            'endFilePos' => 982,
          ),
        ),
        'docComment' => '/**
 * Indicates that all sleeping should be faked.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 56,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 35,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'sequence' => 
      array (
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'name' => 'sequence',
        'modifiers' => 18,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 63,
            'endLine' => 63,
            'startTokenPos' => 127,
            'startFilePos' => 1154,
            'endTokenPos' => 128,
            'endFilePos' => 1155,
          ),
        ),
        'docComment' => '/**
 * The sequence of sleep durations encountered while faking.
 *
 * @var array<int, \\Carbon\\CarbonInterval>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 63,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 36,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'shouldSleep' => 
      array (
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'name' => 'shouldSleep',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'true',
          'attributes' => 
          array (
            'startLine' => 70,
            'endLine' => 70,
            'startTokenPos' => 139,
            'startFilePos' => 1275,
            'endTokenPos' => 139,
            'endFilePos' => 1278,
          ),
        ),
        'docComment' => '/**
 * Indicates if the instance should sleep.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 70,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'alreadySlept' => 
      array (
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'name' => 'alreadySlept',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => 'false',
          'attributes' => 
          array (
            'startLine' => 77,
            'endLine' => 77,
            'startTokenPos' => 150,
            'startFilePos' => 1413,
            'endTokenPos' => 150,
            'endFilePos' => 1417,
          ),
        ),
        'docComment' => '/**
 * Indicates if the instance already slept via `then()`.
 *
 * @var bool
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 77,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 36,
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
          'duration' => 
          array (
            'name' => 'duration',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 84,
            'endLine' => 84,
            'startColumn' => 33,
            'endColumn' => 41,
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
 * Create a new class instance.
 *
 * @param  int|float|\\DateInterval  $duration
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
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'for' => 
      array (
        'name' => 'for',
        'parameters' => 
        array (
          'duration' => 
          array (
            'name' => 'duration',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 95,
            'endLine' => 95,
            'startColumn' => 32,
            'endColumn' => 40,
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
 * Sleep for the given duration.
 *
 * @param  \\DateInterval|int|float  $duration
 * @return static
 */',
        'startLine' => 95,
        'endLine' => 98,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'until' => 
      array (
        'name' => 'until',
        'parameters' => 
        array (
          'timestamp' => 
          array (
            'name' => 'timestamp',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 106,
            'endLine' => 106,
            'startColumn' => 34,
            'endColumn' => 43,
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
 * Sleep until the given timestamp.
 *
 * @param  \\DateTimeInterface|int|float|numeric-string  $timestamp
 * @return static
 */',
        'startLine' => 106,
        'endLine' => 113,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'usleep' => 
      array (
        'name' => 'usleep',
        'parameters' => 
        array (
          'duration' => 
          array (
            'name' => 'duration',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 121,
            'endLine' => 121,
            'startColumn' => 35,
            'endColumn' => 43,
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
 * Sleep for the given number of microseconds.
 *
 * @param  int  $duration
 * @return static
 */',
        'startLine' => 121,
        'endLine' => 124,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'sleep' => 
      array (
        'name' => 'sleep',
        'parameters' => 
        array (
          'duration' => 
          array (
            'name' => 'duration',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 132,
            'endLine' => 132,
            'startColumn' => 34,
            'endColumn' => 42,
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
 * Sleep for the given number of seconds.
 *
 * @param  int|float  $duration
 * @return static
 */',
        'startLine' => 132,
        'endLine' => 135,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'duration' => 
      array (
        'name' => 'duration',
        'parameters' => 
        array (
          'duration' => 
          array (
            'name' => 'duration',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 143,
            'endLine' => 143,
            'startColumn' => 33,
            'endColumn' => 41,
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
 * Sleep for the given duration. Replaces any previously defined duration.
 *
 * @param  \\DateInterval|int|float  $duration
 * @return $this
 */',
        'startLine' => 143,
        'endLine' => 161,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'minutes' => 
      array (
        'name' => 'minutes',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sleep for the given number of minutes.
 *
 * @return $this
 */',
        'startLine' => 168,
        'endLine' => 173,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'minute' => 
      array (
        'name' => 'minute',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sleep for one minute.
 *
 * @return $this
 */',
        'startLine' => 180,
        'endLine' => 183,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'seconds' => 
      array (
        'name' => 'seconds',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sleep for the given number of seconds.
 *
 * @return $this
 */',
        'startLine' => 190,
        'endLine' => 195,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'second' => 
      array (
        'name' => 'second',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sleep for one second.
 *
 * @return $this
 */',
        'startLine' => 202,
        'endLine' => 205,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'milliseconds' => 
      array (
        'name' => 'milliseconds',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sleep for the given number of milliseconds.
 *
 * @return $this
 */',
        'startLine' => 212,
        'endLine' => 217,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'millisecond' => 
      array (
        'name' => 'millisecond',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sleep for one millisecond.
 *
 * @return $this
 */',
        'startLine' => 224,
        'endLine' => 227,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'microseconds' => 
      array (
        'name' => 'microseconds',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sleep for the given number of microseconds.
 *
 * @return $this
 */',
        'startLine' => 234,
        'endLine' => 239,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'microsecond' => 
      array (
        'name' => 'microsecond',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sleep for one microsecond.
 *
 * @return $this
 */',
        'startLine' => 246,
        'endLine' => 249,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'and' => 
      array (
        'name' => 'and',
        'parameters' => 
        array (
          'duration' => 
          array (
            'name' => 'duration',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 257,
            'endLine' => 257,
            'startColumn' => 25,
            'endColumn' => 33,
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
 * Add additional time to sleep for.
 *
 * @param  int|float  $duration
 * @return $this
 */',
        'startLine' => 257,
        'endLine' => 262,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'while' => 
      array (
        'name' => 'while',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Closure',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 270,
            'endLine' => 270,
            'startColumn' => 27,
            'endColumn' => 43,
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
 * Sleep while a given callback returns "true".
 *
 * @param  \\Closure  $callback
 * @return $this
 */',
        'startLine' => 270,
        'endLine' => 275,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'then' => 
      array (
        'name' => 'then',
        'parameters' => 
        array (
          'then' => 
          array (
            'name' => 'then',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'callable',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 283,
            'endLine' => 283,
            'startColumn' => 26,
            'endColumn' => 39,
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
 * Specify a callback that should be executed after sleeping.
 *
 * @param  callable  $then
 * @return mixed
 */',
        'startLine' => 283,
        'endLine' => 290,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      '__destruct' => 
      array (
        'name' => '__destruct',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Handle the object\'s destruction.
 *
 * @return void
 */',
        'startLine' => 297,
        'endLine' => 300,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'goodnight' => 
      array (
        'name' => 'goodnight',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Handle the object\'s destruction.
 *
 * @return void
 *
 * @throws \\RuntimeException
 */',
        'startLine' => 309,
        'endLine' => 356,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'pullPending' => 
      array (
        'name' => 'pullPending',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Resolve the pending duration.
 *
 * @return int|float
 *
 * @throws \\RuntimeException
 */',
        'startLine' => 365,
        'endLine' => 380,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'fake' => 
      array (
        'name' => 'fake',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 389,
                'endLine' => 389,
                'startTokenPos' => 1218,
                'startFilePos' => 7907,
                'endTokenPos' => 1218,
                'endFilePos' => 7910,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 389,
            'endLine' => 389,
            'startColumn' => 33,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'syncWithCarbon' => 
          array (
            'name' => 'syncWithCarbon',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 389,
                'endLine' => 389,
                'startTokenPos' => 1225,
                'startFilePos' => 7931,
                'endTokenPos' => 1225,
                'endFilePos' => 7935,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 389,
            'endLine' => 389,
            'startColumn' => 48,
            'endColumn' => 70,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Stay awake and capture any attempts to sleep.
 *
 * @param  bool  $value
 * @param  bool  $syncWithCarbon
 * @return void
 */',
        'startLine' => 389,
        'endLine' => 396,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'assertSlept' => 
      array (
        'name' => 'assertSlept',
        'parameters' => 
        array (
          'expected' => 
          array (
            'name' => 'expected',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 405,
            'endLine' => 405,
            'startColumn' => 40,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'times' => 
          array (
            'name' => 'times',
            'default' => 
            array (
              'code' => '1',
              'attributes' => 
              array (
                'startLine' => 405,
                'endLine' => 405,
                'startTokenPos' => 1287,
                'startFilePos' => 8351,
                'endTokenPos' => 1287,
                'endFilePos' => 8351,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 405,
            'endLine' => 405,
            'startColumn' => 51,
            'endColumn' => 60,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Assert a given amount of sleeping occurred a specific number of times.
 *
 * @param  \\Closure  $expected
 * @param  int  $times
 * @return void
 */',
        'startLine' => 405,
        'endLine' => 414,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'assertSleptTimes' => 
      array (
        'name' => 'assertSleptTimes',
        'parameters' => 
        array (
          'expected' => 
          array (
            'name' => 'expected',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 422,
            'endLine' => 422,
            'startColumn' => 45,
            'endColumn' => 53,
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
 * Assert sleeping occurred a given number of times.
 *
 * @param  int  $expected
 * @return void
 */',
        'startLine' => 422,
        'endLine' => 425,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'assertSequence' => 
      array (
        'name' => 'assertSequence',
        'parameters' => 
        array (
          'sequence' => 
          array (
            'name' => 'sequence',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 433,
            'endLine' => 433,
            'startColumn' => 43,
            'endColumn' => 51,
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
 * Assert the given sleep sequence was encountered.
 *
 * @param  array  $sequence
 * @return void
 */',
        'startLine' => 433,
        'endLine' => 466,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'assertNeverSlept' => 
      array (
        'name' => 'assertNeverSlept',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Assert that no sleeping occurred.
 *
 * @return void
 */',
        'startLine' => 473,
        'endLine' => 476,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'assertInsomniac' => 
      array (
        'name' => 'assertInsomniac',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Assert that no sleeping occurred.
 *
 * @return void
 */',
        'startLine' => 483,
        'endLine' => 497,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'shouldNotSleep' => 
      array (
        'name' => 'shouldNotSleep',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that the instance should not sleep.
 *
 * @return $this
 */',
        'startLine' => 504,
        'endLine' => 509,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'when' => 
      array (
        'name' => 'when',
        'parameters' => 
        array (
          'condition' => 
          array (
            'name' => 'condition',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 517,
            'endLine' => 517,
            'startColumn' => 26,
            'endColumn' => 35,
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
 * Only sleep when the given condition is true.
 *
 * @param  (\\Closure($this): bool)|bool  $condition
 * @return $this
 */',
        'startLine' => 517,
        'endLine' => 522,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'unless' => 
      array (
        'name' => 'unless',
        'parameters' => 
        array (
          'condition' => 
          array (
            'name' => 'condition',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 530,
            'endLine' => 530,
            'startColumn' => 28,
            'endColumn' => 37,
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
 * Don\'t sleep when the given condition is true.
 *
 * @param  (\\Closure($this): bool)|bool  $condition
 * @return $this
 */',
        'startLine' => 530,
        'endLine' => 533,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'whenFakingSleep' => 
      array (
        'name' => 'whenFakingSleep',
        'parameters' => 
        array (
          'callback' => 
          array (
            'name' => 'callback',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 541,
            'endLine' => 541,
            'startColumn' => 44,
            'endColumn' => 52,
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
 * Specify a callback that should be invoked when faking sleep within a test.
 *
 * @param  callable  $callback
 * @return void
 */',
        'startLine' => 541,
        'endLine' => 544,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
        'aliasName' => NULL,
      ),
      'syncWithCarbon' => 
      array (
        'name' => 'syncWithCarbon',
        'parameters' => 
        array (
          'value' => 
          array (
            'name' => 'value',
            'default' => 
            array (
              'code' => 'true',
              'attributes' => 
              array (
                'startLine' => 551,
                'endLine' => 551,
                'startTokenPos' => 1892,
                'startFilePos' => 12368,
                'endTokenPos' => 1892,
                'endFilePos' => 12371,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 551,
            'endLine' => 551,
            'startColumn' => 43,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Indicate that Carbon\'s "now" should be kept in sync when sleeping.
 *
 * @return void
 */',
        'startLine' => 551,
        'endLine' => 554,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'Illuminate\\Support',
        'declaringClassName' => 'Illuminate\\Support\\Sleep',
        'implementingClassName' => 'Illuminate\\Support\\Sleep',
        'currentClassName' => 'Illuminate\\Support\\Sleep',
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