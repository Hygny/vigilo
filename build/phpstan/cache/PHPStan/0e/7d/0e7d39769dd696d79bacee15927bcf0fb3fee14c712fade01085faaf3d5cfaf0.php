<?php declare(strict_types = 1);

// odsl-C:\laragon\www\vigilo\app\Actions\QueuePortfolioRefresh.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Actions\QueuePortfolioRefresh
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.3-8.4.24-9b4156b19ae2732fd8de8cb3548d2a0bfdd456adee1ce38e57710f6df7c1b10b',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Actions\\QueuePortfolioRefresh',
        'filename' => 'C:/laragon/www/vigilo/app/Actions/QueuePortfolioRefresh.php',
      ),
    ),
    'namespace' => 'App\\Actions',
    'name' => 'App\\Actions\\QueuePortfolioRefresh',
    'shortName' => 'QueuePortfolioRefresh',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 32,
    'docComment' => '/**
 * Marca as empresas-alvo de um portfólio como "pendentes" (limpa status/erro) e
 * (re)enfileira um refresh para cada uma, em chunks. A dedup de fila
 * (ShouldBeUnique no job) evita jobs repetidos.
 *
 * Usado tanto ao atualizar pela lista de portfólios quanto de dentro do
 * portfólio, garantindo estado idêntico (pendente + progresso) nos dois caminhos.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 20,
    'endLine' => 57,
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
    ),
    'immediateMethods' => 
    array (
      'handle' => 
      array (
        'name' => 'handle',
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
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 28,
            'endColumn' => 47,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'constrain' => 
          array (
            'name' => 'constrain',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 26,
                'endLine' => 26,
                'startTokenPos' => 65,
                'startFilePos' => 877,
                'endTokenPos' => 65,
                'endFilePos' => 880,
              ),
            ),
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
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 50,
            'endColumn' => 76,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  (callable(Builder<MonitoredCompany>): void)|null  $constrain  filtro opcional (ex.: só erros/não encontrados)
 * @return int quantas empresas foram enfileiradas
 */',
        'startLine' => 26,
        'endLine' => 56,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Actions',
        'declaringClassName' => 'App\\Actions\\QueuePortfolioRefresh',
        'implementingClassName' => 'App\\Actions\\QueuePortfolioRefresh',
        'currentClassName' => 'App\\Actions\\QueuePortfolioRefresh',
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