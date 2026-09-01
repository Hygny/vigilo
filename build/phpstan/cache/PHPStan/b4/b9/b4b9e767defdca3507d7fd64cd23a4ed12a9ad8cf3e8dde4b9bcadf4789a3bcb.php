<?php declare(strict_types = 1);

// odsl-C:\laragon\www\vigilo\app\Livewire\Alerts\Inbox.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Livewire\Alerts\Inbox
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-3e588b610f8f02cfd7c45aaab2762b3dc1953d735be91c7487ec01ea60f252ee',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Livewire\\Alerts\\Inbox',
        'filename' => 'C:/laragon/www/vigilo/app/Livewire/Alerts/Inbox.php',
      ),
    ),
    'namespace' => 'App\\Livewire\\Alerts',
    'name' => 'App\\Livewire\\Alerts\\Inbox',
    'shortName' => 'Inbox',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
      0 => 
      array (
        'name' => 'Livewire\\Attributes\\Layout',
        'isRepeated' => false,
        'arguments' => 
        array (
          0 => 
          array (
            'code' => '\'layouts.app\'',
            'attributes' => 
            array (
              'startLine' => 19,
              'endLine' => 19,
              'startTokenPos' => 73,
              'startFilePos' => 474,
              'endTokenPos' => 73,
              'endFilePos' => 486,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 19,
    'endLine' => 92,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Livewire\\Component',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
      1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'severity' => 
      array (
        'declaringClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'implementingClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'name' => 'severity',
        'modifiers' => 1,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '\'all\'',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 109,
            'startFilePos' => 681,
            'endTokenPos' => 109,
            'endFilePos' => 685,
          ),
        ),
        'docComment' => '/** Severity filter: \'all\' or a Severity value. */',
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'Livewire\\Attributes\\Url',
            'isRepeated' => false,
            'arguments' => 
            array (
            ),
          ),
        ),
        'startLine' => 25,
        'endLine' => 26,
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
      'setSeverity' => 
      array (
        'name' => 'setSeverity',
        'parameters' => 
        array (
          'severity' => 
          array (
            'name' => 'severity',
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
            'startLine' => 28,
            'endLine' => 28,
            'startColumn' => 33,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 28,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Alerts',
        'declaringClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'implementingClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'currentClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'aliasName' => NULL,
      ),
      'acknowledge' => 
      array (
        'name' => 'acknowledge',
        'parameters' => 
        array (
          'id' => 
          array (
            'name' => 'id',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 33,
            'endLine' => 33,
            'startColumn' => 33,
            'endColumn' => 39,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 33,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Alerts',
        'declaringClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'implementingClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'currentClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'aliasName' => NULL,
      ),
      'acknowledgeAll' => 
      array (
        'name' => 'acknowledgeAll',
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
        'startLine' => 42,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Alerts',
        'declaringClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'implementingClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'currentClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'aliasName' => NULL,
      ),
      'render' => 
      array (
        'name' => 'render',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Contracts\\View\\View',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 50,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Alerts',
        'declaringClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'implementingClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'currentClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'aliasName' => NULL,
      ),
      'openFilteredEvents' => 
      array (
        'name' => 'openFilteredEvents',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Alertas em aberto (não reconhecidos) do filtro de severidade atual.
 *
 * @return Builder<ChangeEvent>
 */',
        'startLine' => 66,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Livewire\\Alerts',
        'declaringClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'implementingClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'currentClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'aliasName' => NULL,
      ),
      'scopedEvents' => 
      array (
        'name' => 'scopedEvents',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Builder',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Change events belonging to the current organization only.
 *
 * @return Builder<ChangeEvent>
 */',
        'startLine' => 82,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Livewire\\Alerts',
        'declaringClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'implementingClassName' => 'App\\Livewire\\Alerts\\Inbox',
        'currentClassName' => 'App\\Livewire\\Alerts\\Inbox',
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