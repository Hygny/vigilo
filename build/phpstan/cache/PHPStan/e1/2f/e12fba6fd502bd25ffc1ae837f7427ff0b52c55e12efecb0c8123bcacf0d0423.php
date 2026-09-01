<?php declare(strict_types = 1);

// osfsl-C:\laragon\www\vigilo\app\Livewire\Portfolios\Show.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Livewire\Portfolios\Show
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-96f8f58eec789e9951138584378ed601b4cc4d419bd76919636a94526c5aba26-8.4.24-6.70.0.3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Livewire\\Portfolios\\Show',
        'filename' => 'C:/laragon/www/vigilo/app/Livewire/Portfolios/Show.php',
      ),
    ),
    'namespace' => 'App\\Livewire\\Portfolios',
    'name' => 'App\\Livewire\\Portfolios\\Show',
    'shortName' => 'Show',
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
              'startLine' => 28,
              'endLine' => 28,
              'startTokenPos' => 118,
              'startFilePos' => 804,
              'endTokenPos' => 118,
              'endFilePos' => 816,
            ),
          ),
        ),
      ),
    ),
    'startLine' => 28,
    'endLine' => 374,
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
      2 => 'Livewire\\WithFileUploads',
      3 => 'Livewire\\WithPagination',
    ),
    'immediateConstants' => 
    array (
      'FILTERS' => 
      array (
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'name' => 'FILTERS',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'all\' => \'Todos\', \'pending\' => \'Pendentes\', \'ok\' => \'Atualizados\', \'nao_encontrado\' => \'Não encontrados\', \'erro\' => \'Erros\']',
          'attributes' => 
          array (
            'startLine' => 54,
            'endLine' => 60,
            'startTokenPos' => 250,
            'startFilePos' => 1597,
            'endTokenPos' => 287,
            'endFilePos' => 1769,
          ),
        ),
        'docComment' => '/** Rótulos dos filtros, na ordem em que aparecem na UI. */',
        'attributes' => 
        array (
        ),
        'startLine' => 54,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 6,
      ),
    ),
    'immediateProperties' => 
    array (
      'portfolio' => 
      array (
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'name' => 'portfolio',
        'modifiers' => 1,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\Portfolio',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 32,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'cnpj' => 
      array (
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'name' => 'cnpj',
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
          'code' => '\'\'',
          'attributes' => 
          array (
            'startLine' => 36,
            'endLine' => 36,
            'startTokenPos' => 168,
            'startFilePos' => 1042,
            'endTokenPos' => 168,
            'endFilePos' => 1043,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'Livewire\\Attributes\\Validate',
            'isRepeated' => false,
            'arguments' => 
            array (
              0 => 
              array (
                'code' => '\'required|string\'',
                'attributes' => 
                array (
                  'startLine' => 35,
                  'endLine' => 35,
                  'startTokenPos' => 156,
                  'startFilePos' => 996,
                  'endTokenPos' => 156,
                  'endFilePos' => 1012,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 35,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 29,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'label' => 
      array (
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'name' => 'label',
        'modifiers' => 1,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'string',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 39,
            'startTokenPos' => 187,
            'startFilePos' => 1118,
            'endTokenPos' => 187,
            'endFilePos' => 1121,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'Livewire\\Attributes\\Validate',
            'isRepeated' => false,
            'arguments' => 
            array (
              0 => 
              array (
                'code' => '\'nullable|string|max:255\'',
                'attributes' => 
                array (
                  'startLine' => 38,
                  'endLine' => 38,
                  'startTokenPos' => 174,
                  'startFilePos' => 1062,
                  'endTokenPos' => 174,
                  'endFilePos' => 1086,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 38,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 33,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'csv' => 
      array (
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'name' => 'csv',
        'modifiers' => 1,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'Livewire\\Features\\SupportFileUploads\\TemporaryUploadedFile',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 42,
            'endLine' => 42,
            'startTokenPos' => 206,
            'startFilePos' => 1208,
            'endTokenPos' => 206,
            'endFilePos' => 1211,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'Livewire\\Attributes\\Validate',
            'isRepeated' => false,
            'arguments' => 
            array (
              0 => 
              array (
                'code' => '\'nullable|file|max:5120\'',
                'attributes' => 
                array (
                  'startLine' => 41,
                  'endLine' => 41,
                  'startTokenPos' => 193,
                  'startFilePos' => 1140,
                  'endTokenPos' => 193,
                  'endFilePos' => 1163,
                ),
              ),
            ),
          ),
        ),
        'startLine' => 41,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 46,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'importReport' => 
      array (
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'name' => 'importReport',
        'modifiers' => 1,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'array',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'default' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 47,
            'endLine' => 47,
            'startTokenPos' => 220,
            'startFilePos' => 1368,
            'endTokenPos' => 220,
            'endFilePos' => 1371,
          ),
        ),
        'docComment' => '/**
 * @var array{imported: int, rejected: list<array{line: int, value: string, reason: string}>}|null
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 47,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'statusFilter' => 
      array (
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'name' => 'statusFilter',
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
            'startLine' => 51,
            'endLine' => 51,
            'startTokenPos' => 237,
            'startFilePos' => 1496,
            'endTokenPos' => 237,
            'endFilePos' => 1500,
          ),
        ),
        'docComment' => '/** Filtro por status da coleta: all|pending|ok|nao_encontrado|erro. */',
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
        'startLine' => 50,
        'endLine' => 51,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'scheduleDays' => 
      array (
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'name' => 'scheduleDays',
        'modifiers' => 1,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '[]',
          'attributes' => 
          array (
            'startLine' => 67,
            'endLine' => 67,
            'startTokenPos' => 300,
            'startFilePos' => 1934,
            'endTokenPos' => 301,
            'endFilePos' => 1935,
          ),
        ),
        'docComment' => '/**
 * Dias do mês (1–31) selecionados para o agendamento mensal do portfólio.
 *
 * @var list<int>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 67,
        'endLine' => 67,
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
      'mount' => 
      array (
        'name' => 'mount',
        'parameters' => 
        array (
          'portfolio' => 
          array (
            'name' => 'portfolio',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Portfolio',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 69,
            'endLine' => 69,
            'startColumn' => 27,
            'endColumn' => 46,
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
        'startLine' => 69,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'toggleScheduleDay' => 
      array (
        'name' => 'toggleScheduleDay',
        'parameters' => 
        array (
          'day' => 
          array (
            'name' => 'day',
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
            'startLine' => 76,
            'endLine' => 76,
            'startColumn' => 39,
            'endColumn' => 46,
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
        'startLine' => 76,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'applyPreset' => 
      array (
        'name' => 'applyPreset',
        'parameters' => 
        array (
          'preset' => 
          array (
            'name' => 'preset',
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
            'startLine' => 93,
            'endLine' => 93,
            'startColumn' => 33,
            'endColumn' => 46,
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
        'docComment' => '/**
 * Aplica um preset comum de agendamento. Não persiste: o usuário confirma
 * com "Salvar".
 */',
        'startLine' => 93,
        'endLine' => 102,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'clearScheduleDays' => 
      array (
        'name' => 'clearScheduleDays',
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
        'startLine' => 104,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'discardScheduleChanges' => 
      array (
        'name' => 'discardScheduleChanges',
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
        'docComment' => '/**
 * Descarta alterações não salvas, voltando ao agendamento persistido.
 */',
        'startLine' => 112,
        'endLine' => 115,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'saveSchedule' => 
      array (
        'name' => 'saveSchedule',
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
        'startLine' => 117,
        'endLine' => 129,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'normalizedScheduleDays' => 
      array (
        'name' => 'normalizedScheduleDays',
        'parameters' => 
        array (
          'days' => 
          array (
            'name' => 'days',
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
            'startLine' => 138,
            'endLine' => 138,
            'startColumn' => 45,
            'endColumn' => 55,
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
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Sanitiza a lista de dias (o cliente pode setar $scheduleDays direto):
 * só 1–31, sem repetição, ordenada e reindexada como lista.
 *
 * @param  array<array-key, int|string>  $days
 * @return list<int>
 */',
        'startLine' => 138,
        'endLine' => 150,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'nextScheduledRun' => 
      array (
        'name' => 'nextScheduledRun',
        'parameters' => 
        array (
          'days' => 
          array (
            'name' => 'days',
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
            'startLine' => 159,
            'endLine' => 159,
            'startColumn' => 39,
            'endColumn' => 49,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'Carbon\\CarbonImmutable',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Próxima data em que o agendamento salvo dispara — espelha a regra de
 * RunDuePortfolioSchedules (um dia > dias-do-mês escorrega para o último).
 * Retorna null se não há agendamento.
 *
 * @param  list<int>  $days
 */',
        'startLine' => 159,
        'endLine' => 183,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'scheduleFiresOn' => 
      array (
        'name' => 'scheduleFiresOn',
        'parameters' => 
        array (
          'days' => 
          array (
            'name' => 'days',
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
            'startLine' => 188,
            'endLine' => 188,
            'startColumn' => 38,
            'endColumn' => 48,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'date' => 
          array (
            'name' => 'date',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Carbon\\CarbonImmutable',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 188,
            'endLine' => 188,
            'startColumn' => 51,
            'endColumn' => 71,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
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
        'docComment' => '/**
 * @param  list<int>  $days
 */',
        'startLine' => 188,
        'endLine' => 199,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'addCompany' => 
      array (
        'name' => 'addCompany',
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
        'startLine' => 201,
        'endLine' => 227,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'import' => 
      array (
        'name' => 'import',
        'parameters' => 
        array (
          'importer' => 
          array (
            'name' => 'importer',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\CompanyImporter',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 229,
            'endLine' => 229,
            'startColumn' => 28,
            'endColumn' => 52,
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
        'startLine' => 229,
        'endLine' => 249,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'queueRefresh' => 
      array (
        'name' => 'queueRefresh',
        'parameters' => 
        array (
          'companyId' => 
          array (
            'name' => 'companyId',
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
            'startLine' => 251,
            'endLine' => 251,
            'startColumn' => 34,
            'endColumn' => 47,
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
        'startLine' => 251,
        'endLine' => 260,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'queueRefreshAll' => 
      array (
        'name' => 'queueRefreshAll',
        'parameters' => 
        array (
          'refresher' => 
          array (
            'name' => 'refresher',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Actions\\QueuePortfolioRefresh',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 262,
            'endLine' => 262,
            'startColumn' => 37,
            'endColumn' => 68,
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
        'startLine' => 262,
        'endLine' => 269,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'setStatusFilter' => 
      array (
        'name' => 'setStatusFilter',
        'parameters' => 
        array (
          'status' => 
          array (
            'name' => 'status',
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
            'startLine' => 271,
            'endLine' => 271,
            'startColumn' => 37,
            'endColumn' => 50,
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
        'startLine' => 271,
        'endLine' => 275,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'reprocessFiltered' => 
      array (
        'name' => 'reprocessFiltered',
        'parameters' => 
        array (
          'refresher' => 
          array (
            'name' => 'refresher',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Actions\\QueuePortfolioRefresh',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 281,
            'endLine' => 281,
            'startColumn' => 39,
            'endColumn' => 70,
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
        'docComment' => '/**
 * Reprocessa (revalida) apenas o subconjunto do filtro ativo — ex.: só os
 * CNPJs com erro, ou só os não encontrados.
 */',
        'startLine' => 281,
        'endLine' => 289,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'statusConstraint' => 
      array (
        'name' => 'statusConstraint',
        'parameters' => 
        array (
          'filter' => 
          array (
            'name' => 'filter',
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
            'startLine' => 296,
            'endLine' => 296,
            'startColumn' => 39,
            'endColumn' => 52,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'callable',
                  'isIdentifier' => true,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Constraint de query correspondente ao filtro de status (null = sem filtro).
 *
 * @return (callable(Builder<MonitoredCompany>): void)|null
 */',
        'startLine' => 296,
        'endLine' => 307,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'removeCompany' => 
      array (
        'name' => 'removeCompany',
        'parameters' => 
        array (
          'companyId' => 
          array (
            'name' => 'companyId',
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
            'startLine' => 309,
            'endLine' => 309,
            'startColumn' => 35,
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
        'startLine' => 309,
        'endLine' => 316,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
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
        'startLine' => 318,
        'endLine' => 365,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
        'aliasName' => NULL,
      ),
      'baseQuery' => 
      array (
        'name' => 'baseQuery',
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
 * @return Builder<MonitoredCompany>
 */',
        'startLine' => 370,
        'endLine' => 373,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Livewire\\Portfolios',
        'declaringClassName' => 'App\\Livewire\\Portfolios\\Show',
        'implementingClassName' => 'App\\Livewire\\Portfolios\\Show',
        'currentClassName' => 'App\\Livewire\\Portfolios\\Show',
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