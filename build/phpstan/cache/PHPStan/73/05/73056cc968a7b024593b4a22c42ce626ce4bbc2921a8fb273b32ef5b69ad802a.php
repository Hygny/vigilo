<?php declare(strict_types = 1);

// osfsl-C:/laragon/www/vigilo/vendor/composer/../livewire/livewire/src/Features/SupportConsoleCommands/Commands/MakeCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Livewire\Features\SupportConsoleCommands\Commands\MakeCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-e40a776d9c5a929ddc04fe3c54f2b8c1dfe0a0b68477c758f114772a4e75254a-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'filename' => 'C:/laragon/www/vigilo/vendor/composer/../livewire/livewire/src/Features/SupportConsoleCommands/Commands/MakeCommand.php',
      ),
    ),
    'namespace' => 'Livewire\\Features\\SupportConsoleCommands\\Commands',
    'name' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
    'shortName' => 'MakeCommand',
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
        'name' => 'Symfony\\Component\\Console\\Attribute\\AsCommand',
        'isRepeated' => false,
        'arguments' => 
        array (
          'name' => 
          array (
            'code' => '\'livewire:make\'',
            'attributes' => 
            array (
              'startLine' => 13,
              'endLine' => 13,
              'startTokenPos' => 52,
              'startFilePos' => 414,
              'endTokenPos' => 52,
              'endFilePos' => 428,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 13,
    'endLine' => 257,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\FileManipulationCommand',
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Console\\PromptsForMissingInput',
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'signature' => 
      array (
        'declaringClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'implementingClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'livewire:make {name} {--force} {--inline} {--test} {--pest} {--stub= : If you have several stubs, stored in subfolders }\'',
          'attributes' => 
          array (
            'startLine' => 16,
            'endLine' => 16,
            'startTokenPos' => 76,
            'startFilePos' => 545,
            'endTokenPos' => 76,
            'endFilePos' => 666,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 16,
        'endLine' => 16,
        'startColumn' => 5,
        'endColumn' => 150,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'implementingClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Create a new Livewire component\'',
          'attributes' => 
          array (
            'startLine' => 18,
            'endLine' => 18,
            'startTokenPos' => 85,
            'startFilePos' => 699,
            'endTokenPos' => 85,
            'endFilePos' => 731,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 18,
        'endLine' => 18,
        'startColumn' => 5,
        'endColumn' => 63,
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
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 20,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Livewire\\Features\\SupportConsoleCommands\\Commands',
        'declaringClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'implementingClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'currentClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'aliasName' => NULL,
      ),
      'createClass' => 
      array (
        'name' => 'createClass',
        'parameters' => 
        array (
          'force' => 
          array (
            'name' => 'force',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 75,
                'endLine' => 75,
                'startTokenPos' => 512,
                'startFilePos' => 2692,
                'endTokenPos' => 512,
                'endFilePos' => 2696,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 75,
            'endLine' => 75,
            'startColumn' => 36,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'inline' => 
          array (
            'name' => 'inline',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 75,
                'endLine' => 75,
                'startTokenPos' => 519,
                'startFilePos' => 2709,
                'endTokenPos' => 519,
                'endFilePos' => 2713,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 75,
            'endLine' => 75,
            'startColumn' => 52,
            'endColumn' => 66,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 75,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Livewire\\Features\\SupportConsoleCommands\\Commands',
        'declaringClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'implementingClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'currentClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'aliasName' => NULL,
      ),
      'createView' => 
      array (
        'name' => 'createView',
        'parameters' => 
        array (
          'force' => 
          array (
            'name' => 'force',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 93,
                'endLine' => 93,
                'startTokenPos' => 633,
                'startFilePos' => 3260,
                'endTokenPos' => 633,
                'endFilePos' => 3264,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 93,
            'endLine' => 93,
            'startColumn' => 35,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'inline' => 
          array (
            'name' => 'inline',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 93,
                'endLine' => 93,
                'startTokenPos' => 640,
                'startFilePos' => 3277,
                'endTokenPos' => 640,
                'endFilePos' => 3281,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 93,
            'endLine' => 93,
            'startColumn' => 51,
            'endColumn' => 65,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 93,
        'endLine' => 111,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Livewire\\Features\\SupportConsoleCommands\\Commands',
        'declaringClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'implementingClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'currentClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'aliasName' => NULL,
      ),
      'createTest' => 
      array (
        'name' => 'createTest',
        'parameters' => 
        array (
          'force' => 
          array (
            'name' => 'force',
            'default' => 
            array (
              'code' => 'false',
              'attributes' => 
              array (
                'startLine' => 113,
                'endLine' => 113,
                'startTokenPos' => 760,
                'startFilePos' => 3783,
                'endTokenPos' => 760,
                'endFilePos' => 3787,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 113,
            'endLine' => 113,
            'startColumn' => 35,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => true,
          ),
          'testType' => 
          array (
            'name' => 'testType',
            'default' => 
            array (
              'code' => '\'phpunit\'',
              'attributes' => 
              array (
                'startLine' => 113,
                'endLine' => 113,
                'startTokenPos' => 767,
                'startFilePos' => 3802,
                'endTokenPos' => 767,
                'endFilePos' => 3810,
              ),
            ),
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 113,
            'endLine' => 113,
            'startColumn' => 51,
            'endColumn' => 71,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 113,
        'endLine' => 129,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Livewire\\Features\\SupportConsoleCommands\\Commands',
        'declaringClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'implementingClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'currentClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'aliasName' => NULL,
      ),
      'isClassNameValid' => 
      array (
        'name' => 'isClassNameValid',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 131,
            'endLine' => 131,
            'startColumn' => 38,
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
        'docComment' => NULL,
        'startLine' => 131,
        'endLine' => 134,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Livewire\\Features\\SupportConsoleCommands\\Commands',
        'declaringClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'implementingClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'currentClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'aliasName' => NULL,
      ),
      'isReservedClassName' => 
      array (
        'name' => 'isReservedClassName',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => NULL,
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 136,
            'endLine' => 136,
            'startColumn' => 41,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 136,
        'endLine' => 139,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Livewire\\Features\\SupportConsoleCommands\\Commands',
        'declaringClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'implementingClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'currentClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'aliasName' => NULL,
      ),
      'afterPromptingForMissingArguments' => 
      array (
        'name' => 'afterPromptingForMissingArguments',
        'parameters' => 
        array (
          'input' => 
          array (
            'name' => 'input',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Symfony\\Component\\Console\\Input\\InputInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 141,
            'endLine' => 141,
            'startColumn' => 58,
            'endColumn' => 78,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'output' => 
          array (
            'name' => 'output',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Symfony\\Component\\Console\\Output\\OutputInterface',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 141,
            'endLine' => 141,
            'startColumn' => 81,
            'endColumn' => 103,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 141,
        'endLine' => 174,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'Livewire\\Features\\SupportConsoleCommands\\Commands',
        'declaringClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'implementingClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'currentClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'aliasName' => NULL,
      ),
      'getReservedName' => 
      array (
        'name' => 'getReservedName',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 176,
        'endLine' => 255,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'Livewire\\Features\\SupportConsoleCommands\\Commands',
        'declaringClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'implementingClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
        'currentClassName' => 'Livewire\\Features\\SupportConsoleCommands\\Commands\\MakeCommand',
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