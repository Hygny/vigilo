<?php declare(strict_types = 1);

return [
	'lastFullAnalysisTime' => 1788291452,
	'meta' => array (
  'cacheVersion' => 'v13-packageDependencies',
  'phpstanVersion' => '2.2.8',
  'fnsr' => false,
  'metaExtensions' => 
  array (
  ),
  'phpVersion' => 80424,
  'projectConfig' => '{conditionalTags: {Larastan\\Larastan\\Rules\\NoEnvCallsOutsideOfConfigRule: {phpstan.rules.rule: %noEnvCallsOutsideOfConfig%}, Larastan\\Larastan\\Rules\\NoModelMakeRule: {phpstan.rules.rule: %noModelMake%}, Larastan\\Larastan\\Rules\\NoUnnecessaryCollectionCallRule: {phpstan.rules.rule: %noUnnecessaryCollectionCall%}, Larastan\\Larastan\\Rules\\NoUnnecessaryEnumerableToArrayCallsRule: {phpstan.rules.rule: %noUnnecessaryEnumerableToArrayCalls%}, Larastan\\Larastan\\Rules\\OctaneCompatibilityRule: {phpstan.rules.rule: %checkOctaneCompatibility%}, Larastan\\Larastan\\Rules\\UnusedViewsRule: {phpstan.rules.rule: %checkUnusedViews%}, Larastan\\Larastan\\Rules\\NoMissingTranslationsRule: {phpstan.rules.rule: %checkMissingTranslations%}, Larastan\\Larastan\\Rules\\ModelAppendsRule: {phpstan.rules.rule: %checkModelAppends%}, Larastan\\Larastan\\Rules\\NoPublicModelScopeAndAccessorRule: {phpstan.rules.rule: %checkModelMethodVisibility%}, Larastan\\Larastan\\Rules\\NoAuthFacadeInRequestScopeRule: {phpstan.rules.rule: %checkAuthCallsWhenInRequestScope%}, Larastan\\Larastan\\Rules\\NoAuthHelperInRequestScopeRule: {phpstan.rules.rule: %checkAuthCallsWhenInRequestScope%}, Larastan\\Larastan\\ReturnTypes\\Helpers\\EnvFunctionDynamicFunctionReturnTypeExtension: {phpstan.broker.dynamicFunctionReturnTypeExtension: %generalizeEnvReturnType%}, Larastan\\Larastan\\ReturnTypes\\Helpers\\ConfigFunctionDynamicFunctionReturnTypeExtension: {phpstan.broker.dynamicFunctionReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\ReturnTypes\\ConfigRepositoryDynamicMethodReturnTypeExtension: {phpstan.broker.dynamicMethodReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\ReturnTypes\\ConfigFacadeCollectionDynamicStaticMethodReturnTypeExtension: {phpstan.broker.dynamicStaticMethodReturnTypeExtension: %checkConfigTypes%}, Larastan\\Larastan\\Rules\\ConfigCollectionRule: {phpstan.rules.rule: %checkConfigTypes%}}, parameters: {universalObjectCratesClasses: [Illuminate\\Http\\Request, Illuminate\\Support\\Optional], earlyTerminatingFunctionCalls: [abort, dd], mixinExcludeClasses: [Eloquent], bootstrapFiles: [bootstrap.php], checkOctaneCompatibility: false, noEnvCallsOutsideOfConfig: true, noModelMake: true, noUnnecessaryCollectionCall: true, noUnnecessaryCollectionCallOnly: [], noUnnecessaryCollectionCallExcept: [], noUnnecessaryEnumerableToArrayCalls: false, squashedMigrationsPath: [], databaseMigrationsPath: [], disableMigrationScan: false, disableSchemaScan: false, configDirectories: [], viewDirectories: [], translationDirectories: [], checkModelProperties: false, checkUnusedViews: false, checkMissingTranslations: false, checkModelAppends: true, checkModelMethodVisibility: false, generalizeEnvReturnType: false, checkConfigTypes: false, checkAuthCallsWhenInRequestScope: false, parseModelCastsMethod: false, enableMigrationCache: false, level: 8, paths: [C:\\laragon\\www\\vigilo\\app, C:\\laragon\\www\\vigilo\\database\\factories, C:\\laragon\\www\\vigilo\\database\\seeders], tmpDir: C:\\laragon\\www\\vigilo\\build\\phpstan}, rules: [Larastan\\Larastan\\Rules\\UselessConstructs\\NoUselessWithFunctionCallsRule, Larastan\\Larastan\\Rules\\UselessConstructs\\NoUselessValueFunctionCallsRule, Larastan\\Larastan\\Rules\\DeferrableServiceProviderMissingProvidesRule, Larastan\\Larastan\\Rules\\ConsoleCommand\\UndefinedArgumentOrOptionRule], services: {{class: Larastan\\Larastan\\Methods\\RelationForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ModelForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\EloquentBuilderForwardsCallsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\HigherOrderTapProxyExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\HigherOrderCollectionProxyExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\StorageMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ContractsMethodsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\FacadesMethodsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ManagersMethodsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\AuthsMethodsExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ModelFactoryMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\RedirectResponseMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\MacroMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Methods\\ViewWithMethodsClassReflectionExtension, tags: [phpstan.broker.methodsClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\ModelAccessorExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\ModelPropertyExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\Properties\\HigherOrderCollectionProxyPropertyExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\HigherOrderTapProxyExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Contracts\\Container\\Container}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Container\\Container}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerArrayAccessDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {className: Illuminate\\Contracts\\Foundation\\Application}}, {class: Larastan\\Larastan\\Properties\\ModelRelationsExtension, tags: [phpstan.broker.propertiesClassReflectionExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelOnlyDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelFactoryDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ModelDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AuthExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\GuardDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AuthManagerExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\DateExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\GuardExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestFileExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestRouteExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RequestUserExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\EloquentBuilderExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\RelationCollectionExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TestCaseExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Support\\CollectionHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\AuthExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\CollectExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\NowAndTodayExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ResponseExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ValidatorExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\LiteralExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\CollectionFilterRejectDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\CollectionWhereNotNullDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\NewModelQueryDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\FactoryDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: abort, negate: false}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: abort, negate: true}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: throw, negate: false}}, {class: Larastan\\Larastan\\Types\\AbortIfFunctionTypeSpecifyingExtension, tags: [phpstan.typeSpecifier.functionTypeSpecifyingExtension], arguments: {methodName: throw, negate: true}}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\AppExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ValueExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\StrExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\TapExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\StorageDynamicStaticMethodReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\GenericEloquentCollectionTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Types\\ViewStringTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Rules\\OctaneCompatibilityRule}, {class: Larastan\\Larastan\\Rules\\NoEnvCallsOutsideOfConfigRule, arguments: {configDirectories: %configDirectories%}}, {class: Larastan\\Larastan\\Rules\\NoModelMakeRule}, {class: Larastan\\Larastan\\Rules\\NoUnnecessaryCollectionCallRule, arguments: {onlyMethods: %noUnnecessaryCollectionCallOnly%, excludeMethods: %noUnnecessaryCollectionCallExcept%}}, {class: Larastan\\Larastan\\Rules\\NoUnnecessaryEnumerableToArrayCallsRule}, {class: Larastan\\Larastan\\Rules\\ModelAppendsRule}, {class: Larastan\\Larastan\\Rules\\NoPublicModelScopeAndAccessorRule}, {class: Larastan\\Larastan\\Types\\GenericEloquentBuilderTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {class: Illuminate\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\AppEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension], arguments: {class: Illuminate\\Contracts\\Foundation\\Application}}, {class: Larastan\\Larastan\\ReturnTypes\\AppFacadeEnvironmentReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Types\\ModelProperty\\ModelPropertyTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension], arguments: {active: %checkModelProperties%}}, {class: Larastan\\Larastan\\Types\\CollectionOf\\CollectionOfTypeNodeResolverExtension, tags: [phpstan.phpDoc.typeNodeResolverExtension]}, {class: Larastan\\Larastan\\Properties\\MigrationHelper, arguments: {databaseMigrationPath: %databaseMigrationsPath%, disableMigrationScan: %disableMigrationScan%, parser: @migrationsParser, reflectionProvider: @reflectionProvider}}, iamcalSqlParser: {class: Larastan\\Larastan\\SQL\\IamcalSqlParser, autowired: false}, sqlParserFactory: {class: Larastan\\Larastan\\SQL\\SqlParserFactory, arguments: {iamcalSqlParser: @iamcalSqlParser}}, sqlParser: {type: Larastan\\Larastan\\SQL\\SqlParser, factory: [@sqlParserFactory, create]}, {class: Larastan\\Larastan\\Properties\\SquashedMigrationHelper, arguments: {schemaPaths: %squashedMigrationsPath%, disableSchemaScan: %disableSchemaScan%}}, {class: Larastan\\Larastan\\Properties\\ModelCastHelper, arguments: {parser: @currentPhpVersionSimpleDirectParser, parseModelCastsMethod: %parseModelCastsMethod%}}, {class: Larastan\\Larastan\\Properties\\MigrationCache, arguments: {cacheDirectory: %tmpDir%, enabled: %enableMigrationCache%}}, {class: Larastan\\Larastan\\Properties\\ModelPropertyHelper}, {class: Larastan\\Larastan\\Rules\\ModelRuleHelper}, {class: Larastan\\Larastan\\Methods\\BuilderHelper, arguments: {checkProperties: %checkModelProperties%}}, {class: Larastan\\Larastan\\Rules\\RelationExistenceRule, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Rules\\CheckDispatchArgumentTypesCompatibleWithClassConstructorRule, arguments: {dispatchableClass: Illuminate\\Foundation\\Bus\\Dispatchable}, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Rules\\CheckDispatchArgumentTypesCompatibleWithClassConstructorRule, arguments: {dispatchableClass: Illuminate\\Foundation\\Events\\Dispatchable}, tags: [phpstan.rules.rule]}, {class: Larastan\\Larastan\\Properties\\Schema\\MySqlDataTypeToPhpTypeConverter}, {class: Larastan\\Larastan\\LarastanStubFilesExtension, tags: [phpstan.stubFilesExtension]}, {class: Larastan\\Larastan\\Rules\\UnusedViewsRule}, {class: Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedEmailViewCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewMakeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewFacadeMakeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedRouteFacadeViewCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedViewInAnotherViewCollector}, {class: Larastan\\Larastan\\Support\\ViewFileHelper, arguments: {viewDirectories: %viewDirectories%}}, {class: Larastan\\Larastan\\Support\\ViewParser, arguments: {parser: @currentPhpVersionSimpleDirectParser}}, {class: Larastan\\Larastan\\Rules\\NoMissingTranslationsRule, arguments: {translationDirectories: %translationDirectories%}}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationFunctionCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationTranslatorCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationFacadeCollector, tags: [phpstan.collector]}, {class: Larastan\\Larastan\\Collectors\\UsedTranslationViewCollector}, {class: Larastan\\Larastan\\ReturnTypes\\ApplicationMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ContainerMakeDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\ArgumentDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\HasArgumentDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\OptionDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\ConsoleCommand\\HasOptionDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TranslatorGetReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\LangGetReturnTypeExtension, tags: [phpstan.broker.dynamicStaticMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\TransHelperReturnTypeExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\DoubleUnderscoreHelperReturnTypeExtension, tags: [phpstan.broker.dynamicFunctionReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\AppMakeHelper}, {class: Larastan\\Larastan\\Internal\\ConsoleApplicationResolver}, {class: Larastan\\Larastan\\Internal\\ConsoleApplicationHelper}, {class: Larastan\\Larastan\\Support\\HigherOrderCollectionProxyHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\ConfigFunctionDynamicFunctionReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\ConfigRepositoryDynamicMethodReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\ConfigFacadeCollectionDynamicStaticMethodReturnTypeExtension}, {class: Larastan\\Larastan\\Support\\ConfigParser, arguments: {parser: @currentPhpVersionSimpleDirectParser, configPaths: %configDirectories%, treatPhpDocTypesAsCertain: %treatPhpDocTypesAsCertain%}}, {class: Larastan\\Larastan\\Internal\\ConfigHelper}, {class: Larastan\\Larastan\\ReturnTypes\\Helpers\\EnvFunctionDynamicFunctionReturnTypeExtension}, {class: Larastan\\Larastan\\ReturnTypes\\FormRequestSafeDynamicMethodReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\ReturnTypes\\EloquentCollectionMapDynamicReturnTypeExtension, tags: [phpstan.broker.dynamicMethodReturnTypeExtension]}, {class: Larastan\\Larastan\\Rules\\NoAuthFacadeInRequestScopeRule}, {class: Larastan\\Larastan\\Rules\\NoAuthHelperInRequestScopeRule}, {class: Larastan\\Larastan\\Rules\\ConfigCollectionRule}, {class: Illuminate\\Filesystem\\Filesystem, autowired: self}, migrationsParser: {class: PHPStan\\Parser\\CachedParser, arguments: {originalParser: @currentPhpVersionSimpleDirectParser, cachedNodesByStringCountMax: %cache.nodesByStringCountMax%}, autowired: false}}}',
  'analysedPaths' => 
  array (
    0 => 'C:\\laragon\\www\\vigilo\\app',
    1 => 'C:\\laragon\\www\\vigilo\\database\\factories',
    2 => 'C:\\laragon\\www\\vigilo\\database\\seeders',
  ),
  'scannedFiles' => 
  array (
  ),
  'composerLocks' => 
  array (
    'C:/laragon/www/vigilo/composer.lock' => 'b77722708327d3feadc9e7b1898577720aaf99951f3242a7fa91cb1405133a33',
  ),
  'composerInstalled' => 
  array (
    'C:/laragon/www/vigilo/vendor/composer/installed.php' => 
    array (
      'versions' => 
      array (
        'brianium/paratest' => 
        array (
          'pretty_version' => 'v7.20.0',
          'version' => '7.20.0.0',
          'reference' => '81c80677c9ec0ed4ef16b246167f11dec81a6e3d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../brianium/paratest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'brick/math' => 
        array (
          'pretty_version' => '0.18.0',
          'version' => '0.18.0.0',
          'reference' => '82944324d1c1bdb2c2618e89978d4e2ad78d69ad',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../brick/math',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'carbonphp/carbon-doctrine-types' => 
        array (
          'pretty_version' => '3.2.0',
          'version' => '3.2.0.0',
          'reference' => '18ba5ddfec8976260ead6e866180bd5d2f71aa1d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../carbonphp/carbon-doctrine-types',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'composer/pcre' => 
        array (
          'pretty_version' => '3.4.0',
          'version' => '3.4.0.0',
          'reference' => 'd5a341b3fb61f3001970940afb1d332968a183ed',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/./pcre',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'composer/xdebug-handler' => 
        array (
          'pretty_version' => '3.0.5',
          'version' => '3.0.5.0',
          'reference' => '6c1925561632e83d60a44492e0b344cf48ab85ef',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/./xdebug-handler',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'cordoval/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'davedevelopment/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'dflydev/dot-access-data' => 
        array (
          'pretty_version' => 'v3.0.3',
          'version' => '3.0.3.0',
          'reference' => 'a23a2bf4f31d3518f3ecb38660c95715dfead60f',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../dflydev/dot-access-data',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'doctrine/deprecations' => 
        array (
          'pretty_version' => '1.1.6',
          'version' => '1.1.6.0',
          'reference' => 'd4fe3e6fd9bb9e72557a19674f44d8ac7db4c6ca',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../doctrine/deprecations',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'doctrine/inflector' => 
        array (
          'pretty_version' => '2.1.0',
          'version' => '2.1.0.0',
          'reference' => '6d6c96277ea252fc1304627204c3d5e6e15faa3b',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../doctrine/inflector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'doctrine/lexer' => 
        array (
          'pretty_version' => '3.0.1',
          'version' => '3.0.1.0',
          'reference' => '31ad66abc0fc9e1a1f2d9bc6a42668d2fbbcd6dd',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../doctrine/lexer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'dragonmantank/cron-expression' => 
        array (
          'pretty_version' => 'v3.6.0',
          'version' => '3.6.0.0',
          'reference' => 'd61a8a9604ec1f8c3d150d09db6ce98b32675013',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../dragonmantank/cron-expression',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'egulias/email-validator' => 
        array (
          'pretty_version' => '4.0.4',
          'version' => '4.0.4.0',
          'reference' => 'd42c8731f0624ad6bdc8d3e5e9a4524f68801cfa',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../egulias/email-validator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'fakerphp/faker' => 
        array (
          'pretty_version' => 'v1.24.1',
          'version' => '1.24.1.0',
          'reference' => 'e0ee18eb1e6dc3cda3ce9fd97e5a0689a88a64b5',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../fakerphp/faker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'fidry/cpu-core-counter' => 
        array (
          'pretty_version' => '1.3.0',
          'version' => '1.3.0.0',
          'reference' => 'db9508f7b1474469d9d3c53b86f817e344732678',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../fidry/cpu-core-counter',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'filp/whoops' => 
        array (
          'pretty_version' => '2.18.4',
          'version' => '2.18.4.0',
          'reference' => 'd2102955e48b9fd9ab24280a7ad12ed552752c4d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../filp/whoops',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'fruitcake/php-cors' => 
        array (
          'pretty_version' => 'v1.4.0',
          'version' => '1.4.0.0',
          'reference' => '38aaa6c3fd4c157ffe2a4d10aa8b9b16ba8de379',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../fruitcake/php-cors',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'graham-campbell/result-type' => 
        array (
          'pretty_version' => 'v1.1.4',
          'version' => '1.1.4.0',
          'reference' => 'e01f4a821471308ba86aa202fed6698b6b695e3b',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../graham-campbell/result-type',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/guzzle' => 
        array (
          'pretty_version' => '7.15.3',
          'version' => '7.15.3.0',
          'reference' => 'ae311b8f045ea93ce7b1c9cdb7cec06c53f944bc',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../guzzlehttp/guzzle',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/promises' => 
        array (
          'pretty_version' => '2.5.2',
          'version' => '2.5.2.0',
          'reference' => '2823687acff28b2dbe67b2508a6b300e2c3fa4ce',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../guzzlehttp/promises',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/psr7' => 
        array (
          'pretty_version' => '2.13.0',
          'version' => '2.13.0.0',
          'reference' => 'dad89620b7a6edb60c15858442eb2e408b45d8f4',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../guzzlehttp/psr7',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'guzzlehttp/uri-template' => 
        array (
          'pretty_version' => 'v1.0.10',
          'version' => '1.0.10.0',
          'reference' => 'f6c24c21f42b990e9a58912b332d0874df6ba839',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../guzzlehttp/uri-template',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'hamcrest/hamcrest-php' => 
        array (
          'pretty_version' => 'v2.1.1',
          'version' => '2.1.1.0',
          'reference' => 'f8b1c0173b22fa6ec77a81fe63e5b01eba7e6487',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../hamcrest/hamcrest-php',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'iamcal/sql-parser' => 
        array (
          'pretty_version' => 'v0.7',
          'version' => '0.7.0.0',
          'reference' => '610392f38de49a44dab08dc1659960a29874c4b8',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../iamcal/sql-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'illuminate/auth' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/broadcasting' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/bus' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/cache' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/collections' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/concurrency' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/conditionable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/config' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/console' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/container' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/contracts' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/cookie' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/database' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/encryption' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/events' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/filesystem' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/hashing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/http' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/image' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/json-schema' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/log' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/macroable' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/mail' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/notifications' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/pagination' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/pipeline' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/process' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/queue' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/redis' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/reflection' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/routing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/session' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/support' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/testing' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/translation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/validation' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'illuminate/view' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => 'v13.24.0',
          ),
        ),
        'jean85/pretty-package-versions' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '4d7aa5dab42e2a76d99559706022885de0e18e1a',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../jean85/pretty-package-versions',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'kodova/hamcrest-php' => 
        array (
          'dev_requirement' => true,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'larastan/larastan' => 
        array (
          'pretty_version' => 'v3.10.0',
          'version' => '3.10.0.0',
          'reference' => '2970f83398154178a739609c244577267c7ee8eb',
          'type' => 'phpstan-extension',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../larastan/larastan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/agent-detector' => 
        array (
          'pretty_version' => 'v2.0.2',
          'version' => '2.0.2.0',
          'reference' => '90694b9256099591cf9e55d08c18ba7a00bf099f',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../laravel/agent-detector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/breeze' => 
        array (
          'pretty_version' => 'v2.4.2',
          'version' => '2.4.2.0',
          'reference' => '4f20e7b2cc8d25daa85d8647241a89c8e0930305',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../laravel/breeze',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/framework' => 
        array (
          'pretty_version' => 'v13.24.0',
          'version' => '13.24.0.0',
          'reference' => '6d481710375d2aa67656922ef760cdd2b18bcfe0',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../laravel/framework',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/pail' => 
        array (
          'pretty_version' => 'v1.2.7',
          'version' => '1.2.7.0',
          'reference' => '2f7d27dada8effc48b8c424445a69cca7007daaa',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../laravel/pail',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/pao' => 
        array (
          'pretty_version' => 'v1.1.4',
          'version' => '1.1.4.0',
          'reference' => '5aee99c8c37565e9c457c33f4d36aa363a389dc8',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../laravel/pao',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/pint' => 
        array (
          'pretty_version' => 'v1.30.5',
          'version' => '1.30.5.0',
          'reference' => 'fe4148c503a0e266353d61396b79bbf7f35122df',
          'type' => 'project',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../laravel/pint',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'laravel/prompts' => 
        array (
          'pretty_version' => 'v0.3.22',
          'version' => '0.3.22.0',
          'reference' => '02b89b39e8972a998db4d5d4ad4719239dd4aee4',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../laravel/prompts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/sanctum' => 
        array (
          'pretty_version' => 'v4.3.3',
          'version' => '4.3.3.0',
          'reference' => 'fee27a573d1a013af3721d86153a65e0b11927e6',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../laravel/sanctum',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/serializable-closure' => 
        array (
          'pretty_version' => 'v2.0.15',
          'version' => '2.0.15.0',
          'reference' => 'dccd8bcb851bb03fcc005df650b708b57cc52661',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../laravel/serializable-closure',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'laravel/tinker' => 
        array (
          'pretty_version' => 'v3.0.2',
          'version' => '3.0.2.0',
          'reference' => '4faba77764bd33411735936acdf30446d058c78b',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../laravel/tinker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/commonmark' => 
        array (
          'pretty_version' => '2.9.2',
          'version' => '2.9.2.0',
          'reference' => '72e9a87efcf41a8e83be3ed0866b69d77565cb12',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../league/commonmark',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/config' => 
        array (
          'pretty_version' => 'v1.2.0',
          'version' => '1.2.0.0',
          'reference' => '754b3604fb2984c71f4af4a9cbe7b57f346ec1f3',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../league/config',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/flysystem' => 
        array (
          'pretty_version' => '3.35.2',
          'version' => '3.35.2.0',
          'reference' => 'b277b5dc3d56650b68904117124e79c851e12376',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../league/flysystem',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/flysystem-local' => 
        array (
          'pretty_version' => '3.31.0',
          'version' => '3.31.0.0',
          'reference' => '2f669db18a4c20c755c2bb7d3a7b0b2340488079',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../league/flysystem-local',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/mime-type-detection' => 
        array (
          'pretty_version' => '1.17.0',
          'version' => '1.17.0.0',
          'reference' => 'f5f47eff7c48ed1003069a2ca67f316fb4021c76',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../league/mime-type-detection',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri' => 
        array (
          'pretty_version' => '7.8.1',
          'version' => '7.8.1.0',
          'reference' => '08cf38e3924d4f56238125547b5720496fac8fd4',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../league/uri',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'league/uri-interfaces' => 
        array (
          'pretty_version' => '7.8.1',
          'version' => '7.8.1.0',
          'reference' => '85d5c77c5d6d3af6c54db4a78246364908f3c928',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../league/uri-interfaces',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'livewire/livewire' => 
        array (
          'pretty_version' => 'v3.8.4',
          'version' => '3.8.4.0',
          'reference' => 'bc06b755058e2253cc4153d95c8720c585f6254c',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../livewire/livewire',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'livewire/volt' => 
        array (
          'pretty_version' => 'v1.11.2',
          'version' => '1.11.2.0',
          'reference' => '1e46197d615bcd395ae87784fd8d25077782043d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../livewire/volt',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'mockery/mockery' => 
        array (
          'pretty_version' => '1.6.12',
          'version' => '1.6.12.0',
          'reference' => '1f4efdd7d3beafe9807b08156dfcb176d18f1699',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../mockery/mockery',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'monolog/monolog' => 
        array (
          'pretty_version' => '3.10.0',
          'version' => '3.10.0.0',
          'reference' => 'b321dd6749f0bf7189444158a3ce785cc16d69b0',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../monolog/monolog',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'mtdowling/cron-expression' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '^1.0',
          ),
        ),
        'myclabs/deep-copy' => 
        array (
          'pretty_version' => '1.13.4',
          'version' => '1.13.4.0',
          'reference' => '07d290f0c47959fd5eed98c95ee5602db07e0b6a',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../myclabs/deep-copy',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'nesbot/carbon' => 
        array (
          'pretty_version' => '3.13.2',
          'version' => '3.13.2.0',
          'reference' => 'a1c54919f5fff9800cd03c32bd01defd5a4061cb',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../nesbot/carbon',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/schema' => 
        array (
          'pretty_version' => 'v1.3.5',
          'version' => '1.3.5.0',
          'reference' => 'f0ab1a3cda782dbc5da270d28545236aa80c4002',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../nette/schema',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nette/utils' => 
        array (
          'pretty_version' => 'v4.1.5',
          'version' => '4.1.5.0',
          'reference' => 'b043439dbdf954e6c28b5ea7e34b0100f83165e0',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../nette/utils',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nikic/php-parser' => 
        array (
          'pretty_version' => 'v5.8.0',
          'version' => '5.8.0.0',
          'reference' => '044a6a392ff8ad0d61f14370a5fbbd0a0107152f',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../nikic/php-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'nunomaduro/collision' => 
        array (
          'pretty_version' => 'v8.9.5',
          'version' => '8.9.5.0',
          'reference' => 'fb53eacd509a1d303858e2d20cfebf2d630254ec',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../nunomaduro/collision',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'nunomaduro/termwind' => 
        array (
          'pretty_version' => 'v2.4.0',
          'version' => '2.4.0.0',
          'reference' => '712a31b768f5daea284c2169a7d227031001b9a8',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../nunomaduro/termwind',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'pestphp/pest' => 
        array (
          'pretty_version' => 'v4.7.8',
          'version' => '4.7.8.0',
          'reference' => '5b2293f67adcf1b2320b33f521b94a692d18f360',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../pestphp/pest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin' => 
        array (
          'pretty_version' => 'v4.0.0',
          'version' => '4.0.0.0',
          'reference' => '9d4b93d7f73d3f9c3189bb22c220fef271cdf568',
          'type' => 'composer-plugin',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../pestphp/pest-plugin',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-arch' => 
        array (
          'pretty_version' => 'v4.0.2',
          'version' => '4.0.2.0',
          'reference' => '3fb0d02a91b9da504b139dc7ab2a31efb7c3215c',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../pestphp/pest-plugin-arch',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-laravel' => 
        array (
          'pretty_version' => 'v4.1.0',
          'version' => '4.1.0.0',
          'reference' => '3057a36669ff11416cc0dc2b521b3aec58c488d0',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../pestphp/pest-plugin-laravel',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-mutate' => 
        array (
          'pretty_version' => 'v4.0.1',
          'version' => '4.0.1.0',
          'reference' => 'd9b32b60b2385e1688a68cc227594738ec26d96c',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../pestphp/pest-plugin-mutate',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'pestphp/pest-plugin-profanity' => 
        array (
          'pretty_version' => 'v4.2.1',
          'version' => '4.2.1.0',
          'reference' => '343cfa6f3564b7e35df0ebb77b7fa97039f72b27',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../pestphp/pest-plugin-profanity',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phar-io/manifest' => 
        array (
          'pretty_version' => '2.0.4',
          'version' => '2.0.4.0',
          'reference' => '54750ef60c58e43759730615a392c31c80e23176',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phar-io/manifest',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phar-io/version' => 
        array (
          'pretty_version' => '3.2.1',
          'version' => '3.2.1.0',
          'reference' => '4f7fd7836c6f332bb2933569e566a0d6c4cbed74',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phar-io/version',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/reflection-common' => 
        array (
          'pretty_version' => '2.2.0',
          'version' => '2.2.0.0',
          'reference' => '1d01c49d4ed62f25aa84a747ad35d5a16924662b',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phpdocumentor/reflection-common',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/reflection-docblock' => 
        array (
          'pretty_version' => '6.0.3',
          'version' => '6.0.3.0',
          'reference' => '7bae67520aa9f5ecc506d646810bd40d9da54582',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phpdocumentor/reflection-docblock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpdocumentor/type-resolver' => 
        array (
          'pretty_version' => '2.0.0',
          'version' => '2.0.0.0',
          'reference' => '327a05bbee54120d4786a0dc67aad30226ad4cf9',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phpdocumentor/type-resolver',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpoption/phpoption' => 
        array (
          'pretty_version' => '1.9.5',
          'version' => '1.9.5.0',
          'reference' => '75365b91986c2405cf5e1e012c5595cd487a98be',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phpoption/phpoption',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'phpstan/phpdoc-parser' => 
        array (
          'pretty_version' => '2.3.3',
          'version' => '2.3.3.0',
          'reference' => 'fb19eedd2bb67ff8cf7a5502ad329e701d6398a3',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phpstan/phpdoc-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpstan/phpstan' => 
        array (
          'pretty_version' => '2.2.8',
          'version' => '2.2.8.0',
          'reference' => 'e285254e60f33c21902efef4a926ca0987c06804',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phpstan/phpstan',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-code-coverage' => 
        array (
          'pretty_version' => '12.5.7',
          'version' => '12.5.7.0',
          'reference' => '186dab580576598076de6818596d12b61801880e',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phpunit/php-code-coverage',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-file-iterator' => 
        array (
          'pretty_version' => '6.0.1',
          'version' => '6.0.1.0',
          'reference' => '3d1cd096ef6bea4bf2762ba586e35dbd317cbfd5',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phpunit/php-file-iterator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-invoker' => 
        array (
          'pretty_version' => '6.0.0',
          'version' => '6.0.0.0',
          'reference' => '12b54e689b07a25a9b41e57736dfab6ec9ae5406',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phpunit/php-invoker',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-text-template' => 
        array (
          'pretty_version' => '5.0.0',
          'version' => '5.0.0.0',
          'reference' => 'e1367a453f0eda562eedb4f659e13aa900d66c53',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phpunit/php-text-template',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/php-timer' => 
        array (
          'pretty_version' => '8.0.0',
          'version' => '8.0.0.0',
          'reference' => 'f258ce36aa457f3aa3339f9ed4c81fc66dc8c2cc',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phpunit/php-timer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'phpunit/phpunit' => 
        array (
          'pretty_version' => '12.5.33',
          'version' => '12.5.33.0',
          'reference' => 'b98e028a26c5c5ba7e4a54be96ccf35f2914d184',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../phpunit/phpunit',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'psr/clock' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'reference' => 'e41a24703d4560fd0acb709162f73b8adfc3aa0d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../psr/clock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/clock-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/container' => 
        array (
          'pretty_version' => '2.0.2',
          'version' => '2.0.2.0',
          'reference' => 'c71ecc56dfe541dbd90c5360474fbc405f8d5963',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../psr/container',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/container-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.1 || 2.0',
          ),
        ),
        'psr/event-dispatcher' => 
        array (
          'pretty_version' => '1.0.0',
          'version' => '1.0.0.0',
          'reference' => 'dbefd12671e8a14ec7f180cab83036ed26714bb0',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../psr/event-dispatcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-client' => 
        array (
          'pretty_version' => '1.0.3',
          'version' => '1.0.3.0',
          'reference' => 'bb5906edc1c324c9a05aa0873d40117941e5fa90',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../psr/http-client',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-client-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-factory' => 
        array (
          'pretty_version' => '1.1.0',
          'version' => '1.1.0.0',
          'reference' => '2b4765fddfe3b508ac62f829e852b1501d3f6e8a',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../psr/http-factory',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-factory-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/http-message' => 
        array (
          'pretty_version' => '2.0',
          'version' => '2.0.0.0',
          'reference' => '402d35bcb92c70c026d1a6a9883f06b2ead23d71',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../psr/http-message',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/http-message-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0',
          ),
        ),
        'psr/log' => 
        array (
          'pretty_version' => '3.0.2',
          'version' => '3.0.2.0',
          'reference' => 'f16e1d5863e37f8d8c2a01719f5b34baa2b714d3',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../psr/log',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/log-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0 || 2.0 || 3.0',
            1 => '1.0|2.0|3.0',
            2 => '3.0.0',
          ),
        ),
        'psr/simple-cache' => 
        array (
          'pretty_version' => '3.0.0',
          'version' => '3.0.0.0',
          'reference' => '764e0b3939f5ca87cb904f570ef9be2d78a07865',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../psr/simple-cache',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'psr/simple-cache-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '1.0 || 2.0 || 3.0',
          ),
        ),
        'psy/psysh' => 
        array (
          'pretty_version' => 'v0.12.24',
          'version' => '0.12.24.0',
          'reference' => 'ca0fdcf8a7617afa3adfdf1b5fef573dffb69ca1',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../psy/psysh',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ralouphie/getallheaders' => 
        array (
          'pretty_version' => '3.0.3',
          'version' => '3.0.3.0',
          'reference' => '120b605dfeb996808c31b6477290a714d356e822',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../ralouphie/getallheaders',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ramsey/collection' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '344572933ad0181accbf4ba763e85a0306a8c5e2',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../ramsey/collection',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ramsey/uuid' => 
        array (
          'pretty_version' => '4.9.3',
          'version' => '4.9.3.0',
          'reference' => '1df15849d00943a67d677dc9cfd80795f038c9f8',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../ramsey/uuid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'rhumsaa/uuid' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '4.9.3',
          ),
        ),
        'sebastian/cli-parser' => 
        array (
          'pretty_version' => '4.2.1',
          'version' => '4.2.1.0',
          'reference' => '7d05781b13f7dec9043a629a21d086ed74582a15',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../sebastian/cli-parser',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/comparator' => 
        array (
          'pretty_version' => '7.1.8',
          'version' => '7.1.8.0',
          'reference' => '7c65c1e79836812819705b473a90c12399542485',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../sebastian/comparator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/complexity' => 
        array (
          'pretty_version' => '5.0.0',
          'version' => '5.0.0.0',
          'reference' => 'bad4316aba5303d0221f43f8cee37eb58d384bbb',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../sebastian/complexity',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/diff' => 
        array (
          'pretty_version' => '7.0.0',
          'version' => '7.0.0.0',
          'reference' => '7ab1ea946c012266ca32390913653d844ecd085f',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../sebastian/diff',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/environment' => 
        array (
          'pretty_version' => '8.1.2',
          'version' => '8.1.2.0',
          'reference' => '9d32c685773823b1983e256ae4ecd48a10d6e439',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../sebastian/environment',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/exporter' => 
        array (
          'pretty_version' => '7.0.3',
          'version' => '7.0.3.0',
          'reference' => 'c5e21b5de653ce0a769fb36f5cdfcb5e7a32cf23',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../sebastian/exporter',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/global-state' => 
        array (
          'pretty_version' => '8.0.3',
          'version' => '8.0.3.0',
          'reference' => 'b164d3274d6537ab462591c5755f76a8f5b1aae9',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../sebastian/global-state',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/lines-of-code' => 
        array (
          'pretty_version' => '4.0.1',
          'version' => '4.0.1.0',
          'reference' => 'd543b8ef219dcd8da262cbb958639a96bedba10e',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../sebastian/lines-of-code',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/object-enumerator' => 
        array (
          'pretty_version' => '7.0.0',
          'version' => '7.0.0.0',
          'reference' => '1effe8e9b8e068e9ae228e542d5d11b5d16db894',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../sebastian/object-enumerator',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/object-reflector' => 
        array (
          'pretty_version' => '5.0.0',
          'version' => '5.0.0.0',
          'reference' => '4bfa827c969c98be1e527abd576533293c634f6a',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../sebastian/object-reflector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/recursion-context' => 
        array (
          'pretty_version' => '7.0.1',
          'version' => '7.0.1.0',
          'reference' => '0b01998a7d5b1f122911a66bebcb8d46f0c82d8c',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../sebastian/recursion-context',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/type' => 
        array (
          'pretty_version' => '6.0.4',
          'version' => '6.0.4.0',
          'reference' => '82ff822c2edc46724be9f7411d3163021f602773',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../sebastian/type',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'sebastian/version' => 
        array (
          'pretty_version' => '6.0.0',
          'version' => '6.0.0.0',
          'reference' => '3e6ccf7657d4f0a59200564b08cead899313b53c',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../sebastian/version',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'spatie/once' => 
        array (
          'dev_requirement' => false,
          'replaced' => 
          array (
            0 => '*',
          ),
        ),
        'staabm/side-effects-detector' => 
        array (
          'pretty_version' => '1.0.5',
          'version' => '1.0.5.0',
          'reference' => 'd8334211a140ce329c13726d4a715adbddd0a163',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../staabm/side-effects-detector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'symfony/clock' => 
        array (
          'pretty_version' => 'v7.4.8',
          'version' => '7.4.8.0',
          'reference' => '674fa3b98e21531dd040e613479f5f6fa8f32111',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/clock',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/console' => 
        array (
          'pretty_version' => 'v7.4.16',
          'version' => '7.4.16.0',
          'reference' => 'f4c69c9aed03abf933b294257d618bdd9b30a06d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/console',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/css-selector' => 
        array (
          'pretty_version' => 'v7.4.9',
          'version' => '7.4.9.0',
          'reference' => 'b75663ed96cf4756e28e3105476f220f92886cc4',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/css-selector',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/deprecation-contracts' => 
        array (
          'pretty_version' => 'v3.7.1',
          'version' => '3.7.1.0',
          'reference' => 'f3202fa1b5097b0af062dc978b32ecf63404e31d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/deprecation-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/error-handler' => 
        array (
          'pretty_version' => 'v7.4.15',
          'version' => '7.4.15.0',
          'reference' => 'd49f6a19f326db41ae7103bdc38e3eb35a791261',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/error-handler',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher' => 
        array (
          'pretty_version' => 'v7.4.15',
          'version' => '7.4.15.0',
          'reference' => '336e7f3b9e95aba04f93ea9143920c2186abfbb9',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/event-dispatcher',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-contracts' => 
        array (
          'pretty_version' => 'v3.7.1',
          'version' => '3.7.1.0',
          'reference' => 'c7de7a00ffb67842132da02ea92988a39ccd9f4e',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/event-dispatcher-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/event-dispatcher-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '2.0|3.0',
          ),
        ),
        'symfony/finder' => 
        array (
          'pretty_version' => 'v7.4.14',
          'version' => '7.4.14.0',
          'reference' => '13b38720174286f55d1761152b575a8d1436fc25',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/finder',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-foundation' => 
        array (
          'pretty_version' => 'v7.4.16',
          'version' => '7.4.16.0',
          'reference' => 'b676451bb638e99a7d34d8a2be90406822e301eb',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/http-foundation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/http-kernel' => 
        array (
          'pretty_version' => 'v7.4.16',
          'version' => '7.4.16.0',
          'reference' => 'f5e728670fa2218ae8be8ea91f2b44b7d6e5304c',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/http-kernel',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mailer' => 
        array (
          'pretty_version' => 'v7.4.15',
          'version' => '7.4.15.0',
          'reference' => '68c1f27c97edd0222eb8d440a6c8c4da5354ab46',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/mailer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/mime' => 
        array (
          'pretty_version' => 'v7.4.16',
          'version' => '7.4.16.0',
          'reference' => '20094b76a7106dbe978d31cd3bd7aa1ed248d0e5',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/mime',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-ctype' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => '141046a8f9477948ff284fa65be2095baafb94f2',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/polyfill-ctype',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-grapheme' => 
        array (
          'pretty_version' => 'v1.41.0',
          'version' => '1.41.0.0',
          'reference' => 'bb899c1db0aa8127dc3afe8cda4a67eb24915f8d',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/polyfill-intl-grapheme',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-idn' => 
        array (
          'pretty_version' => 'v1.38.1',
          'version' => '1.38.1.0',
          'reference' => 'dc21118016c039a66235cf93d96b435ffb282412',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/polyfill-intl-idn',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-normalizer' => 
        array (
          'pretty_version' => 'v1.38.0',
          'version' => '1.38.0.0',
          'reference' => '2d446c214bdbe5b71bde5011b060a05fece3ae6b',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/polyfill-intl-normalizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-mbstring' => 
        array (
          'pretty_version' => 'v1.38.2',
          'version' => '1.38.2.0',
          'reference' => 'd3d318bad5e7a1bfbd026009c8bfb8d8f99ae6b6',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/polyfill-mbstring',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php80' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => 'dfb55726c3a76ea3b6459fcfda1ec2d80a682411',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/polyfill-php80',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php83' => 
        array (
          'pretty_version' => 'v1.41.0',
          'version' => '1.41.0.0',
          'reference' => '5ea99087fb99c273a9b9236ed4c31e78b16103c6',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/polyfill-php83',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php84' => 
        array (
          'pretty_version' => 'v1.38.1',
          'version' => '1.38.1.0',
          'reference' => 'f4e1dfaee5b74aba5964fe1fd4dfc7ba5e3085fa',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/polyfill-php84',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php85' => 
        array (
          'pretty_version' => 'v1.41.0',
          'version' => '1.41.0.0',
          'reference' => '255fab485aaa1006ed411040c42aecd7b5302d7a',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/polyfill-php85',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-php86' => 
        array (
          'pretty_version' => 'v1.41.0',
          'version' => '1.41.0.0',
          'reference' => '6bc356ed3d8dbfeea8f0de235e34d670704e880e',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/polyfill-php86',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/polyfill-uuid' => 
        array (
          'pretty_version' => 'v1.37.0',
          'version' => '1.37.0.0',
          'reference' => '26dfec253c4cf3e51b541b52ddf7e42cb0908e94',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/polyfill-uuid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/process' => 
        array (
          'pretty_version' => 'v7.4.13',
          'version' => '7.4.13.0',
          'reference' => 'f5804be144caceb570f6747519999636b664f24c',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/process',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/routing' => 
        array (
          'pretty_version' => 'v7.4.15',
          'version' => '7.4.15.0',
          'reference' => '80c0a93d3f8e7499f716204a1fb38ead942a7a2b',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/routing',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/service-contracts' => 
        array (
          'pretty_version' => 'v3.7.1',
          'version' => '3.7.1.0',
          'reference' => 'c0a284bab1ed8aa0417e3d69250ab437739563a0',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/service-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/string' => 
        array (
          'pretty_version' => 'v7.4.15',
          'version' => '7.4.15.0',
          'reference' => 'e394af32256bf9e7bf80849d95e589167c10097b',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/string',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation' => 
        array (
          'pretty_version' => 'v7.4.16',
          'version' => '7.4.16.0',
          'reference' => '501e0ff4553c744209ca1a68790d8a4541563710',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/translation',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation-contracts' => 
        array (
          'pretty_version' => 'v3.7.1',
          'version' => '3.7.1.0',
          'reference' => 'ccb206b98faccc511ebae8e5fad50f2dc0b30621',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/translation-contracts',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/translation-implementation' => 
        array (
          'dev_requirement' => false,
          'provided' => 
          array (
            0 => '2.3|3.0',
          ),
        ),
        'symfony/uid' => 
        array (
          'pretty_version' => 'v7.4.9',
          'version' => '7.4.9.0',
          'reference' => '2676b524340abcfe4d6151ec698463cebafee439',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/uid',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'symfony/var-dumper' => 
        array (
          'pretty_version' => 'v7.4.15',
          'version' => '7.4.15.0',
          'reference' => '04ba4add636a95ff437af3a5a9499bb1d6c6d4bd',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../symfony/var-dumper',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'ta-tikoma/phpunit-architecture-test' => 
        array (
          'pretty_version' => '0.8.7',
          'version' => '0.8.7.0',
          'reference' => '1248f3f506ca9641d4f68cebcd538fa489754db8',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../ta-tikoma/phpunit-architecture-test',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'theseer/tokenizer' => 
        array (
          'pretty_version' => '2.0.1',
          'version' => '2.0.1.0',
          'reference' => '7989e43bf381af0eac72e4f0ca5bcbfa81658be4',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../theseer/tokenizer',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
        'tijsverkoyen/css-to-inline-styles' => 
        array (
          'pretty_version' => 'v2.4.0',
          'version' => '2.4.0.0',
          'reference' => 'f0292ccf0ec75843d65027214426b6b163b48b41',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../tijsverkoyen/css-to-inline-styles',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'vlucas/phpdotenv' => 
        array (
          'pretty_version' => 'v5.6.4',
          'version' => '5.6.4.0',
          'reference' => '416df702837983f8d5ff48c9c3fee4f5f57b980b',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../vlucas/phpdotenv',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'voku/portable-ascii' => 
        array (
          'pretty_version' => '2.1.1',
          'version' => '2.1.1.0',
          'reference' => '8e1051fe39379367aecf014f41744ce7539a856f',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../voku/portable-ascii',
          'aliases' => 
          array (
          ),
          'dev_requirement' => false,
        ),
        'webmozart/assert' => 
        array (
          'pretty_version' => '2.4.1',
          'version' => '2.4.1.0',
          'reference' => '2ccb7c2e821038c03a3e6e1700c570c158c55f70',
          'type' => 'library',
          'install_path' => 'C:\\laragon\\www\\vigilo\\vendor\\composer/../webmozart/assert',
          'aliases' => 
          array (
          ),
          'dev_requirement' => true,
        ),
      ),
    ),
  ),
  'executedFilesHashes' => 
  array (
    'C:\\laragon\\www\\vigilo\\vendor\\larastan\\larastan\\bootstrap.php' => '5a3eacbf63b3e41659adfee92facededf8e020a932800f93c9a8b0e67f235805',
    'phar://C:\\laragon\\www\\vigilo\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\Attribute85.php' => 'cb8b31e82c61ce197871c9e8a6f122256751f2ab606dd2be90846d4fa5f8933e',
    'phar://C:\\laragon\\www\\vigilo\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionAttribute.php' => 'c0068e383717870a304781d462f7e2afe1c6f24e9133851852a2aca96b4fa26f',
    'phar://C:\\laragon\\www\\vigilo\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionIntersectionType.php' => '65fe0a8bc6fe285d8ddc8798ab5b9299920af70db5ad74596bc08df823e7c5d9',
    'phar://C:\\laragon\\www\\vigilo\\vendor\\phpstan\\phpstan\\phpstan.phar\\stubs\\runtime\\ReflectionUnionType.php' => '1e2fe940e4ba4e00d9ee6adb2af3ee1bf333e6f8afe61c61deb038886d293427',
  ),
  'phpExtensions' => 
  array (
    0 => 'Core',
    1 => 'PDO',
    2 => 'Phar',
    3 => 'Reflection',
    4 => 'SPL',
    5 => 'SimpleXML',
    6 => 'bcmath',
    7 => 'calendar',
    8 => 'ctype',
    9 => 'curl',
    10 => 'date',
    11 => 'dom',
    12 => 'exif',
    13 => 'fileinfo',
    14 => 'filter',
    15 => 'gd',
    16 => 'hash',
    17 => 'iconv',
    18 => 'intl',
    19 => 'json',
    20 => 'libxml',
    21 => 'mbstring',
    22 => 'mysqli',
    23 => 'mysqlnd',
    24 => 'openssl',
    25 => 'pcre',
    26 => 'pdo_mysql',
    27 => 'pdo_sqlite',
    28 => 'random',
    29 => 'readline',
    30 => 'session',
    31 => 'sodium',
    32 => 'sqlite3',
    33 => 'standard',
    34 => 'tokenizer',
    35 => 'xml',
    36 => 'xmlreader',
    37 => 'xmlwriter',
    38 => 'zip',
    39 => 'zlib',
  ),
  'stubFiles' => 
  array (
  ),
  'level' => '8',
),
	'projectExtensionFiles' => array (
),
	'errorsCallback' => static function (): array { return array (
); },
	'locallyIgnoredErrorsCallback' => static function (): array { return array (
); },
	'linesToIgnore' => array (
),
	'unmatchedLineIgnores' => array (
),
	'collectedDataCallback' => static function (): array { return array (
  'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => '$ids->isEmpty():38',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
        1 => 'dispatch',
        2 => 51,
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => '$portfolio->scheduledRuns()->whereDate(\'ran_on\', $ranOn)->exists():45',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Actions\\RunDuePortfolioSchedules',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Actions\\RunDuePortfolioSchedules',
        1 => 'matchedDay',
        2 => 'App\\Actions\\RunDuePortfolioSchedules',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\ChangeEventData.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\DTO\\ChangeEventData',
        1 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\CompanyData.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\DTO\\CompanyData',
        1 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\ImportReport.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\DTO\\ImportReport',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\DTO\\ImportReport',
        1 => 'importedCount',
        2 => 'App\\DTO\\ImportReport',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\DTO\\ImportReport',
        1 => 'rejectedCount',
        2 => 'App\\DTO\\ImportReport',
        3 => 
        array (
        ),
      ),
      2 => 
      array (
        0 => 'App\\DTO\\ImportReport',
        1 => 'rejectedWithReason',
        2 => 'App\\DTO\\ImportReport',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\PartnerData.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\DTO\\PartnerData',
        1 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Enums\\ChangeType.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Enums\\ChangeType',
        1 => 'label',
        2 => 'App\\Enums\\ChangeType',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Enums\\RefreshStatus.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Enums\\RefreshStatus',
        1 => 'label',
        2 => 'App\\Enums\\RefreshStatus',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Enums\\RefreshStatus',
        1 => 'badgeClasses',
        2 => 'App\\Enums\\RefreshStatus',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Enums\\Severity.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Enums\\Severity',
        1 => 'weight',
        2 => 'App\\Enums\\Severity',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Enums\\Severity',
        1 => 'label',
        2 => 'App\\Enums\\Severity',
        3 => 
        array (
        ),
      ),
      2 => 
      array (
        0 => 'App\\Enums\\Severity',
        1 => 'badgeClasses',
        2 => 'App\\Enums\\Severity',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => 'is_file($this->path):37',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Jobs\\ImportCompaniesCsvJob',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Queue\\Queueable',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => '$events->isNotEmpty():122',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => '$users->isEmpty():176',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
        1 => 'uniqueId',
        2 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
        1 => 'middleware',
        2 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
        3 => 
        array (
          0 => 'm' . "\0" . 'illuminate\\queue\\middleware\\withoutoverlapping' . "\0" . '__construct',
          1 => 'm' . "\0" . 'illuminate\\queue\\middleware\\withoutoverlapping' . "\0" . 'releaseafter',
          2 => 'm' . "\0" . 'illuminate\\queue\\middleware\\withoutoverlapping' . "\0" . 'expireafter',
          3 => 'm' . "\0" . 'illuminate\\queue\\middleware\\ratelimited' . "\0" . '__construct',
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Bus\\Batchable',
        1 => 'Illuminate\\Foundation\\Queue\\Queueable',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Actions\\Logout.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\Session',
        1 => 'invalidate',
        2 => 19,
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector' => 
    array (
      0 => 'livewire.alerts.inbox',
    ),
    'PHPStan\\Rules\\Comparison\\ConstantConditionInTraitCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeStaticMethodCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '\\Illuminate\\Support\\Facades\\Auth::user():14',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '!$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      3 => 
      array (
        0 => 'PHPStan\\Rules\\Classes\\ImpossibleInstanceOfRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      4 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeFunctionCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => 'abort(403):17',
        3 => NULL,
      ),
      5 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$this->currentUser():25',
        3 => NULL,
      ),
      6 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$organizationId === null:27',
        3 => NULL,
      ),
      7 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\StrictComparisonOfDifferentTypesRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$organizationId === null:27',
        3 => NULL,
      ),
      8 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeFunctionCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => 'abort(403, \'Usuário sem organização.\'):28',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => 'in_array($this->severity, [\'critical\', \'high\', \'medium\', \'low\'], true):70',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'abort',
        1 => 17,
      ),
      1 => 
      array (
        0 => 'abort',
        1 => 28,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector' => 
    array (
      0 => 'livewire.companies.show',
    ),
    'PHPStan\\Rules\\Comparison\\ConstantConditionInTraitCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeStaticMethodCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '\\Illuminate\\Support\\Facades\\Auth::user():14',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '!$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      3 => 
      array (
        0 => 'PHPStan\\Rules\\Classes\\ImpossibleInstanceOfRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      4 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeFunctionCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => 'abort(403):17',
        3 => NULL,
      ),
      5 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$this->currentUser():25',
        3 => NULL,
      ),
      6 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$organizationId === null:27',
        3 => NULL,
      ),
      7 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\StrictComparisonOfDifferentTypesRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$organizationId === null:27',
        3 => NULL,
      ),
      8 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeFunctionCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => 'abort(403, \'Usuário sem organização.\'):28',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'abort',
        1 => 17,
      ),
      1 => 
      array (
        0 => 'abort',
        1 => 28,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
        1 => 'dispatch',
        2 => 33,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Concerns\\InteractsWithCurrentOrganization.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitDeclarationCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        1 => 10,
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector' => 
    array (
      0 => 'livewire.dashboard',
    ),
    'PHPStan\\Rules\\Comparison\\ConstantConditionInTraitCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeStaticMethodCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '\\Illuminate\\Support\\Facades\\Auth::user():14',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '!$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      3 => 
      array (
        0 => 'PHPStan\\Rules\\Classes\\ImpossibleInstanceOfRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      4 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeFunctionCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => 'abort(403):17',
        3 => NULL,
      ),
      5 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$this->currentUser():25',
        3 => NULL,
      ),
      6 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$organizationId === null:27',
        3 => NULL,
      ),
      7 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\StrictComparisonOfDifferentTypesRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$organizationId === null:27',
        3 => NULL,
      ),
      8 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeFunctionCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => 'abort(403, \'Usuário sem organização.\'):28',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'abort',
        1 => 17,
      ),
      1 => 
      array (
        0 => 'abort',
        1 => 28,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Forms\\LoginForm.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedTranslationFunctionCollector' => 
    array (
      0 => 
      array (
        0 => 'auth.failed',
        1 => 39,
      ),
      1 => 
      array (
        0 => 'auth.throttle',
        1 => 60,
      ),
    ),
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => '\\Illuminate\\Support\\Facades\\Auth::attempt($this->only([\'email\', \'password\']), $this->remember):35',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => '\\Illuminate\\Support\\Facades\\RateLimiter::tooManyAttempts($this->throttleKey(), 5):51',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'event',
        1 => 55,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\RateLimiter',
        1 => 'hit',
        2 => 36,
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector' => 
    array (
      0 => 'livewire.portfolios.index',
    ),
    'PHPStan\\Rules\\Comparison\\ConstantConditionInTraitCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeStaticMethodCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '\\Illuminate\\Support\\Facades\\Auth::user():14',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '!$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      3 => 
      array (
        0 => 'PHPStan\\Rules\\Classes\\ImpossibleInstanceOfRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      4 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeFunctionCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => 'abort(403):17',
        3 => NULL,
      ),
      5 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$this->currentUser():25',
        3 => NULL,
      ),
      6 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$organizationId === null:27',
        3 => NULL,
      ),
      7 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\StrictComparisonOfDifferentTypesRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$organizationId === null:27',
        3 => NULL,
      ),
      8 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeFunctionCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => 'abort(403, \'Usuário sem organização.\'):28',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'abort',
        1 => 17,
      ),
      1 => 
      array (
        0 => 'abort',
        1 => 28,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Builder',
        1 => 'create',
        2 => 34,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector' => 
    array (
      0 => 'livewire.portfolios.show',
    ),
    'PHPStan\\Rules\\Comparison\\ConstantConditionInTraitCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeStaticMethodCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '\\Illuminate\\Support\\Facades\\Auth::user():14',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '!$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      3 => 
      array (
        0 => 'PHPStan\\Rules\\Classes\\ImpossibleInstanceOfRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$user instanceof \\App\\Models\\User:16',
        3 => NULL,
      ),
      4 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeFunctionCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => 'abort(403):17',
        3 => NULL,
      ),
      5 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$this->currentUser():25',
        3 => NULL,
      ),
      6 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$organizationId === null:27',
        3 => NULL,
      ),
      7 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\StrictComparisonOfDifferentTypesRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => '$organizationId === null:27',
        3 => NULL,
      ),
      8 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeFunctionCallRule',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => 'abort(403, \'Usuário sem organização.\'):28',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\TernaryOperatorConstantConditionRule',
        1 => NULL,
        2 => 'in_array($day, $this->scheduleDays, true):82',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => '$this->scheduleFiresOn($days, $date):170',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => NULL,
        2 => '$this->portfolio->scheduledRuns()->whereDate(\'ran_on\', $date->toDateString())->exists():175',
        3 => NULL,
      ),
      3 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => '$this->portfolio->monitoredCompanies()->where(\'cnpj\', $cnpj->value)->exists():214',
        3 => NULL,
      ),
      4 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\TernaryOperatorConstantConditionRule',
        1 => NULL,
        2 => 'array_key_exists($status, self::FILTERS):273',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Livewire\\Portfolios\\Show',
        1 => 'normalizedScheduleDays',
        2 => 'App\\Livewire\\Portfolios\\Show',
        3 => 
        array (
          0 => 'f' . "\0" . 'ksort',
        ),
      ),
      1 => 
      array (
        0 => 'App\\Livewire\\Portfolios\\Show',
        1 => 'scheduleFiresOn',
        2 => 'App\\Livewire\\Portfolios\\Show',
        3 => 
        array (
        ),
      ),
      2 => 
      array (
        0 => 'App\\Livewire\\Portfolios\\Show',
        1 => 'statusConstraint',
        2 => 'App\\Livewire\\Portfolios\\Show',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureFuncCallCollector' => 
    array (
      0 => 
      array (
        0 => 'abort',
        1 => 17,
      ),
      1 => 
      array (
        0 => 'abort',
        1 => 28,
      ),
      2 => 
      array (
        0 => 'ksort',
        1 => 147,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
        1 => 'dispatch',
        2 => 257,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => 'Livewire\\WithFileUploads',
        3 => 'Livewire\\WithPagination',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\ChangeEvent.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\ChangeEvent',
        1 => 'casts',
        2 => 'App\\Models\\ChangeEvent',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Models\\ChangeEvent',
        1 => 'isAcknowledged',
        2 => 'App\\Models\\ChangeEvent',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\CompanyPartner.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\CompanySnapshot.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\CompanySnapshot',
        1 => 'casts',
        2 => 'App\\Models\\CompanySnapshot',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Concerns\\BelongsToOrganization.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitDeclarationCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\Concerns\\BelongsToOrganization',
        1 => 19,
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\MonitoredCompany',
        1 => 'casts',
        2 => 'App\\Models\\MonitoredCompany',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Models\\MonitoredCompany',
        1 => 'formattedCnpj',
        2 => 'App\\Models\\MonitoredCompany',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\support\\cnpj' . "\0" . 'tryfrom',
          1 => 'm' . "\0" . 'app\\support\\cnpj' . "\0" . 'formatted',
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Organization.php' => 
  array (
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\ConstantConditionInTraitCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeStaticMethodCallRule',
        1 => 'App\\Models\\Concerns\\BelongsToOrganization',
        2 => 'static::addGlobalScope(new \\App\\Models\\Scopes\\OrganizationScope()):23',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeStaticMethodCallRule',
        1 => 'App\\Models\\Concerns\\BelongsToOrganization',
        2 => '\\Illuminate\\Support\\Facades\\Auth::user():26',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => 'App\\Models\\Concerns\\BelongsToOrganization',
        2 => '$model->getAttribute(\'organization_id\') === null && $user instanceof \\App\\Models\\User:28',
        3 => NULL,
      ),
      3 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Models\\Concerns\\BelongsToOrganization',
        2 => '$model->getAttribute(\'organization_id\'):28',
        3 => NULL,
      ),
      4 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\StrictComparisonOfDifferentTypesRule',
        1 => 'App\\Models\\Concerns\\BelongsToOrganization',
        2 => '$model->getAttribute(\'organization_id\') === null:28',
        3 => NULL,
      ),
      5 => 
      array (
        0 => 'PHPStan\\Rules\\Classes\\ImpossibleInstanceOfRule',
        1 => 'App\\Models\\Concerns\\BelongsToOrganization',
        2 => '$user instanceof \\App\\Models\\User:28',
        3 => NULL,
      ),
      6 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Models\\Concerns\\BelongsToOrganization',
        2 => '$model->getAttribute(\'organization_id\') === null:28',
        3 => NULL,
      ),
      7 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Models\\Concerns\\BelongsToOrganization',
        2 => '$user instanceof \\App\\Models\\User:28',
        3 => NULL,
      ),
      8 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => 'App\\Models\\Concerns\\BelongsToOrganization',
        2 => '$model->getAttribute(\'organization_id\') === null && $user instanceof \\App\\Models\\User:28',
        3 => NULL,
      ),
      9 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeMethodCallRule',
        1 => 'App\\Models\\Concerns\\BelongsToOrganization',
        2 => '$model->setAttribute(\'organization_id\', $user->organization_id):29',
        3 => NULL,
      ),
      10 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\ImpossibleCheckTypeStaticMethodCallRule',
        1 => 'App\\Models\\Concerns\\BelongsToOrganization',
        2 => 'static::creating(function (\\Illuminate\\Database\\Eloquent\\Model $model): void {
    $user = \\Illuminate\\Support\\Facades\\Auth::user();
    if ($model->getAttribute(\'organization_id\') === null && $user instanceof \\App\\Models\\User) {
        $model->setAttribute(\'organization_id\', $user->organization_id);
    }
}):25',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\Portfolio',
        1 => 'casts',
        2 => 'App\\Models\\Portfolio',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Model',
        1 => 'addGlobalScope',
        2 => 23,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\Concerns\\BelongsToOrganization',
        1 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\PortfolioScheduledRun.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\PortfolioScheduledRun',
        1 => 'casts',
        2 => 'App\\Models\\PortfolioScheduledRun',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\User.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Models\\User',
        1 => 'casts',
        2 => 'App\\Models\\User',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Laravel\\Sanctum\\HasApiTokens',
        1 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        2 => 'Illuminate\\Notifications\\Notifiable',
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompaniesImported.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Notifications\\CompaniesImported',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Notifications\\CompaniesImported',
        1 => 'via',
        2 => 'App\\Notifications\\CompaniesImported',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Notifications\\CompaniesImported',
        1 => 'toArray',
        2 => 'App\\Notifications\\CompaniesImported',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\dto\\importreport' . "\0" . 'importedcount',
          1 => 'm' . "\0" . 'app\\dto\\importreport' . "\0" . 'rejectedcount',
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompanyChangeDetected.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Notifications\\CompanyChangeDetected',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Notifications\\CompanyChangeDetected',
        1 => 'via',
        2 => 'App\\Notifications\\CompanyChangeDetected',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Notifications\\CompanyChangeDetected',
        1 => 'describe',
        2 => 'App\\Notifications\\CompanyChangeDetected',
        3 => 
        array (
        ),
      ),
      2 => 
      array (
        0 => 'App\\Notifications\\CompanyChangeDetected',
        1 => 'formattedCnpj',
        2 => 'App\\Notifications\\CompanyChangeDetected',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\support\\cnpj' . "\0" . 'tryfrom',
          1 => 'm' . "\0" . 'app\\support\\cnpj' . "\0" . 'formatted',
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Policies\\MonitoredCompanyPolicy',
        1 => 'view',
        2 => 'App\\Policies\\MonitoredCompanyPolicy',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\policies\\monitoredcompanypolicy' . "\0" . 'owns',
        ),
      ),
      1 => 
      array (
        0 => 'App\\Policies\\MonitoredCompanyPolicy',
        1 => 'update',
        2 => 'App\\Policies\\MonitoredCompanyPolicy',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\policies\\monitoredcompanypolicy' . "\0" . 'owns',
        ),
      ),
      2 => 
      array (
        0 => 'App\\Policies\\MonitoredCompanyPolicy',
        1 => 'delete',
        2 => 'App\\Policies\\MonitoredCompanyPolicy',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\policies\\monitoredcompanypolicy' . "\0" . 'owns',
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Policies\\PortfolioPolicy.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Policies\\PortfolioPolicy',
        1 => 'viewAny',
        2 => 'App\\Policies\\PortfolioPolicy',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Policies\\PortfolioPolicy',
        1 => 'view',
        2 => 'App\\Policies\\PortfolioPolicy',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\policies\\portfoliopolicy' . "\0" . 'owns',
        ),
      ),
      2 => 
      array (
        0 => 'App\\Policies\\PortfolioPolicy',
        1 => 'create',
        2 => 'App\\Policies\\PortfolioPolicy',
        3 => 
        array (
        ),
      ),
      3 => 
      array (
        0 => 'App\\Policies\\PortfolioPolicy',
        1 => 'update',
        2 => 'App\\Policies\\PortfolioPolicy',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\policies\\portfoliopolicy' . "\0" . 'owns',
        ),
      ),
      4 => 
      array (
        0 => 'App\\Policies\\PortfolioPolicy',
        1 => 'delete',
        2 => 'App\\Policies\\PortfolioPolicy',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\policies\\portfoliopolicy' . "\0" . 'owns',
        ),
      ),
      5 => 
      array (
        0 => 'App\\Policies\\PortfolioPolicy',
        1 => 'owns',
        2 => 'App\\Policies\\PortfolioPolicy',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\AppServiceProvider.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\RateLimiter',
        1 => 'for',
        2 => 46,
      ),
      1 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\RateLimiter',
        1 => 'for',
        2 => 54,
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\BrasilApiProvider.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanAndConstantConditionRule',
        1 => NULL,
        2 => 'in_array($e->response->status(), self::RETRYABLE_STATUSES, true):56',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => '$response->failed():67',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\TernaryOperatorConstantConditionRule',
        1 => NULL,
        2 => 'is_array($decoded):73',
        3 => NULL,
      ),
      3 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\IfConstantConditionRule',
        1 => NULL,
        2 => '\\Illuminate\\Support\\Facades\\RateLimiter::tooManyAttempts(self::THROTTLE_KEY, $this->throttlePerMinute):88',
        3 => NULL,
      ),
      4 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => 'is_array($qsa):129',
        3 => NULL,
      ),
      5 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => 'is_array($socio):136',
        3 => NULL,
      ),
      6 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => 'is_scalar($value):172',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Providers\\Cnpj\\BrasilApiProvider',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Providers\\Cnpj\\BrasilApiProvider',
        1 => 'map',
        2 => 'App\\Providers\\Cnpj\\BrasilApiProvider',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\dto\\companydata' . "\0" . '__construct',
          1 => 'm' . "\0" . 'app\\providers\\cnpj\\brasilapiprovider' . "\0" . 'stringornull',
          2 => 'm' . "\0" . 'app\\providers\\cnpj\\brasilapiprovider' . "\0" . 'buildlogradouro',
          3 => 'm' . "\0" . 'app\\providers\\cnpj\\brasilapiprovider' . "\0" . 'mappartners',
        ),
      ),
      1 => 
      array (
        0 => 'App\\Providers\\Cnpj\\BrasilApiProvider',
        1 => 'mapPartners',
        2 => 'App\\Providers\\Cnpj\\BrasilApiProvider',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\dto\\partnerdata' . "\0" . '__construct',
          1 => 'm' . "\0" . 'app\\providers\\cnpj\\brasilapiprovider' . "\0" . 'stringornull',
        ),
      ),
      2 => 
      array (
        0 => 'App\\Providers\\Cnpj\\BrasilApiProvider',
        1 => 'buildLogradouro',
        2 => 'App\\Providers\\Cnpj\\BrasilApiProvider',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\providers\\cnpj\\brasilapiprovider' . "\0" . 'stringornull',
        ),
      ),
      3 => 
      array (
        0 => 'App\\Providers\\Cnpj\\BrasilApiProvider',
        1 => 'stringOrNull',
        2 => 'App\\Providers\\Cnpj\\BrasilApiProvider',
        3 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Support\\Facades\\RateLimiter',
        1 => 'hit',
        2 => 92,
      ),
    ),
    'PHPStan\\Rules\\Methods\\NamedArgumentParameterMethodCallsCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Http\\Client\\PendingRequest',
        1 => 'retry',
        2 => 'throw',
        3 => 57,
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\Exceptions\\CnpjProviderException.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Providers\\Cnpj\\Exceptions\\CnpjProviderException',
        1 => 'requestFailed',
        2 => 'App\\Providers\\Cnpj\\Exceptions\\CnpjProviderException',
        3 => 
        array (
        ),
      ),
      1 => 
      array (
        0 => 'App\\Providers\\Cnpj\\Exceptions\\CnpjProviderException',
        1 => 'unreachable',
        2 => 'App\\Providers\\Cnpj\\Exceptions\\CnpjProviderException',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyDiffer.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\TernaryOperatorConstantConditionRule',
        1 => NULL,
        2 => '$this->isNegativeSituacao($new):64',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\CompanyDiffer',
        1 => 'diff',
        2 => 'App\\Services\\CompanyDiffer',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\services\\companydiffer' . "\0" . 'comparesituacao',
          1 => 'm' . "\0" . 'app\\services\\companydiffer' . "\0" . 'comparepartners',
          2 => 'm' . "\0" . 'app\\services\\companydiffer' . "\0" . 'comparescalar',
        ),
      ),
      1 => 
      array (
        0 => 'App\\Services\\CompanyDiffer',
        1 => 'compareSituacao',
        2 => 'App\\Services\\CompanyDiffer',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\services\\companydiffer' . "\0" . 'isnegativesituacao',
          1 => 'm' . "\0" . 'app\\dto\\changeeventdata' . "\0" . '__construct',
        ),
      ),
      2 => 
      array (
        0 => 'App\\Services\\CompanyDiffer',
        1 => 'comparePartners',
        2 => 'App\\Services\\CompanyDiffer',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\services\\companydiffer' . "\0" . 'keypartners',
          1 => 'm' . "\0" . 'app\\dto\\changeeventdata' . "\0" . '__construct',
          2 => 'm' . "\0" . 'app\\services\\companydiffer' . "\0" . 'partnerlabel',
        ),
      ),
      3 => 
      array (
        0 => 'App\\Services\\CompanyDiffer',
        1 => 'compareScalar',
        2 => 'App\\Services\\CompanyDiffer',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\services\\companydiffer' . "\0" . 'normalize',
          1 => 'm' . "\0" . 'app\\dto\\changeeventdata' . "\0" . '__construct',
        ),
      ),
      4 => 
      array (
        0 => 'App\\Services\\CompanyDiffer',
        1 => 'keyPartners',
        2 => 'App\\Services\\CompanyDiffer',
        3 => 
        array (
        ),
      ),
      5 => 
      array (
        0 => 'App\\Services\\CompanyDiffer',
        1 => 'partnerLabel',
        2 => 'App\\Services\\CompanyDiffer',
        3 => 
        array (
        ),
      ),
      6 => 
      array (
        0 => 'App\\Services\\CompanyDiffer',
        1 => 'isNegativeSituacao',
        2 => 'App\\Services\\CompanyDiffer',
        3 => 
        array (
        ),
      ),
      7 => 
      array (
        0 => 'App\\Services\\CompanyDiffer',
        1 => 'normalize',
        2 => 'App\\Services\\CompanyDiffer',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Services\\CompanyImporter',
        1 => 'importFromFile',
        2 => 'App\\Services\\CompanyImporter',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\services\\companyimporter' . "\0" . 'import',
          1 => 'm' . "\0" . 'app\\services\\companyimporter' . "\0" . 'readcsv',
        ),
      ),
      1 => 
      array (
        0 => 'App\\Services\\CompanyImporter',
        1 => 'sniffDelimiter',
        2 => 'App\\Services\\CompanyImporter',
        3 => 
        array (
          0 => 'f' . "\0" . 'fopen',
          1 => 'f' . "\0" . 'fgets',
          2 => 'f' . "\0" . 'fclose',
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Support\\Cnpj.php' => 
  array (
    'PHPStan\\Rules\\Comparison\\FunctionCallConstantConditionCollector' => 
    array (
      0 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => 'self::isValidNormalized($normalized):33',
        3 => NULL,
      ),
      1 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\TernaryOperatorConstantConditionRule',
        1 => NULL,
        2 => 'self::isValid($raw):45',
        3 => NULL,
      ),
      2 => 
      array (
        0 => 'PHPStan\\Rules\\Comparison\\BooleanNotConstantConditionRule',
        1 => NULL,
        2 => 'ctype_digit($cnpj):92',
        3 => NULL,
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\ConstructorWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Support\\Cnpj',
        1 => 
        array (
        ),
      ),
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Support\\Cnpj',
        1 => 'normalize',
        2 => 'App\\Support\\Cnpj',
        3 => 
        array (
          0 => 'f' . "\0" . 'preg_replace',
        ),
      ),
      1 => 
      array (
        0 => 'App\\Support\\Cnpj',
        1 => 'isValid',
        2 => 'App\\Support\\Cnpj',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\support\\cnpj' . "\0" . 'isvalidnormalized',
          1 => 'm' . "\0" . 'app\\support\\cnpj' . "\0" . 'normalize',
        ),
      ),
      2 => 
      array (
        0 => 'App\\Support\\Cnpj',
        1 => 'isValidNormalized',
        2 => 'App\\Support\\Cnpj',
        3 => 
        array (
          0 => 'f' . "\0" . 'preg_match',
          1 => 'm' . "\0" . 'app\\support\\cnpj' . "\0" . 'checkdigit',
        ),
      ),
      3 => 
      array (
        0 => 'App\\Support\\Cnpj',
        1 => 'checkDigit',
        2 => 'App\\Support\\Cnpj',
        3 => 
        array (
        ),
      ),
      4 => 
      array (
        0 => 'App\\Support\\Cnpj',
        1 => 'formatted',
        2 => 'App\\Support\\Cnpj',
        3 => 
        array (
          0 => 'f' . "\0" . 'preg_replace',
        ),
      ),
      5 => 
      array (
        0 => 'App\\Support\\Cnpj',
        1 => '__toString',
        2 => 'App\\Support\\Cnpj',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Support\\Exceptions\\InvalidCnpjException.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\Support\\Exceptions\\InvalidCnpjException',
        1 => 'for',
        2 => 'App\\Support\\Exceptions\\InvalidCnpjException',
        3 => 
        array (
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\View\\Components\\AppLayout.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector' => 
    array (
      0 => 'layouts.app',
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\View\\Components\\AppLayout',
        1 => 'render',
        2 => 'App\\View\\Components\\AppLayout',
        3 => 
        array (
          0 => 'f' . "\0" . 'view',
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\View\\Components\\GuestLayout.php' => 
  array (
    'Larastan\\Larastan\\Collectors\\UsedViewFunctionCollector' => 
    array (
      0 => 'layouts.guest',
    ),
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'App\\View\\Components\\GuestLayout',
        1 => 'render',
        2 => 'App\\View\\Components\\GuestLayout',
        3 => 
        array (
          0 => 'f' . "\0" . 'view',
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\MethodWithoutImpurePointsCollector' => 
    array (
      0 => 
      array (
        0 => 'Database\\Factories\\ChangeEventFactory',
        1 => 'definition',
        2 => 'Database\\Factories\\ChangeEventFactory',
        3 => 
        array (
          0 => 'm' . "\0" . 'app\\models\\monitoredcompany' . "\0" . 'factory',
          1 => 'm' . "\0" . 'app\\models\\companysnapshot' . "\0" . 'factory',
          2 => 'f' . "\0" . 'now',
        ),
      ),
    ),
  ),
  'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php' => 
  array (
    'PHPStan\\Rules\\DeadCode\\PossiblyPureStaticCallCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Builder',
        1 => 'create',
        2 => 27,
      ),
    ),
    'PHPStan\\Rules\\Traits\\TraitUseCollector' => 
    array (
      0 => 
      array (
        0 => 'Illuminate\\Database\\Console\\Seeds\\WithoutModelEvents',
      ),
    ),
  ),
); },
	'dependencies' => array (
  'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php' => 
  array (
    'fileHash' => '9b4156b19ae2732fd8de8cb3548d2a0bfdd456adee1ce38e57710f6df7c1b10b',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php' => 
  array (
    'fileHash' => '09fa74a068d850d9244f9f37e9b630cc707d98bcc4597bae313224df9f02519d',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Console\\Commands\\RunScheduledPortfolioRefreshes.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Console\\Commands\\IssueApiToken.php' => 
  array (
    'fileHash' => 'bbbcfbedaa8905b8e29a8f2f961c9530bb1bfeb9d49708bad79ada94cb500ae3',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Console\\Commands\\RunScheduledPortfolioRefreshes.php' => 
  array (
    'fileHash' => '490634e567f8c8e3a75a41631eb7fb4f2e6e736c2430e2b7554d8f69b1e7d194',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Contracts\\CnpjDataProvider.php' => 
  array (
    'fileHash' => '2fefb735de786c9e694c916b23fd229fcd4f9a1c60ca4bbedf04ab9b2e9b6c18',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Providers\\AppServiceProvider.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\BrasilApiProvider.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\ChangeEventData.php' => 
  array (
    'fileHash' => 'fb47cf4f9f134f52008d52f04ac4165b4cd0444039fe915d412b0f23f499eb9f',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyDiffer.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\CompanyData.php' => 
  array (
    'fileHash' => '91478d177c239304b744519c6369c3ce5bcc454ff87c1b04848c69cad3242f0d',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Contracts\\CnpjDataProvider.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\BrasilApiProvider.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Services\\SnapshotService.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\ImportReport.php' => 
  array (
    'fileHash' => '4c46c15ddcf25a3f6db799fb7effdc5441c5fa2dbd49612baebb6db6892a66a7',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompaniesImported.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\PartnerData.php' => 
  array (
    'fileHash' => 'e8957462b3009ab830062095dc4d21b713757ead4dc603cc285bda393d030d0d',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\DTO\\CompanyData.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\BrasilApiProvider.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Services\\SnapshotService.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Enums\\ChangeType.php' => 
  array (
    'fileHash' => '2aa8b1255c2d4c5a82c2dca04e15862793a40fe14506cc1d6319bf80106e61a0',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\DTO\\ChangeEventData.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php',
      5 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php',
      6 => 'C:\\laragon\\www\\vigilo\\app\\Models\\ChangeEvent.php',
      7 => 'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php',
      8 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompanyChangeDetected.php',
      9 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyDiffer.php',
      10 => 'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Enums\\RefreshStatus.php' => 
  array (
    'fileHash' => 'caa76bc27660662b381cb5f9c623b802808e98cca56bfa8fe36c572aee5b497a',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php',
      5 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php',
      6 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php',
      7 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
      8 => 'C:\\laragon\\www\\vigilo\\app\\Models\\ChangeEvent.php',
      9 => 'C:\\laragon\\www\\vigilo\\app\\Models\\CompanySnapshot.php',
      10 => 'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php',
      11 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php',
      12 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompanyChangeDetected.php',
      13 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php',
      14 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php',
      15 => 'C:\\laragon\\www\\vigilo\\app\\Services\\SnapshotService.php',
      16 => 'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php',
      17 => 'C:\\laragon\\www\\vigilo\\database\\factories\\CompanySnapshotFactory.php',
      18 => 'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Enums\\Severity.php' => 
  array (
    'fileHash' => '21cb3594002ed2c91adbb52c67885984ffcdddbf52e1ec9bccb92933af33f9b9',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\DTO\\ChangeEventData.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php',
      5 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php',
      6 => 'C:\\laragon\\www\\vigilo\\app\\Models\\ChangeEvent.php',
      7 => 'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php',
      8 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompanyChangeDetected.php',
      9 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyDiffer.php',
      10 => 'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php' => 
  array (
    'fileHash' => '54b3437b0492337dfd4294b932ffd59066929e30df94e16d252e0f9fce1397c9',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Auth\\VerifyEmailController.php' => 
  array (
    'fileHash' => 'd33fb522e3610364dca2391076aae007c56018df1f6b2b201667da428af697f8',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Controller.php' => 
  array (
    'fileHash' => '09e5cac5a69959ccf23d756cda697b993df937c106655d124ff27ca5fe24a705',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Auth\\VerifyEmailController.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php' => 
  array (
    'fileHash' => '64c44ec317ccb79902f5fddc09b4608f37eb7067e826d3fec0dd94d5dbaa657a',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php' => 
  array (
    'fileHash' => '158f01189507dec37aff6574398766cf91d3465a54a7690d22176284f2918245',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Actions\\Logout.php' => 
  array (
    'fileHash' => '2e97be3671f060ca8d24a1565f24ba64b77f5085df7007fecd7e8977f3a6a265',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php' => 
  array (
    'fileHash' => '3e588b610f8f02cfd7c45aaab2762b3dc1953d735be91c7487ec01ea60f252ee',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php' => 
  array (
    'fileHash' => 'ab43604464353a87bdefe04ddf60c24eb9fda53cff6431bd72baf7f785cf1aac',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Concerns\\InteractsWithCurrentOrganization.php' => 
  array (
    'fileHash' => 'f3e8e378e67fbf3b105ad67c44eb1404b8dfefd2c514ad7a575bd5e3571ee00d',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
    ),
    'usedTraitDependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php' => 
  array (
    'fileHash' => '55ae19ab9ef32f659a20629e6533a72f8f42a3e9fcb446e68e0d4a86755bd29e',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Forms\\LoginForm.php' => 
  array (
    'fileHash' => '73b57e08a448424a197ec2bd71b2fa1c8fd592900b5e8313d58099857e197a43',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php' => 
  array (
    'fileHash' => '3297b0c7971bb0b1ce4e19828c596b626f2678d32a2f62ace25e9a8565bedb87',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php' => 
  array (
    'fileHash' => '96f8f58eec789e9951138584378ed601b4cc4d419bd76919636a94526c5aba26',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\ChangeEvent.php' => 
  array (
    'fileHash' => '04cdf530b41ec96ac66f5ff38b29bac1bdf9decacaa94599188c59203f08d800',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php',
      5 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php',
      6 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php',
      7 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
      8 => 'C:\\laragon\\www\\vigilo\\app\\Models\\CompanySnapshot.php',
      9 => 'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php',
      10 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php',
      11 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompanyChangeDetected.php',
      12 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php',
      13 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php',
      14 => 'C:\\laragon\\www\\vigilo\\app\\Services\\SnapshotService.php',
      15 => 'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php',
      16 => 'C:\\laragon\\www\\vigilo\\database\\factories\\CompanySnapshotFactory.php',
      17 => 'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\CompanyPartner.php' => 
  array (
    'fileHash' => 'b2b4bed1748daf292554ea1ddf6baef7391a50f66ff128cfe9d3856f3f7f3e76',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Models\\ChangeEvent.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Models\\CompanySnapshot.php',
      5 => 'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php',
      6 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyDiffer.php',
      7 => 'C:\\laragon\\www\\vigilo\\app\\Services\\SnapshotService.php',
      8 => 'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php',
      9 => 'C:\\laragon\\www\\vigilo\\database\\factories\\CompanyPartnerFactory.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\CompanySnapshot.php' => 
  array (
    'fileHash' => '3e0f6b0b5c1195bbbce457ff0a3249e6a9871d7fc01807224dbb526564a9694e',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php',
      5 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php',
      6 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php',
      7 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
      8 => 'C:\\laragon\\www\\vigilo\\app\\Models\\ChangeEvent.php',
      9 => 'C:\\laragon\\www\\vigilo\\app\\Models\\CompanyPartner.php',
      10 => 'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php',
      11 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php',
      12 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompanyChangeDetected.php',
      13 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php',
      14 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyDiffer.php',
      15 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php',
      16 => 'C:\\laragon\\www\\vigilo\\app\\Services\\SnapshotService.php',
      17 => 'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php',
      18 => 'C:\\laragon\\www\\vigilo\\database\\factories\\CompanyPartnerFactory.php',
      19 => 'C:\\laragon\\www\\vigilo\\database\\factories\\CompanySnapshotFactory.php',
      20 => 'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Concerns\\BelongsToOrganization.php' => 
  array (
    'fileHash' => '7736cbf860e1488ec1a6da861371084f63ea87c95574b0da9225126668dbbca0',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php',
      5 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php',
      6 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php',
      7 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
      8 => 'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php',
      9 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Organization.php',
      10 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php',
      11 => 'C:\\laragon\\www\\vigilo\\app\\Models\\PortfolioScheduledRun.php',
      12 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompaniesImported.php',
      13 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php',
      14 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\PortfolioPolicy.php',
      15 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php',
      16 => 'C:\\laragon\\www\\vigilo\\database\\factories\\MonitoredCompanyFactory.php',
      17 => 'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioScheduledRunFactory.php',
      18 => 'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php',
    ),
    'usedTraitDependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php' => 
  array (
    'fileHash' => '28b64c40543042a2b2b0f9dc32d4a909b707311297561d1a5a4e1d7a89a16a51',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      5 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php',
      6 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php',
      7 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php',
      8 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php',
      9 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
      10 => 'C:\\laragon\\www\\vigilo\\app\\Models\\ChangeEvent.php',
      11 => 'C:\\laragon\\www\\vigilo\\app\\Models\\CompanyPartner.php',
      12 => 'C:\\laragon\\www\\vigilo\\app\\Models\\CompanySnapshot.php',
      13 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Organization.php',
      14 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php',
      15 => 'C:\\laragon\\www\\vigilo\\app\\Models\\PortfolioScheduledRun.php',
      16 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompaniesImported.php',
      17 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompanyChangeDetected.php',
      18 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php',
      19 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\PortfolioPolicy.php',
      20 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyDiffer.php',
      21 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php',
      22 => 'C:\\laragon\\www\\vigilo\\app\\Services\\SnapshotService.php',
      23 => 'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php',
      24 => 'C:\\laragon\\www\\vigilo\\database\\factories\\CompanyPartnerFactory.php',
      25 => 'C:\\laragon\\www\\vigilo\\database\\factories\\CompanySnapshotFactory.php',
      26 => 'C:\\laragon\\www\\vigilo\\database\\factories\\MonitoredCompanyFactory.php',
      27 => 'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioScheduledRunFactory.php',
      28 => 'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Organization.php' => 
  array (
    'fileHash' => '17ac6e2c0593b9997e12dc36c332a3a1c6ab83eefbefec81e2a7fb63a0aa1494',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Console\\Commands\\IssueApiToken.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php',
      5 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      6 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php',
      7 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php',
      8 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php',
      9 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php',
      10 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
      11 => 'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php',
      12 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php',
      13 => 'C:\\laragon\\www\\vigilo\\app\\Models\\PortfolioScheduledRun.php',
      14 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Scopes\\OrganizationScope.php',
      15 => 'C:\\laragon\\www\\vigilo\\app\\Models\\User.php',
      16 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompaniesImported.php',
      17 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php',
      18 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\PortfolioPolicy.php',
      19 => 'C:\\laragon\\www\\vigilo\\app\\Providers\\AppServiceProvider.php',
      20 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php',
      21 => 'C:\\laragon\\www\\vigilo\\database\\factories\\MonitoredCompanyFactory.php',
      22 => 'C:\\laragon\\www\\vigilo\\database\\factories\\OrganizationFactory.php',
      23 => 'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioFactory.php',
      24 => 'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioScheduledRunFactory.php',
      25 => 'C:\\laragon\\www\\vigilo\\database\\factories\\UserFactory.php',
      26 => 'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php' => 
  array (
    'fileHash' => '7b87d7205006639c583dc4872b152df05952185c8a1772d6f9f37f4fd1138e32',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      5 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php',
      6 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php',
      7 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php',
      8 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php',
      9 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
      10 => 'C:\\laragon\\www\\vigilo\\app\\Models\\ChangeEvent.php',
      11 => 'C:\\laragon\\www\\vigilo\\app\\Models\\CompanySnapshot.php',
      12 => 'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php',
      13 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Organization.php',
      14 => 'C:\\laragon\\www\\vigilo\\app\\Models\\PortfolioScheduledRun.php',
      15 => 'C:\\laragon\\www\\vigilo\\app\\Models\\User.php',
      16 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompaniesImported.php',
      17 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompanyChangeDetected.php',
      18 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php',
      19 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\PortfolioPolicy.php',
      20 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php',
      21 => 'C:\\laragon\\www\\vigilo\\app\\Services\\SnapshotService.php',
      22 => 'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php',
      23 => 'C:\\laragon\\www\\vigilo\\database\\factories\\CompanySnapshotFactory.php',
      24 => 'C:\\laragon\\www\\vigilo\\database\\factories\\MonitoredCompanyFactory.php',
      25 => 'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioFactory.php',
      26 => 'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioScheduledRunFactory.php',
      27 => 'C:\\laragon\\www\\vigilo\\database\\factories\\UserFactory.php',
      28 => 'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\PortfolioScheduledRun.php' => 
  array (
    'fileHash' => '10d7f72bb437420f90e3a81b151e3b453181842e6cfcfc9225670a3b86c3dd25',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php',
      5 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php',
      6 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php',
      7 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
      8 => 'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php',
      9 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Organization.php',
      10 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php',
      11 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompaniesImported.php',
      12 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php',
      13 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\PortfolioPolicy.php',
      14 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php',
      15 => 'C:\\laragon\\www\\vigilo\\database\\factories\\MonitoredCompanyFactory.php',
      16 => 'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioScheduledRunFactory.php',
      17 => 'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Scopes\\OrganizationScope.php' => 
  array (
    'fileHash' => '2ccdd53c8844f7d0a5a0c644c37b582826a0e6162c4242e8944321675614c321',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\User.php' => 
  array (
    'fileHash' => '2ec5de35b06a382872fc17322b1383cf156ae134330b50df29180a3adbd6ff26',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Console\\Commands\\IssueApiToken.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php',
      5 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php',
      6 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php',
      7 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php',
      8 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
      9 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Organization.php',
      10 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php',
      11 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Scopes\\OrganizationScope.php',
      12 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php',
      13 => 'C:\\laragon\\www\\vigilo\\app\\Policies\\PortfolioPolicy.php',
      14 => 'C:\\laragon\\www\\vigilo\\app\\Providers\\AppServiceProvider.php',
      15 => 'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioFactory.php',
      16 => 'C:\\laragon\\www\\vigilo\\database\\factories\\UserFactory.php',
      17 => 'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompaniesImported.php' => 
  array (
    'fileHash' => '19b94be1dcc03f671cb58c9f73d31ae4cb4feb042e679527cef7351574f78614',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompanyChangeDetected.php' => 
  array (
    'fileHash' => '209e337eac59d7a2374baab8ecf64d615b8f90d0bd2a7a6963cc02faf759fcf4',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php' => 
  array (
    'fileHash' => '7d8defe8bb086a9ce40c348882f76bfda585e806d75dafea9e82af0acff2a680',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Policies\\PortfolioPolicy.php' => 
  array (
    'fileHash' => '9315f984855fce8c50dff8749a88d9fbf4c8775d2166e9b530e2044df9b4efd8',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\AppServiceProvider.php' => 
  array (
    'fileHash' => 'b1d5988f2ad301ad94b8a50f40d40bf5b248609c8f6410776fe6e0b375454015',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\BrasilApiProvider.php' => 
  array (
    'fileHash' => '190ae36d635a9e153090b8f43d492a998703e433c0c73015ddc03824732c814e',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Providers\\AppServiceProvider.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\Exceptions\\CnpjProviderException.php' => 
  array (
    'fileHash' => '006c32426102cfb227799369d7e9bebfecacf137d87228f520131fd8c77a22b5',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Contracts\\CnpjDataProvider.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\BrasilApiProvider.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\VoltServiceProvider.php' => 
  array (
    'fileHash' => '4b0f313a107b9c3c17a7dd9036dd36a06745aa51dae27ef1d066134aa657b411',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyDiffer.php' => 
  array (
    'fileHash' => 'd8291ff1c32e4a94b56251b799ad583da6ecc13a0bff9d78a790fcb8dd3c6ec2',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php' => 
  array (
    'fileHash' => '8915c8fd62f517d7a11ca8faf21e063f28f4d9c3116ce3ad97a5f4a046c89a00',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Services\\SnapshotService.php' => 
  array (
    'fileHash' => 'bf02ab90411c4e8258495a4de4118ccf2984722fd05e70aaac4af97f80f6319e',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Support\\Cnpj.php' => 
  array (
    'fileHash' => '58db6f8e4c34ce0a87266d4e85532f9086bc29dc75840c0ec26e7a8ffa40f2f6',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php',
      3 => 'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompanyChangeDetected.php',
      4 => 'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\BrasilApiProvider.php',
      5 => 'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php',
      6 => 'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Support\\Exceptions\\InvalidCnpjException.php' => 
  array (
    'fileHash' => '6371aedacdf23b7d8e406c88d7aeb4a8eb4daf0a19c9b152a30b98ea0e7d1233',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Contracts\\CnpjDataProvider.php',
      1 => 'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\BrasilApiProvider.php',
      2 => 'C:\\laragon\\www\\vigilo\\app\\Support\\Cnpj.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\View\\Components\\AppLayout.php' => 
  array (
    'fileHash' => '3ca6426929b27a88ad8fa55304249004be01d1f436e0f1194bd81488888f55ab',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\app\\View\\Components\\GuestLayout.php' => 
  array (
    'fileHash' => '4f0964e6c277da1854b3c369f390361c7d8517884f79878fc5b355da5dc67322',
    'dependentFiles' => 
    array (
    ),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php' => 
  array (
    'fileHash' => '3da9cc9f75a37394789c268539671aadece32d15f332c14e8900db64c3aa6100',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Models\\ChangeEvent.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\CompanyPartnerFactory.php' => 
  array (
    'fileHash' => 'f6fcdfffba2633dfc0b05351f6bee2cb070885ebaefb4b8f486062453889c602',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Models\\CompanyPartner.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\CompanySnapshotFactory.php' => 
  array (
    'fileHash' => '86ad3152bb59dd67d8d48f2e893e76fd05663afaf1fa0027038e576ab34167fd',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Models\\CompanySnapshot.php',
      1 => 'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php',
      2 => 'C:\\laragon\\www\\vigilo\\database\\factories\\CompanyPartnerFactory.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\MonitoredCompanyFactory.php' => 
  array (
    'fileHash' => 'ffac31d6d3df5b81cfdf11a4b0e78a591fd9ba65b559bffccf86b0e22bd38be5',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php',
      1 => 'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php',
      2 => 'C:\\laragon\\www\\vigilo\\database\\factories\\CompanySnapshotFactory.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\OrganizationFactory.php' => 
  array (
    'fileHash' => '3b8b583c36a5e1318eb92ebc3d48ce954ffc20b0b2c99f66796a2b78232d7933',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Organization.php',
      1 => 'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioFactory.php',
      2 => 'C:\\laragon\\www\\vigilo\\database\\factories\\UserFactory.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioFactory.php' => 
  array (
    'fileHash' => 'de52361cea2aa3dd55df4cb70d19060265d012de35119c0c0baf8f50c54af721',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php',
      1 => 'C:\\laragon\\www\\vigilo\\database\\factories\\MonitoredCompanyFactory.php',
      2 => 'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioScheduledRunFactory.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioScheduledRunFactory.php' => 
  array (
    'fileHash' => '2fd7b294ceeb08912bf4619630e845a7d956b025606f6236e7e952a4422b22bb',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Models\\PortfolioScheduledRun.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\UserFactory.php' => 
  array (
    'fileHash' => 'bca6303acb7ebbf1c7c854d468bc11fcce7e448904cbb595dffc25a87cbfd256',
    'dependentFiles' => 
    array (
      0 => 'C:\\laragon\\www\\vigilo\\app\\Models\\User.php',
    ),
  ),
  'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php' => 
  array (
    'fileHash' => '3a0b8252e184cfe223e5dcb42f0df3d78ff3802e46e5751cd21e1c35edb4a25f',
    'dependentFiles' => 
    array (
    ),
  ),
),
	'packageDependencies' => array (
  'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php' => 
  array (
    0 => 'nesbot/carbon',
    1 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Console\\Commands\\RunScheduledPortfolioRefreshes.php' => 
  array (
    0 => 'symfony/console',
    1 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Contracts\\CnpjDataProvider.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\ChangeEventData.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\app\\Enums\\Severity.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'laravel/sanctum',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Actions\\Logout.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'symfony/http-foundation',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Forms\\LoginForm.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'livewire/livewire',
    2 => 'symfony/http-foundation',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'livewire/livewire',
    2 => 'laravel/sanctum',
    3 => 'symfony/http-foundation',
    4 => 'nesbot/carbon',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\ChangeEvent.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'nesbot/carbon',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Concerns\\BelongsToOrganization.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Scopes\\OrganizationScope.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'laravel/sanctum',
    2 => 'symfony/http-foundation',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\User.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'laravel/sanctum',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompaniesImported.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompanyChangeDetected.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\VoltServiceProvider.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'livewire/volt',
    2 => 'livewire/livewire',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyDiffer.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Support\\Exceptions\\InvalidCnpjException.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\UserFactory.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'fakerphp/faker',
    2 => 'nesbot/carbon',
  ),
  'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'nesbot/carbon',
    2 => 'laravel/sanctum',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Enums\\ChangeType.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\app\\Enums\\RefreshStatus.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'symfony/http-foundation',
    2 => 'laravel/sanctum',
    3 => 'nesbot/carbon',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Auth\\VerifyEmailController.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'symfony/http-foundation',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Controller.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'nesbot/carbon',
    2 => 'laravel/sanctum',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'livewire/livewire',
    2 => 'laravel/sanctum',
    3 => 'symfony/http-foundation',
    4 => 'nesbot/carbon',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Concerns\\InteractsWithCurrentOrganization.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'livewire/livewire',
    2 => 'laravel/sanctum',
    3 => 'symfony/http-foundation',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\CompanyPartner.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Organization.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'laravel/sanctum',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'laravel/sanctum',
    2 => 'symfony/http-foundation',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\PortfolioScheduledRun.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\AppServiceProvider.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'psr/container',
    2 => 'symfony/http-foundation',
    3 => 'laravel/sanctum',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\Exceptions\\CnpjProviderException.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Services\\SnapshotService.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'nesbot/carbon',
  ),
  'C:\\laragon\\www\\vigilo\\app\\View\\Components\\GuestLayout.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\CompanySnapshotFactory.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'nesbot/carbon',
    2 => 'fakerphp/faker',
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioFactory.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'fakerphp/faker',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Console\\Commands\\IssueApiToken.php' => 
  array (
    0 => 'symfony/console',
    1 => 'laravel/framework',
    2 => 'laravel/sanctum',
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\CompanyData.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\ImportReport.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\PartnerData.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'livewire/livewire',
    2 => 'laravel/sanctum',
    3 => 'symfony/http-foundation',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'livewire/livewire',
    2 => 'laravel/sanctum',
    3 => 'symfony/http-foundation',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\CompanySnapshot.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'laravel/sanctum',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Policies\\PortfolioPolicy.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'laravel/sanctum',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\BrasilApiProvider.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\app\\Support\\Cnpj.php' => 
  array (
  ),
  'C:\\laragon\\www\\vigilo\\app\\View\\Components\\AppLayout.php' => 
  array (
    0 => 'laravel/framework',
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'nesbot/carbon',
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\CompanyPartnerFactory.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'fakerphp/faker',
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\MonitoredCompanyFactory.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'fakerphp/faker',
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\OrganizationFactory.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'fakerphp/faker',
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioScheduledRunFactory.php' => 
  array (
    0 => 'laravel/framework',
    1 => 'fakerphp/faker',
    2 => 'nesbot/carbon',
  ),
),
	'exportedNodesCallback' => static function (): array { return array (
  'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Actions\\QueuePortfolioRefresh',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Marca as empresas-alvo de um portfólio como "pendentes" (limpa status/erro) e
 * (re)enfileira um refresh para cada uma, em chunks. A dedup de fila
 * (ShouldBeUnique no job) evita jobs repetidos.
 *
 * Usado tanto ao atualizar pela lista de portfólios quanto de dentro do
 * portfólio, garantindo estado idêntico (pendente + progresso) nos dois caminhos.
 */',
         'namespace' => 'App\\Actions',
         'uses' => 
        array (
          'refreshmonitoredcompanyjob' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          'monitoredcompany' => 'App\\Models\\MonitoredCompany',
          'portfolio' => 'App\\Models\\Portfolio',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  (callable(Builder<MonitoredCompany>): void)|null  $constrain  filtro opcional (ex.: só erros/não encontrados)
     * @return int quantas empresas foram enfileiradas
     */',
             'namespace' => 'App\\Actions',
             'uses' => 
            array (
              'refreshmonitoredcompanyjob' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'portfolio' => 'App\\Models\\Portfolio',
              'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'portfolio',
               'type' => 'App\\Models\\Portfolio',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'constrain',
               'type' => '?callable',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Actions\\RunDuePortfolioSchedules',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Dispara os refreshes dos portfólios agendados para uma data e registra cada
 * execução. Chamado 1×/dia pelo scheduler.
 *
 * O dispatch usa QueuePortfolioRefresh (jobs ShouldBeUnique), então o lote é
 * naturalmente deduplicado — sem o problema do antigo Bus::batch (DT-1).
 */',
         'namespace' => 'App\\Actions',
         'uses' => 
        array (
          'portfolio' => 'App\\Models\\Portfolio',
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'refresher',
               'type' => 'App\\Actions\\QueuePortfolioRefresh',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return int número de portfólios executados nesta data
     */',
             'namespace' => 'App\\Actions',
             'uses' => 
            array (
              'portfolio' => 'App\\Models\\Portfolio',
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'date',
               'type' => '?Carbon\\CarbonImmutable',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Console\\Commands\\IssueApiToken.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\IssueApiToken',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Emite um token Bearer (Sanctum) para a API pública de CNPJ. O token herda a
 * organização do usuário informado — é o que escopa o que a API pode devolver.
 */',
         'namespace' => 'App\\Console\\Commands',
         'uses' => 
        array (
          'user' => 'App\\Models\\User',
          'command' => 'Illuminate\\Console\\Command',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Console\\Commands\\RunScheduledPortfolioRefreshes.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Console\\Commands\\RunScheduledPortfolioRefreshes',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Console\\Command',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'signature',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'description',
          ),
           'phpDoc' => NULL,
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'action',
               'type' => 'App\\Actions\\RunDuePortfolioSchedules',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Contracts\\CnpjDataProvider.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedInterfaceNode::__set_state(array(
       'name' => 'App\\Contracts\\CnpjDataProvider',
       'phpDoc' => NULL,
       'extends' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fetch',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Fetch normalized cadastral data for a CNPJ.
     *
     * Returns null when the company is not found (HTTP 404). Throws when the
     * input is not a valid CNPJ, or when the upstream service fails after the
     * configured retries (timeout, rate limit, 5xx).
     *
     * @throws InvalidCnpjException
     * @throws CnpjProviderException
     */',
             'namespace' => 'App\\Contracts',
             'uses' => 
            array (
              'companydata' => 'App\\DTO\\CompanyData',
              'cnpjproviderexception' => 'App\\Providers\\Cnpj\\Exceptions\\CnpjProviderException',
              'invalidcnpjexception' => 'App\\Support\\Exceptions\\InvalidCnpjException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => '?App\\DTO\\CompanyData',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'cnpj',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\ChangeEventData.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\DTO\\ChangeEventData',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * A single detected change between two snapshots.
 *
 * Produced by {@see CompanyDiffer} (a pure function) and later
 * persisted as a change_events row by the refresh job. It intentionally carries
 * no timestamps or foreign keys — those belong to the persistence layer, not to
 * the diffing logic.
 */',
         'namespace' => 'App\\DTO',
         'uses' => 
        array (
          'changetype' => 'App\\Enums\\ChangeType',
          'severity' => 'App\\Enums\\Severity',
          'companydiffer' => 'App\\Services\\CompanyDiffer',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'type',
               'type' => 'App\\Enums\\ChangeType',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'field',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'oldValue',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'newValue',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'severity',
               'type' => 'App\\Enums\\Severity',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\CompanyData.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\DTO\\CompanyData',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Normalized representation of a company\'s Receita cadastral data, decoupled
 * from any specific provider\'s response shape.
 */',
         'namespace' => 'App\\DTO',
         'uses' => 
        array (
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  list<PartnerData>  $partners
     * @param  array<array-key, mixed>  $raw  The provider\'s untouched payload (persisted as raw_json).
     */',
             'namespace' => 'App\\DTO',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'cnpj',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'razaoSocial',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'nomeFantasia',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'situacaoCadastral',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'situacaoData',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            5 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'cnaePrincipal',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            6 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'porte',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            7 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'naturezaJuridica',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            8 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'logradouro',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            9 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'municipio',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            10 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'uf',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            11 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'partners',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            12 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'raw',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\ImportReport.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\DTO\\ImportReport',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Outcome of a CSV import: which CNPJs were created and which rows were rejected
 * (with the reason and the originating line, for a legible report).
 */',
         'namespace' => 'App\\DTO',
         'uses' => 
        array (
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  list<string>  $imported  Normalized CNPJs that were created.
     * @param  list<array{line: int, value: string, reason: string}>  $rejected
     */',
             'namespace' => 'App\\DTO',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'imported',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rejected',
               'type' => 'array',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'importedCount',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rejectedCount',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'rejectedWithReason',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return list<array{line: int, value: string, reason: string}>
     */',
             'namespace' => 'App\\DTO',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'reason',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\DTO\\PartnerData.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\DTO\\PartnerData',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * A single partner (sócio) as returned by a CNPJ data provider.
 *
 * The `documento` is typically a masked CPF (e.g. "***123456**") for natural
 * persons. It is stored only to monitor the client\'s own portfolio (KYB) —
 * never exported for outreach. See README (LGPD).
 */',
         'namespace' => 'App\\DTO',
         'uses' => 
        array (
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'nome',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'documento',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'qualificacao',
               'type' => '?string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Enums\\ChangeType.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedEnumNode::__set_state(array(
       'name' => 'App\\Enums\\ChangeType',
       'scalarType' => 'string',
       'phpDoc' => NULL,
       'implements' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'SituacaoChanged',
           'value' => '\'situacao_changed\'',
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'PartnerAdded',
           'value' => '\'partner_added\'',
           'phpDoc' => NULL,
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'PartnerRemoved',
           'value' => '\'partner_removed\'',
           'phpDoc' => NULL,
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'AddressChanged',
           'value' => '\'address_changed\'',
           'phpDoc' => NULL,
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'CnaeChanged',
           'value' => '\'cnae_changed\'',
           'phpDoc' => NULL,
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'PorteChanged',
           'value' => '\'porte_changed\'',
           'phpDoc' => NULL,
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'NameChanged',
           'value' => '\'name_changed\'',
           'phpDoc' => NULL,
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'label',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Enums\\RefreshStatus.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedEnumNode::__set_state(array(
       'name' => 'App\\Enums\\RefreshStatus',
       'scalarType' => 'string',
       'phpDoc' => NULL,
       'implements' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Ok',
           'value' => '\'ok\'',
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'NotFound',
           'value' => '\'nao_encontrado\'',
           'phpDoc' => NULL,
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Error',
           'value' => '\'erro\'',
           'phpDoc' => NULL,
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'label',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'badgeClasses',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Enums\\Severity.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedEnumNode::__set_state(array(
       'name' => 'App\\Enums\\Severity',
       'scalarType' => 'string',
       'phpDoc' => NULL,
       'implements' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Critical',
           'value' => '\'critical\'',
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'High',
           'value' => '\'high\'',
           'phpDoc' => NULL,
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Medium',
           'value' => '\'medium\'',
           'phpDoc' => NULL,
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedEnumCaseNode::__set_state(array(
           'name' => 'Low',
           'value' => '\'low\'',
           'phpDoc' => NULL,
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'weight',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Higher weight = more severe. Useful for ordering alerts.
     */',
             'namespace' => 'App\\Enums',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'label',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'badgeClasses',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Tailwind badge classes used across the UI.
     */',
             'namespace' => 'App\\Enums',
             'uses' => 
            array (
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\Api\\CnpjLookupController',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Serves this app\'s known cadastral data for a single CNPJ to authenticated
 * integrations (Bearer token via Sanctum).
 *
 * Scoped to the token owner\'s organization: only CNPJs that the organization
 * already monitors — and that have at least one collected snapshot — are
 * returned. Anything else is a 404, which the consumer treats as "pendente"
 * (não trava), per the agreed contract.
 */',
         'namespace' => 'App\\Http\\Controllers\\Api',
         'uses' => 
        array (
          'controller' => 'App\\Http\\Controllers\\Controller',
          'changeevent' => 'App\\Models\\ChangeEvent',
          'monitoredcompany' => 'App\\Models\\MonitoredCompany',
          'user' => 'App\\Models\\User',
          'cnpj' => 'App\\Support\\Cnpj',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'jsonresponse' => 'Illuminate\\Http\\JsonResponse',
          'request' => 'Illuminate\\Http\\Request',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__invoke',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\JsonResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Http\\Request',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'cnpj',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Auth\\VerifyEmailController.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\Auth\\VerifyEmailController',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'App\\Http\\Controllers\\Controller',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__invoke',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Mark the authenticated user\'s email address as verified.
     */',
             'namespace' => 'App\\Http\\Controllers\\Auth',
             'uses' => 
            array (
              'controller' => 'App\\Http\\Controllers\\Controller',
              'emailverificationrequest' => 'Illuminate\\Foundation\\Auth\\EmailVerificationRequest',
              'redirectresponse' => 'Illuminate\\Http\\RedirectResponse',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Http\\RedirectResponse',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'request',
               'type' => 'Illuminate\\Foundation\\Auth\\EmailVerificationRequest',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Controller.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Http\\Controllers\\Controller',
       'phpDoc' => NULL,
       'abstract' => true,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Jobs\\ImportCompaniesCsvJob',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Imports a (potentially large) CSV of companies into a portfolio off the
 * request cycle, then notifies the requesting user with the resulting report
 * and removes the temporary upload.
 */',
         'namespace' => 'App\\Jobs',
         'uses' => 
        array (
          'portfolio' => 'App\\Models\\Portfolio',
          'user' => 'App\\Models\\User',
          'companiesimported' => 'App\\Notifications\\CompaniesImported',
          'companyimporter' => 'App\\Services\\CompanyImporter',
          'shouldqueue' => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
          'queueable' => 'Illuminate\\Foundation\\Queue\\Queueable',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Queue\\Queueable',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'portfolio',
               'type' => 'App\\Models\\Portfolio',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'path',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'notifyUserId',
               'type' => '?int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'importer',
               'type' => 'App\\Services\\CompanyImporter',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Refresh a single monitored company: fetch its current cadastral data, store a
 * new snapshot, diff it against the previous one, persist any change events and
 * notify the organization.
 */',
         'namespace' => 'App\\Jobs',
         'uses' => 
        array (
          'cnpjdataprovider' => 'App\\Contracts\\CnpjDataProvider',
          'changeeventdata' => 'App\\DTO\\ChangeEventData',
          'refreshstatus' => 'App\\Enums\\RefreshStatus',
          'changeevent' => 'App\\Models\\ChangeEvent',
          'companysnapshot' => 'App\\Models\\CompanySnapshot',
          'monitoredcompany' => 'App\\Models\\MonitoredCompany',
          'companychangedetected' => 'App\\Notifications\\CompanyChangeDetected',
          'companydiffer' => 'App\\Services\\CompanyDiffer',
          'snapshotservice' => 'App\\Services\\SnapshotService',
          'datetimeinterface' => 'DateTimeInterface',
          'batchable' => 'Illuminate\\Bus\\Batchable',
          'shouldbeunique' => 'Illuminate\\Contracts\\Queue\\ShouldBeUnique',
          'shouldqueue' => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'queueable' => 'Illuminate\\Foundation\\Queue\\Queueable',
          'ratelimited' => 'Illuminate\\Queue\\Middleware\\RateLimited',
          'withoutoverlapping' => 'Illuminate\\Queue\\Middleware\\WithoutOverlapping',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'db' => 'Illuminate\\Support\\Facades\\DB',
          'notification' => 'Illuminate\\Support\\Facades\\Notification',
          'throwable' => 'Throwable',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'Illuminate\\Contracts\\Queue\\ShouldBeUnique',
        1 => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Bus\\Batchable',
        1 => 'Illuminate\\Foundation\\Queue\\Queueable',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'maxExceptions',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Real errors (SSL, repeated 5xx) fail the job after this many exceptions.
     * We deliberately do NOT cap `$tries`: the RateLimited middleware releases
     * over-limit jobs back to the queue, and each release counts as an attempt —
     * so a low `$tries` would fail jobs merely for being rate limited. Instead we
     * bound retries by time via retryUntil().
     */',
             'namespace' => 'App\\Jobs',
             'uses' => 
            array (
              'cnpjdataprovider' => 'App\\Contracts\\CnpjDataProvider',
              'changeeventdata' => 'App\\DTO\\ChangeEventData',
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'changeevent' => 'App\\Models\\ChangeEvent',
              'companysnapshot' => 'App\\Models\\CompanySnapshot',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'companychangedetected' => 'App\\Notifications\\CompanyChangeDetected',
              'companydiffer' => 'App\\Services\\CompanyDiffer',
              'snapshotservice' => 'App\\Services\\SnapshotService',
              'datetimeinterface' => 'DateTimeInterface',
              'batchable' => 'Illuminate\\Bus\\Batchable',
              'shouldbeunique' => 'Illuminate\\Contracts\\Queue\\ShouldBeUnique',
              'shouldqueue' => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
              'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'queueable' => 'Illuminate\\Foundation\\Queue\\Queueable',
              'ratelimited' => 'Illuminate\\Queue\\Middleware\\RateLimited',
              'withoutoverlapping' => 'Illuminate\\Queue\\Middleware\\WithoutOverlapping',
              'carbon' => 'Illuminate\\Support\\Carbon',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'notification' => 'Illuminate\\Support\\Facades\\Notification',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => 'int',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'uniqueFor',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Máximo que o lock de unicidade fica retido (rede de segurança caso o job
     * morra sem liberar). Na prática o lock é solto assim que o job conclui.
     */',
             'namespace' => 'App\\Jobs',
             'uses' => 
            array (
              'cnpjdataprovider' => 'App\\Contracts\\CnpjDataProvider',
              'changeeventdata' => 'App\\DTO\\ChangeEventData',
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'changeevent' => 'App\\Models\\ChangeEvent',
              'companysnapshot' => 'App\\Models\\CompanySnapshot',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'companychangedetected' => 'App\\Notifications\\CompanyChangeDetected',
              'companydiffer' => 'App\\Services\\CompanyDiffer',
              'snapshotservice' => 'App\\Services\\SnapshotService',
              'datetimeinterface' => 'DateTimeInterface',
              'batchable' => 'Illuminate\\Bus\\Batchable',
              'shouldbeunique' => 'Illuminate\\Contracts\\Queue\\ShouldBeUnique',
              'shouldqueue' => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
              'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'queueable' => 'Illuminate\\Foundation\\Queue\\Queueable',
              'ratelimited' => 'Illuminate\\Queue\\Middleware\\RateLimited',
              'withoutoverlapping' => 'Illuminate\\Queue\\Middleware\\WithoutOverlapping',
              'carbon' => 'Illuminate\\Support\\Carbon',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'notification' => 'Illuminate\\Support\\Facades\\Notification',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => 'int',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'company',
               'type' => 'App\\Models\\MonitoredCompany',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'uniqueId',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Deduplica o enfileiramento: enquanto já houver um refresh pendente ou em
     * execução para esta empresa, novos dispatches (ex.: cliques repetidos em
     * "Atualizar todas") são descartados em vez de empilhar jobs iguais.
     */',
             'namespace' => 'App\\Jobs',
             'uses' => 
            array (
              'cnpjdataprovider' => 'App\\Contracts\\CnpjDataProvider',
              'changeeventdata' => 'App\\DTO\\ChangeEventData',
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'changeevent' => 'App\\Models\\ChangeEvent',
              'companysnapshot' => 'App\\Models\\CompanySnapshot',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'companychangedetected' => 'App\\Notifications\\CompanyChangeDetected',
              'companydiffer' => 'App\\Services\\CompanyDiffer',
              'snapshotservice' => 'App\\Services\\SnapshotService',
              'datetimeinterface' => 'DateTimeInterface',
              'batchable' => 'Illuminate\\Bus\\Batchable',
              'shouldbeunique' => 'Illuminate\\Contracts\\Queue\\ShouldBeUnique',
              'shouldqueue' => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
              'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'queueable' => 'Illuminate\\Foundation\\Queue\\Queueable',
              'ratelimited' => 'Illuminate\\Queue\\Middleware\\RateLimited',
              'withoutoverlapping' => 'Illuminate\\Queue\\Middleware\\WithoutOverlapping',
              'carbon' => 'Illuminate\\Support\\Carbon',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'notification' => 'Illuminate\\Support\\Facades\\Notification',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'retryUntil',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Keep retrying (including rate-limit releases) for up to 12 hours, which is
     * plenty for a large batch to drain at the throttled rate.
     */',
             'namespace' => 'App\\Jobs',
             'uses' => 
            array (
              'cnpjdataprovider' => 'App\\Contracts\\CnpjDataProvider',
              'changeeventdata' => 'App\\DTO\\ChangeEventData',
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'changeevent' => 'App\\Models\\ChangeEvent',
              'companysnapshot' => 'App\\Models\\CompanySnapshot',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'companychangedetected' => 'App\\Notifications\\CompanyChangeDetected',
              'companydiffer' => 'App\\Services\\CompanyDiffer',
              'snapshotservice' => 'App\\Services\\SnapshotService',
              'datetimeinterface' => 'DateTimeInterface',
              'batchable' => 'Illuminate\\Bus\\Batchable',
              'shouldbeunique' => 'Illuminate\\Contracts\\Queue\\ShouldBeUnique',
              'shouldqueue' => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
              'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'queueable' => 'Illuminate\\Foundation\\Queue\\Queueable',
              'ratelimited' => 'Illuminate\\Queue\\Middleware\\RateLimited',
              'withoutoverlapping' => 'Illuminate\\Queue\\Middleware\\WithoutOverlapping',
              'carbon' => 'Illuminate\\Support\\Carbon',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'notification' => 'Illuminate\\Support\\Facades\\Notification',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'DateTimeInterface',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'middleware',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Guard the upstream provider: never refresh the same company concurrently,
     * and cap the global refresh rate (see the \'cnpj-refresh\' limiter).
     *
     * @return array<int, object>
     */',
             'namespace' => 'App\\Jobs',
             'uses' => 
            array (
              'cnpjdataprovider' => 'App\\Contracts\\CnpjDataProvider',
              'changeeventdata' => 'App\\DTO\\ChangeEventData',
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'changeevent' => 'App\\Models\\ChangeEvent',
              'companysnapshot' => 'App\\Models\\CompanySnapshot',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'companychangedetected' => 'App\\Notifications\\CompanyChangeDetected',
              'companydiffer' => 'App\\Services\\CompanyDiffer',
              'snapshotservice' => 'App\\Services\\SnapshotService',
              'datetimeinterface' => 'DateTimeInterface',
              'batchable' => 'Illuminate\\Bus\\Batchable',
              'shouldbeunique' => 'Illuminate\\Contracts\\Queue\\ShouldBeUnique',
              'shouldqueue' => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
              'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'queueable' => 'Illuminate\\Foundation\\Queue\\Queueable',
              'ratelimited' => 'Illuminate\\Queue\\Middleware\\RateLimited',
              'withoutoverlapping' => 'Illuminate\\Queue\\Middleware\\WithoutOverlapping',
              'carbon' => 'Illuminate\\Support\\Carbon',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'notification' => 'Illuminate\\Support\\Facades\\Notification',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'handle',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'provider',
               'type' => 'App\\Contracts\\CnpjDataProvider',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'snapshots',
               'type' => 'App\\Services\\SnapshotService',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'differ',
               'type' => 'App\\Services\\CompanyDiffer',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'failed',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Record the outcome once the job ultimately fails (after maxExceptions),
     * so the error is visible per-company in the UI.
     */',
             'namespace' => 'App\\Jobs',
             'uses' => 
            array (
              'cnpjdataprovider' => 'App\\Contracts\\CnpjDataProvider',
              'changeeventdata' => 'App\\DTO\\ChangeEventData',
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'changeevent' => 'App\\Models\\ChangeEvent',
              'companysnapshot' => 'App\\Models\\CompanySnapshot',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'companychangedetected' => 'App\\Notifications\\CompanyChangeDetected',
              'companydiffer' => 'App\\Services\\CompanyDiffer',
              'snapshotservice' => 'App\\Services\\SnapshotService',
              'datetimeinterface' => 'DateTimeInterface',
              'batchable' => 'Illuminate\\Bus\\Batchable',
              'shouldbeunique' => 'Illuminate\\Contracts\\Queue\\ShouldBeUnique',
              'shouldqueue' => 'Illuminate\\Contracts\\Queue\\ShouldQueue',
              'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'queueable' => 'Illuminate\\Foundation\\Queue\\Queueable',
              'ratelimited' => 'Illuminate\\Queue\\Middleware\\RateLimited',
              'withoutoverlapping' => 'Illuminate\\Queue\\Middleware\\WithoutOverlapping',
              'carbon' => 'Illuminate\\Support\\Carbon',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'notification' => 'Illuminate\\Support\\Facades\\Notification',
              'throwable' => 'Throwable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'e',
               'type' => '?Throwable',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Actions\\Logout.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Livewire\\Actions\\Logout',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__invoke',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Log the current user out of the application.
     */',
             'namespace' => 'App\\Livewire\\Actions',
             'uses' => 
            array (
              'auth' => 'Illuminate\\Support\\Facades\\Auth',
              'session' => 'Illuminate\\Support\\Facades\\Session',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Livewire\\Alerts\\Inbox',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Livewire\\Component',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'severity',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/** Severity filter: \'all\' or a Severity value. */',
             'namespace' => 'App\\Livewire\\Alerts',
             'uses' => 
            array (
              'interactswithcurrentorganization' => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
              'changeevent' => 'App\\Models\\ChangeEvent',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'portfolio' => 'App\\Models\\Portfolio',
              'view' => 'Illuminate\\Contracts\\View\\View',
              'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
              'authorizesrequests' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
              'carbon' => 'Illuminate\\Support\\Carbon',
              'layout' => 'Livewire\\Attributes\\Layout',
              'url' => 'Livewire\\Attributes\\Url',
              'component' => 'Livewire\\Component',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
               'name' => 'Livewire\\Attributes\\Url',
               'args' => 
              array (
              ),
            )),
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'setSeverity',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'severity',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'acknowledge',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'id',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'acknowledgeAll',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'render',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Contracts\\View\\View',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
           'name' => 'Livewire\\Attributes\\Layout',
           'args' => 
          array (
            0 => '\'layouts.app\'',
          ),
        )),
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Livewire\\Companies\\Show',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Livewire\\Component',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'company',
          ),
           'phpDoc' => NULL,
           'type' => 'App\\Models\\MonitoredCompany',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'mount',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'company',
               'type' => 'App\\Models\\MonitoredCompany',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'queueRefresh',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'render',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Contracts\\View\\View',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
           'name' => 'Livewire\\Attributes\\Layout',
           'args' => 
          array (
            0 => '\'layouts.app\'',
          ),
        )),
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Concerns\\InteractsWithCurrentOrganization.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedTraitNode::__set_state(array(
       'name' => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
       'phpDoc' => NULL,
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'currentUser',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Models\\User',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'currentOrganizationId',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'int',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Livewire\\Dashboard',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Livewire\\Component',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'render',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Contracts\\View\\View',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
           'name' => 'Livewire\\Attributes\\Layout',
           'args' => 
          array (
            0 => '\'layouts.app\'',
          ),
        )),
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Forms\\LoginForm.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Livewire\\Forms\\LoginForm',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Livewire\\Form',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'email',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
               'name' => 'Livewire\\Attributes\\Validate',
               'args' => 
              array (
                0 => '\'required|string|email\'',
              ),
            )),
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'password',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
               'name' => 'Livewire\\Attributes\\Validate',
               'args' => 
              array (
                0 => '\'required|string\'',
              ),
            )),
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'remember',
          ),
           'phpDoc' => NULL,
           'type' => 'bool',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
               'name' => 'Livewire\\Attributes\\Validate',
               'args' => 
              array (
                0 => '\'boolean\'',
              ),
            )),
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'authenticate',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Attempt to authenticate the request\'s credentials.
     *
     * @throws ValidationException
     */',
             'namespace' => 'App\\Livewire\\Forms',
             'uses' => 
            array (
              'lockout' => 'Illuminate\\Auth\\Events\\Lockout',
              'auth' => 'Illuminate\\Support\\Facades\\Auth',
              'ratelimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
              'str' => 'Illuminate\\Support\\Str',
              'validationexception' => 'Illuminate\\Validation\\ValidationException',
              'validate' => 'Livewire\\Attributes\\Validate',
              'form' => 'Livewire\\Form',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'ensureIsNotRateLimited',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Ensure the authentication request is not rate limited.
     */',
             'namespace' => 'App\\Livewire\\Forms',
             'uses' => 
            array (
              'lockout' => 'Illuminate\\Auth\\Events\\Lockout',
              'auth' => 'Illuminate\\Support\\Facades\\Auth',
              'ratelimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
              'str' => 'Illuminate\\Support\\Str',
              'validationexception' => 'Illuminate\\Validation\\ValidationException',
              'validate' => 'Livewire\\Attributes\\Validate',
              'form' => 'Livewire\\Form',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'throttleKey',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the authentication rate limiting throttle key.
     */',
             'namespace' => 'App\\Livewire\\Forms',
             'uses' => 
            array (
              'lockout' => 'Illuminate\\Auth\\Events\\Lockout',
              'auth' => 'Illuminate\\Support\\Facades\\Auth',
              'ratelimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
              'str' => 'Illuminate\\Support\\Str',
              'validationexception' => 'Illuminate\\Validation\\ValidationException',
              'validate' => 'Livewire\\Attributes\\Validate',
              'form' => 'Livewire\\Form',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Livewire\\Portfolios\\Index',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Livewire\\Component',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'name',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
               'name' => 'Livewire\\Attributes\\Validate',
               'args' => 
              array (
                0 => '\'required|string|min:2|max:255\'',
              ),
            )),
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'editingId',
          ),
           'phpDoc' => NULL,
           'type' => '?int',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'editingName',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
               'name' => 'Livewire\\Attributes\\Validate',
               'args' => 
              array (
                0 => '\'required|string|min:2|max:255\'',
              ),
            )),
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'startEdit',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'id',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'saveEdit',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'cancelEdit',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'id',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'queueRefresh',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'id',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'refresher',
               'type' => 'App\\Actions\\QueuePortfolioRefresh',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        9 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'render',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Contracts\\View\\View',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
           'name' => 'Livewire\\Attributes\\Layout',
           'args' => 
          array (
            0 => '\'layouts.app\'',
          ),
        )),
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Livewire\\Portfolios\\Show',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Livewire\\Component',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
        1 => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
        2 => 'Livewire\\WithFileUploads',
        3 => 'Livewire\\WithPagination',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'portfolio',
          ),
           'phpDoc' => NULL,
           'type' => 'App\\Models\\Portfolio',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'cnpj',
          ),
           'phpDoc' => NULL,
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
               'name' => 'Livewire\\Attributes\\Validate',
               'args' => 
              array (
                0 => '\'required|string\'',
              ),
            )),
          ),
           'hooks' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'label',
          ),
           'phpDoc' => NULL,
           'type' => '?string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
               'name' => 'Livewire\\Attributes\\Validate',
               'args' => 
              array (
                0 => '\'nullable|string|max:255\'',
              ),
            )),
          ),
           'hooks' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'csv',
          ),
           'phpDoc' => NULL,
           'type' => '?Livewire\\Features\\SupportFileUploads\\TemporaryUploadedFile',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
               'name' => 'Livewire\\Attributes\\Validate',
               'args' => 
              array (
                0 => '\'nullable|file|max:5120\'',
              ),
            )),
          ),
           'hooks' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'importReport',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @var array{imported: int, rejected: list<array{line: int, value: string, reason: string}>}|null
     */',
             'namespace' => 'App\\Livewire\\Portfolios',
             'uses' => 
            array (
              'queueportfoliorefresh' => 'App\\Actions\\QueuePortfolioRefresh',
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'refreshmonitoredcompanyjob' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
              'interactswithcurrentorganization' => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'portfolio' => 'App\\Models\\Portfolio',
              'companyimporter' => 'App\\Services\\CompanyImporter',
              'cnpj' => 'App\\Support\\Cnpj',
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'view' => 'Illuminate\\Contracts\\View\\View',
              'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
              'authorizesrequests' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'layout' => 'Livewire\\Attributes\\Layout',
              'url' => 'Livewire\\Attributes\\Url',
              'validate' => 'Livewire\\Attributes\\Validate',
              'component' => 'Livewire\\Component',
              'temporaryuploadedfile' => 'Livewire\\Features\\SupportFileUploads\\TemporaryUploadedFile',
              'withfileuploads' => 'Livewire\\WithFileUploads',
              'withpagination' => 'Livewire\\WithPagination',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => '?array',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'statusFilter',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/** Filtro por status da coleta: all|pending|ok|nao_encontrado|erro. */',
             'namespace' => 'App\\Livewire\\Portfolios',
             'uses' => 
            array (
              'queueportfoliorefresh' => 'App\\Actions\\QueuePortfolioRefresh',
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'refreshmonitoredcompanyjob' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
              'interactswithcurrentorganization' => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'portfolio' => 'App\\Models\\Portfolio',
              'companyimporter' => 'App\\Services\\CompanyImporter',
              'cnpj' => 'App\\Support\\Cnpj',
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'view' => 'Illuminate\\Contracts\\View\\View',
              'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
              'authorizesrequests' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'layout' => 'Livewire\\Attributes\\Layout',
              'url' => 'Livewire\\Attributes\\Url',
              'validate' => 'Livewire\\Attributes\\Validate',
              'component' => 'Livewire\\Component',
              'temporaryuploadedfile' => 'Livewire\\Features\\SupportFileUploads\\TemporaryUploadedFile',
              'withfileuploads' => 'Livewire\\WithFileUploads',
              'withpagination' => 'Livewire\\WithPagination',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => 'string',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
               'name' => 'Livewire\\Attributes\\Url',
               'args' => 
              array (
              ),
            )),
          ),
           'hooks' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'scheduleDays',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Dias do mês (1–31) selecionados para o agendamento mensal do portfólio.
     *
     * @var list<int>
     */',
             'namespace' => 'App\\Livewire\\Portfolios',
             'uses' => 
            array (
              'queueportfoliorefresh' => 'App\\Actions\\QueuePortfolioRefresh',
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'refreshmonitoredcompanyjob' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
              'interactswithcurrentorganization' => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'portfolio' => 'App\\Models\\Portfolio',
              'companyimporter' => 'App\\Services\\CompanyImporter',
              'cnpj' => 'App\\Support\\Cnpj',
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'view' => 'Illuminate\\Contracts\\View\\View',
              'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
              'authorizesrequests' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'layout' => 'Livewire\\Attributes\\Layout',
              'url' => 'Livewire\\Attributes\\Url',
              'validate' => 'Livewire\\Attributes\\Validate',
              'component' => 'Livewire\\Component',
              'temporaryuploadedfile' => 'Livewire\\Features\\SupportFileUploads\\TemporaryUploadedFile',
              'withfileuploads' => 'Livewire\\WithFileUploads',
              'withpagination' => 'Livewire\\WithPagination',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => 'array',
           'public' => true,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        7 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'mount',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'portfolio',
               'type' => 'App\\Models\\Portfolio',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        8 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toggleScheduleDay',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'day',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        9 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'applyPreset',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Aplica um preset comum de agendamento. Não persiste: o usuário confirma
     * com "Salvar".
     */',
             'namespace' => 'App\\Livewire\\Portfolios',
             'uses' => 
            array (
              'queueportfoliorefresh' => 'App\\Actions\\QueuePortfolioRefresh',
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'refreshmonitoredcompanyjob' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
              'interactswithcurrentorganization' => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'portfolio' => 'App\\Models\\Portfolio',
              'companyimporter' => 'App\\Services\\CompanyImporter',
              'cnpj' => 'App\\Support\\Cnpj',
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'view' => 'Illuminate\\Contracts\\View\\View',
              'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
              'authorizesrequests' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'layout' => 'Livewire\\Attributes\\Layout',
              'url' => 'Livewire\\Attributes\\Url',
              'validate' => 'Livewire\\Attributes\\Validate',
              'component' => 'Livewire\\Component',
              'temporaryuploadedfile' => 'Livewire\\Features\\SupportFileUploads\\TemporaryUploadedFile',
              'withfileuploads' => 'Livewire\\WithFileUploads',
              'withpagination' => 'Livewire\\WithPagination',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'preset',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        10 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'clearScheduleDays',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        11 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'discardScheduleChanges',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Descarta alterações não salvas, voltando ao agendamento persistido.
     */',
             'namespace' => 'App\\Livewire\\Portfolios',
             'uses' => 
            array (
              'queueportfoliorefresh' => 'App\\Actions\\QueuePortfolioRefresh',
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'refreshmonitoredcompanyjob' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
              'interactswithcurrentorganization' => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'portfolio' => 'App\\Models\\Portfolio',
              'companyimporter' => 'App\\Services\\CompanyImporter',
              'cnpj' => 'App\\Support\\Cnpj',
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'view' => 'Illuminate\\Contracts\\View\\View',
              'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
              'authorizesrequests' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'layout' => 'Livewire\\Attributes\\Layout',
              'url' => 'Livewire\\Attributes\\Url',
              'validate' => 'Livewire\\Attributes\\Validate',
              'component' => 'Livewire\\Component',
              'temporaryuploadedfile' => 'Livewire\\Features\\SupportFileUploads\\TemporaryUploadedFile',
              'withfileuploads' => 'Livewire\\WithFileUploads',
              'withpagination' => 'Livewire\\WithPagination',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        12 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'saveSchedule',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        13 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'addCompany',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        14 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'import',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'importer',
               'type' => 'App\\Services\\CompanyImporter',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        15 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'queueRefresh',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'companyId',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        16 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'queueRefreshAll',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'refresher',
               'type' => 'App\\Actions\\QueuePortfolioRefresh',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        17 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'setStatusFilter',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'status',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        18 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'reprocessFiltered',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Reprocessa (revalida) apenas o subconjunto do filtro ativo — ex.: só os
     * CNPJs com erro, ou só os não encontrados.
     */',
             'namespace' => 'App\\Livewire\\Portfolios',
             'uses' => 
            array (
              'queueportfoliorefresh' => 'App\\Actions\\QueuePortfolioRefresh',
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'refreshmonitoredcompanyjob' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
              'interactswithcurrentorganization' => 'App\\Livewire\\Concerns\\InteractsWithCurrentOrganization',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'portfolio' => 'App\\Models\\Portfolio',
              'companyimporter' => 'App\\Services\\CompanyImporter',
              'cnpj' => 'App\\Support\\Cnpj',
              'carbonimmutable' => 'Carbon\\CarbonImmutable',
              'view' => 'Illuminate\\Contracts\\View\\View',
              'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
              'authorizesrequests' => 'Illuminate\\Foundation\\Auth\\Access\\AuthorizesRequests',
              'db' => 'Illuminate\\Support\\Facades\\DB',
              'layout' => 'Livewire\\Attributes\\Layout',
              'url' => 'Livewire\\Attributes\\Url',
              'validate' => 'Livewire\\Attributes\\Validate',
              'component' => 'Livewire\\Component',
              'temporaryuploadedfile' => 'Livewire\\Features\\SupportFileUploads\\TemporaryUploadedFile',
              'withfileuploads' => 'Livewire\\WithFileUploads',
              'withpagination' => 'Livewire\\WithPagination',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'refresher',
               'type' => 'App\\Actions\\QueuePortfolioRefresh',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        19 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'removeCompany',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'companyId',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        20 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'render',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Contracts\\View\\View',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
           'name' => 'Livewire\\Attributes\\Layout',
           'args' => 
          array (
            0 => '\'layouts.app\'',
          ),
        )),
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\ChangeEvent.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\ChangeEvent',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
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
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'changetype' => 'App\\Enums\\ChangeType',
          'severity' => 'App\\Enums\\Severity',
          'changeeventfactory' => 'Database\\Factories\\ChangeEventFactory',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
          'carbon' => 'Illuminate\\Support\\Carbon',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/** @var list<string> */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'changetype' => 'App\\Enums\\ChangeType',
              'severity' => 'App\\Enums\\Severity',
              'changeeventfactory' => 'Database\\Factories\\ChangeEventFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, string>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'changetype' => 'App\\Enums\\ChangeType',
              'severity' => 'App\\Enums\\Severity',
              'changeeventfactory' => 'Database\\Factories\\ChangeEventFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'monitoredCompany',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return BelongsTo<MonitoredCompany, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'changetype' => 'App\\Enums\\ChangeType',
              'severity' => 'App\\Enums\\Severity',
              'changeeventfactory' => 'Database\\Factories\\ChangeEventFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fromSnapshot',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return BelongsTo<CompanySnapshot, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'changetype' => 'App\\Enums\\ChangeType',
              'severity' => 'App\\Enums\\Severity',
              'changeeventfactory' => 'Database\\Factories\\ChangeEventFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toSnapshot',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return BelongsTo<CompanySnapshot, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'changetype' => 'App\\Enums\\ChangeType',
              'severity' => 'App\\Enums\\Severity',
              'changeeventfactory' => 'Database\\Factories\\ChangeEventFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isAcknowledged',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\CompanyPartner.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\CompanyPartner',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @property int $id
 * @property int $company_snapshot_id
 * @property string|null $documento
 * @property string $nome
 * @property string|null $qualificacao
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CompanySnapshot $companySnapshot
 */',
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'companypartnerfactory' => 'Database\\Factories\\CompanyPartnerFactory',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
          'carbon' => 'Illuminate\\Support\\Carbon',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/** @var list<string> */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'companypartnerfactory' => 'Database\\Factories\\CompanyPartnerFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'companySnapshot',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return BelongsTo<CompanySnapshot, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'companypartnerfactory' => 'Database\\Factories\\CompanyPartnerFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\CompanySnapshot.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\CompanySnapshot',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @property int $id
 * @property int $monitored_company_id
 * @property Carbon $captured_at
 * @property string $razao_social
 * @property string|null $nome_fantasia
 * @property string $situacao_cadastral
 * @property Carbon|null $situacao_data
 * @property string|null $cnae_principal
 * @property string|null $porte
 * @property string|null $natureza_juridica
 * @property string|null $logradouro
 * @property string|null $municipio
 * @property string|null $uf
 * @property array<string, mixed> $raw_json
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read MonitoredCompany $monitoredCompany
 * @property-read Collection<int, CompanyPartner> $partners
 */',
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'companysnapshotfactory' => 'Database\\Factories\\CompanySnapshotFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'carbon' => 'Illuminate\\Support\\Carbon',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/** @var list<string> */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'companysnapshotfactory' => 'Database\\Factories\\CompanySnapshotFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, string>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'companysnapshotfactory' => 'Database\\Factories\\CompanySnapshotFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'monitoredCompany',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return BelongsTo<MonitoredCompany, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'companysnapshotfactory' => 'Database\\Factories\\CompanySnapshotFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'partners',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return HasMany<CompanyPartner, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'companysnapshotfactory' => 'Database\\Factories\\CompanySnapshotFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Concerns\\BelongsToOrganization.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedTraitNode::__set_state(array(
       'name' => 'App\\Models\\Concerns\\BelongsToOrganization',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Makes a model tenant-aware:
 *  - a global scope constrains every query to the current user\'s organization;
 *  - new records inherit the current user\'s organization_id automatically.
 *
 * The model must have an `organization_id` column.
 */',
         'namespace' => 'App\\Models\\Concerns',
         'uses' => 
        array (
          'organizationscope' => 'App\\Models\\Scopes\\OrganizationScope',
          'user' => 'App\\Models\\User',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
        ),
         'constUses' => 
        array (
        ),
      )),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'bootBelongsToOrganization',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\MonitoredCompany',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @property int $id
 * @property int $portfolio_id
 * @property string $cnpj
 * @property string|null $label
 * @property Carbon|null $last_refreshed_at
 * @property RefreshStatus|null $last_refresh_status
 * @property string|null $last_refresh_error
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Portfolio $portfolio
 * @property-read Collection<int, CompanySnapshot> $snapshots
 * @property-read CompanySnapshot|null $latestSnapshot
 * @property-read Collection<int, ChangeEvent> $changeEvents
 */',
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'refreshstatus' => 'App\\Enums\\RefreshStatus',
          'cnpj' => 'App\\Support\\Cnpj',
          'monitoredcompanyfactory' => 'Database\\Factories\\MonitoredCompanyFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
          'carbon' => 'Illuminate\\Support\\Carbon',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/** @var list<string> */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'cnpj' => 'App\\Support\\Cnpj',
              'monitoredcompanyfactory' => 'Database\\Factories\\MonitoredCompanyFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, string>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'cnpj' => 'App\\Support\\Cnpj',
              'monitoredcompanyfactory' => 'Database\\Factories\\MonitoredCompanyFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'formattedCnpj',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'portfolio',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return BelongsTo<Portfolio, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'cnpj' => 'App\\Support\\Cnpj',
              'monitoredcompanyfactory' => 'Database\\Factories\\MonitoredCompanyFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'snapshots',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return HasMany<CompanySnapshot, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'cnpj' => 'App\\Support\\Cnpj',
              'monitoredcompanyfactory' => 'Database\\Factories\\MonitoredCompanyFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'latestSnapshot',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Most recent snapshot captured for this company.
     *
     * @return HasOne<CompanySnapshot, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'cnpj' => 'App\\Support\\Cnpj',
              'monitoredcompanyfactory' => 'Database\\Factories\\MonitoredCompanyFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'changeEvents',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return HasMany<ChangeEvent, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'refreshstatus' => 'App\\Enums\\RefreshStatus',
              'cnpj' => 'App\\Support\\Cnpj',
              'monitoredcompanyfactory' => 'Database\\Factories\\MonitoredCompanyFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'hasone' => 'Illuminate\\Database\\Eloquent\\Relations\\HasOne',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Organization.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\Organization',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, User> $users
 * @property-read Collection<int, Portfolio> $portfolios
 */',
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'organizationfactory' => 'Database\\Factories\\OrganizationFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'carbon' => 'Illuminate\\Support\\Carbon',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/** @var list<string> */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'organizationfactory' => 'Database\\Factories\\OrganizationFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'users',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return HasMany<User, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'organizationfactory' => 'Database\\Factories\\OrganizationFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'portfolios',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return HasMany<Portfolio, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'organizationfactory' => 'Database\\Factories\\OrganizationFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\Portfolio',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property list<int>|null $schedule_days
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Organization $organization
 * @property-read Collection<int, MonitoredCompany> $monitoredCompanies
 * @property-read int|null $monitored_companies_count
 * @property-read Collection<int, PortfolioScheduledRun> $scheduledRuns
 */',
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'belongstoorganization' => 'App\\Models\\Concerns\\BelongsToOrganization',
          'portfoliofactory' => 'Database\\Factories\\PortfolioFactory',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
          'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
          'carbon' => 'Illuminate\\Support\\Carbon',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'App\\Models\\Concerns\\BelongsToOrganization',
        1 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/** @var list<string> */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'belongstoorganization' => 'App\\Models\\Concerns\\BelongsToOrganization',
              'portfoliofactory' => 'Database\\Factories\\PortfolioFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, string>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'belongstoorganization' => 'App\\Models\\Concerns\\BelongsToOrganization',
              'portfoliofactory' => 'Database\\Factories\\PortfolioFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'organization',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return BelongsTo<Organization, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'belongstoorganization' => 'App\\Models\\Concerns\\BelongsToOrganization',
              'portfoliofactory' => 'Database\\Factories\\PortfolioFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'monitoredCompanies',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return HasMany<MonitoredCompany, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'belongstoorganization' => 'App\\Models\\Concerns\\BelongsToOrganization',
              'portfoliofactory' => 'Database\\Factories\\PortfolioFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'scheduledRuns',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return HasMany<PortfolioScheduledRun, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'belongstoorganization' => 'App\\Models\\Concerns\\BelongsToOrganization',
              'portfoliofactory' => 'Database\\Factories\\PortfolioFactory',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'hasmany' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\PortfolioScheduledRun.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\PortfolioScheduledRun',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
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
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'portfolioscheduledrunfactory' => 'Database\\Factories\\PortfolioScheduledRunFactory',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
          'carbon' => 'Illuminate\\Support\\Carbon',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Model',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'fillable',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/** @var list<string> */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'portfolioscheduledrunfactory' => 'Database\\Factories\\PortfolioScheduledRunFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => NULL,
           'public' => false,
           'private' => false,
           'static' => false,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, string>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'portfolioscheduledrunfactory' => 'Database\\Factories\\PortfolioScheduledRunFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'portfolio',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return BelongsTo<Portfolio, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'portfolioscheduledrunfactory' => 'Database\\Factories\\PortfolioScheduledRunFactory',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'carbon' => 'Illuminate\\Support\\Carbon',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\Scopes\\OrganizationScope.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\Scopes\\OrganizationScope',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Restricts queries to the authenticated user\'s organization.
 *
 * When there is no authenticated user (console commands, queued jobs) the scope
 * is a no-op, so system processes can operate across every tenant.
 *
 * @implements Scope<Model>
 */',
         'namespace' => 'App\\Models\\Scopes',
         'uses' => 
        array (
          'user' => 'App\\Models\\User',
          'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
          'model' => 'Illuminate\\Database\\Eloquent\\Model',
          'scope' => 'Illuminate\\Database\\Eloquent\\Scope',
          'auth' => 'Illuminate\\Support\\Facades\\Auth',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'Illuminate\\Database\\Eloquent\\Scope',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'apply',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  Builder<*>  $builder
     */',
             'namespace' => 'App\\Models\\Scopes',
             'uses' => 
            array (
              'user' => 'App\\Models\\User',
              'builder' => 'Illuminate\\Database\\Eloquent\\Builder',
              'model' => 'Illuminate\\Database\\Eloquent\\Model',
              'scope' => 'Illuminate\\Database\\Eloquent\\Scope',
              'auth' => 'Illuminate\\Support\\Facades\\Auth',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'builder',
               'type' => 'Illuminate\\Database\\Eloquent\\Builder',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'model',
               'type' => 'Illuminate\\Database\\Eloquent\\Model',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Models\\User.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Models\\User',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @property int $id
 * @property int|null $organization_id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Organization|null $organization
 */',
         'namespace' => 'App\\Models',
         'uses' => 
        array (
          'userfactory' => 'Database\\Factories\\UserFactory',
          'fillable' => 'Illuminate\\Database\\Eloquent\\Attributes\\Fillable',
          'hidden' => 'Illuminate\\Database\\Eloquent\\Attributes\\Hidden',
          'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
          'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
          'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
          'notifiable' => 'Illuminate\\Notifications\\Notifiable',
          'carbon' => 'Illuminate\\Support\\Carbon',
          'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Foundation\\Auth\\User',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Laravel\\Sanctum\\HasApiTokens',
        1 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
        2 => 'Illuminate\\Notifications\\Notifiable',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'organization',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return BelongsTo<Organization, $this>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'userfactory' => 'Database\\Factories\\UserFactory',
              'fillable' => 'Illuminate\\Database\\Eloquent\\Attributes\\Fillable',
              'hidden' => 'Illuminate\\Database\\Eloquent\\Attributes\\Hidden',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
              'carbon' => 'Illuminate\\Support\\Carbon',
              'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'casts',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */',
             'namespace' => 'App\\Models',
             'uses' => 
            array (
              'userfactory' => 'Database\\Factories\\UserFactory',
              'fillable' => 'Illuminate\\Database\\Eloquent\\Attributes\\Fillable',
              'hidden' => 'Illuminate\\Database\\Eloquent\\Attributes\\Hidden',
              'hasfactory' => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
              'belongsto' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
              'authenticatable' => 'Illuminate\\Foundation\\Auth\\User',
              'notifiable' => 'Illuminate\\Notifications\\Notifiable',
              'carbon' => 'Illuminate\\Support\\Carbon',
              'hasapitokens' => 'Laravel\\Sanctum\\HasApiTokens',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => false,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
           'name' => 'Illuminate\\Database\\Eloquent\\Attributes\\Fillable',
           'args' => 
          array (
            0 => '[\'name\', \'email\', \'password\', \'organization_id\']',
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedAttributeNode::__set_state(array(
           'name' => 'Illuminate\\Database\\Eloquent\\Attributes\\Hidden',
           'args' => 
          array (
            0 => '[\'password\', \'remember_token\']',
          ),
        )),
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompaniesImported.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Notifications\\CompaniesImported',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * In-app report delivered after an asynchronous CSV import finishes.
 */',
         'namespace' => 'App\\Notifications',
         'uses' => 
        array (
          'importreport' => 'App\\DTO\\ImportReport',
          'portfolio' => 'App\\Models\\Portfolio',
          'notification' => 'Illuminate\\Notifications\\Notification',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Notifications\\Notification',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'portfolio',
               'type' => 'App\\Models\\Portfolio',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'report',
               'type' => 'App\\DTO\\ImportReport',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'via',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<int, string>
     */',
             'namespace' => 'App\\Notifications',
             'uses' => 
            array (
              'importreport' => 'App\\DTO\\ImportReport',
              'portfolio' => 'App\\Models\\Portfolio',
              'notification' => 'Illuminate\\Notifications\\Notification',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'notifiable',
               'type' => 'object',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toArray',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'App\\Notifications',
             'uses' => 
            array (
              'importreport' => 'App\\DTO\\ImportReport',
              'portfolio' => 'App\\Models\\Portfolio',
              'notification' => 'Illuminate\\Notifications\\Notification',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'notifiable',
               'type' => 'object',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompanyChangeDetected.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Notifications\\CompanyChangeDetected',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Alerts an organization that one or more changes were detected on a monitored
 * company. Delivered in-app (database) and by e-mail.
 */',
         'namespace' => 'App\\Notifications',
         'uses' => 
        array (
          'severity' => 'App\\Enums\\Severity',
          'changeevent' => 'App\\Models\\ChangeEvent',
          'monitoredcompany' => 'App\\Models\\MonitoredCompany',
          'cnpj' => 'App\\Support\\Cnpj',
          'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'mailmessage' => 'Illuminate\\Notifications\\Messages\\MailMessage',
          'notification' => 'Illuminate\\Notifications\\Notification',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Notifications\\Notification',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  Collection<int, ChangeEvent>  $events
     */',
             'namespace' => 'App\\Notifications',
             'uses' => 
            array (
              'severity' => 'App\\Enums\\Severity',
              'changeevent' => 'App\\Models\\ChangeEvent',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'cnpj' => 'App\\Support\\Cnpj',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'mailmessage' => 'Illuminate\\Notifications\\Messages\\MailMessage',
              'notification' => 'Illuminate\\Notifications\\Notification',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'company',
               'type' => 'App\\Models\\MonitoredCompany',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'razaoSocial',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'events',
               'type' => 'Illuminate\\Database\\Eloquent\\Collection',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 1,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'via',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<int, string>
     */',
             'namespace' => 'App\\Notifications',
             'uses' => 
            array (
              'severity' => 'App\\Enums\\Severity',
              'changeevent' => 'App\\Models\\ChangeEvent',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'cnpj' => 'App\\Support\\Cnpj',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'mailmessage' => 'Illuminate\\Notifications\\Messages\\MailMessage',
              'notification' => 'Illuminate\\Notifications\\Notification',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'notifiable',
               'type' => 'object',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toMail',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\Notifications\\Messages\\MailMessage',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'notifiable',
               'type' => 'object',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'toArray',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'App\\Notifications',
             'uses' => 
            array (
              'severity' => 'App\\Enums\\Severity',
              'changeevent' => 'App\\Models\\ChangeEvent',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'cnpj' => 'App\\Support\\Cnpj',
              'collection' => 'Illuminate\\Database\\Eloquent\\Collection',
              'mailmessage' => 'Illuminate\\Notifications\\Messages\\MailMessage',
              'notification' => 'Illuminate\\Notifications\\Notification',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'notifiable',
               'type' => 'object',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Policies\\MonitoredCompanyPolicy',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'view',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'company',
               'type' => 'App\\Models\\MonitoredCompany',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'company',
               'type' => 'App\\Models\\MonitoredCompany',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'company',
               'type' => 'App\\Models\\MonitoredCompany',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Policies\\PortfolioPolicy.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Policies\\PortfolioPolicy',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'viewAny',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'view',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'portfolio',
               'type' => 'App\\Models\\Portfolio',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'create',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'update',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'portfolio',
               'type' => 'App\\Models\\Portfolio',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'delete',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'user',
               'type' => 'App\\Models\\User',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'portfolio',
               'type' => 'App\\Models\\Portfolio',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\AppServiceProvider.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Providers\\AppServiceProvider',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Support\\ServiceProvider',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'register',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Register any application services.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'cnpjdataprovider' => 'App\\Contracts\\CnpjDataProvider',
              'brasilapiprovider' => 'App\\Providers\\Cnpj\\BrasilApiProvider',
              'limit' => 'Illuminate\\Cache\\RateLimiting\\Limit',
              'request' => 'Illuminate\\Http\\Request',
              'ratelimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
              'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
              'invalidargumentexception' => 'InvalidArgumentException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'boot',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Bootstrap any application services.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'cnpjdataprovider' => 'App\\Contracts\\CnpjDataProvider',
              'brasilapiprovider' => 'App\\Providers\\Cnpj\\BrasilApiProvider',
              'limit' => 'Illuminate\\Cache\\RateLimiting\\Limit',
              'request' => 'Illuminate\\Http\\Request',
              'ratelimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
              'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
              'invalidargumentexception' => 'InvalidArgumentException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\BrasilApiProvider.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Providers\\Cnpj\\BrasilApiProvider',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * CNPJ data provider backed by BrasilAPI (https://brasilapi.com.br).
 *
 * Endpoint: GET /api/cnpj/v1/{cnpj}
 */',
         'namespace' => 'App\\Providers\\Cnpj',
         'uses' => 
        array (
          'cnpjdataprovider' => 'App\\Contracts\\CnpjDataProvider',
          'companydata' => 'App\\DTO\\CompanyData',
          'partnerdata' => 'App\\DTO\\PartnerData',
          'cnpjproviderexception' => 'App\\Providers\\Cnpj\\Exceptions\\CnpjProviderException',
          'cnpj' => 'App\\Support\\Cnpj',
          'connectionexception' => 'Illuminate\\Http\\Client\\ConnectionException',
          'requestexception' => 'Illuminate\\Http\\Client\\RequestException',
          'http' => 'Illuminate\\Support\\Facades\\Http',
          'ratelimiter' => 'Illuminate\\Support\\Facades\\RateLimiter',
          'sleep' => 'Illuminate\\Support\\Sleep',
          'throwable' => 'Throwable',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'App\\Contracts\\CnpjDataProvider',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__construct',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => NULL,
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'baseUrl',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'timeout',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'tries',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            3 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'retryBackoffMs',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
            4 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'throttlePerMinute',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 68,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fetch',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => '?App\\DTO\\CompanyData',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'cnpj',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\Exceptions\\CnpjProviderException.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Providers\\Cnpj\\Exceptions\\CnpjProviderException',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => 'RuntimeException',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'requestFailed',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'cnpj',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'status',
               'type' => 'int',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'unreachable',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'cnpj',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'reason',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Providers\\VoltServiceProvider.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Providers\\VoltServiceProvider',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Support\\ServiceProvider',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'register',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Register services.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
              'volt' => 'Livewire\\Volt\\Volt',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'boot',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Bootstrap services.
     */',
             'namespace' => 'App\\Providers',
             'uses' => 
            array (
              'serviceprovider' => 'Illuminate\\Support\\ServiceProvider',
              'volt' => 'Livewire\\Volt\\Volt',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyDiffer.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\CompanyDiffer',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * The heart of Vigilo: a pure function that compares two snapshots of a
 * company\'s cadastral data and reports what changed.
 *
 * It performs no I/O and has no side effects — given the same two snapshots it
 * always returns the same list of {@see ChangeEventData}. Persisting those
 * events (timestamps, foreign keys, notifications) is the caller\'s job.
 */',
         'namespace' => 'App\\Services',
         'uses' => 
        array (
          'changeeventdata' => 'App\\DTO\\ChangeEventData',
          'changetype' => 'App\\Enums\\ChangeType',
          'severity' => 'App\\Enums\\Severity',
          'companypartner' => 'App\\Models\\CompanyPartner',
          'companysnapshot' => 'App\\Models\\CompanySnapshot',
          'collection' => 'Illuminate\\Support\\Collection',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'diff',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return list<ChangeEventData>
     */',
             'namespace' => 'App\\Services',
             'uses' => 
            array (
              'changeeventdata' => 'App\\DTO\\ChangeEventData',
              'changetype' => 'App\\Enums\\ChangeType',
              'severity' => 'App\\Enums\\Severity',
              'companypartner' => 'App\\Models\\CompanyPartner',
              'companysnapshot' => 'App\\Models\\CompanySnapshot',
              'collection' => 'Illuminate\\Support\\Collection',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'previous',
               'type' => '?App\\Models\\CompanySnapshot',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'current',
               'type' => 'App\\Models\\CompanySnapshot',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\CompanyImporter',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Imports monitored companies into a portfolio from CSV rows.
 *
 * Each row is normalized and validated (mod-11), de-duplicated against both the
 * file itself and the companies already in the portfolio, and either created or
 * rejected with a reason. Nothing here does authorization — the caller must have
 * verified the user owns the portfolio.
 */',
         'namespace' => 'App\\Services',
         'uses' => 
        array (
          'importreport' => 'App\\DTO\\ImportReport',
          'portfolio' => 'App\\Models\\Portfolio',
          'cnpj' => 'App\\Support\\Cnpj',
          'generator' => 'Generator',
          'uniqueconstraintviolationexception' => 'Illuminate\\Database\\UniqueConstraintViolationException',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'REASON_EMPTY',
               'value' => '\'cnpj_vazio\'',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'REASON_INVALID',
               'value' => '\'cnpj_invalido\'',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'REASON_DUPLICATE',
               'value' => '\'duplicado\'',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'import',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @param  iterable<int, array{cnpj?: string|null, label?: string|null, line?: int}>  $rows
     */',
             'namespace' => 'App\\Services',
             'uses' => 
            array (
              'importreport' => 'App\\DTO\\ImportReport',
              'portfolio' => 'App\\Models\\Portfolio',
              'cnpj' => 'App\\Support\\Cnpj',
              'generator' => 'Generator',
              'uniqueconstraintviolationexception' => 'Illuminate\\Database\\UniqueConstraintViolationException',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\DTO\\ImportReport',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'portfolio',
               'type' => 'App\\Models\\Portfolio',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'rows',
               'type' => 'iterable',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'importFromFile',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\DTO\\ImportReport',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'portfolio',
               'type' => 'App\\Models\\Portfolio',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'path',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Services\\SnapshotService.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Services\\SnapshotService',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'store',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Persist a new snapshot (and its partners) for a monitored company,
     * atomically. The whole write is wrapped in a transaction so a snapshot
     * never exists without its partners.
     */',
             'namespace' => 'App\\Services',
             'uses' => 
            array (
              'companydata' => 'App\\DTO\\CompanyData',
              'companysnapshot' => 'App\\Models\\CompanySnapshot',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'carbon' => 'Illuminate\\Support\\Carbon',
              'db' => 'Illuminate\\Support\\Facades\\DB',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'App\\Models\\CompanySnapshot',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'company',
               'type' => 'App\\Models\\MonitoredCompany',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            1 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'data',
               'type' => 'App\\DTO\\CompanyData',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
            2 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'capturedAt',
               'type' => '?Illuminate\\Support\\Carbon',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => true,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Support\\Cnpj.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Support\\Cnpj',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * Immutable CNPJ value object.
 *
 * Holds a normalized (14-digit) CNPJ whose check digits have been validated
 * with the mod-11 algorithm. Constructing an instance for an invalid CNPJ is
 * impossible — use {@see self::fromString()} (throws) or {@see self::tryFrom()}
 * (returns null).
 */',
         'namespace' => 'App\\Support',
         'uses' => 
        array (
          'invalidcnpjexception' => 'App\\Support\\Exceptions\\InvalidCnpjException',
          'stringable' => 'Stringable',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => true,
       'extends' => NULL,
       'implements' => 
      array (
        0 => 'Stringable',
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedClassConstantsNode::__set_state(array(
           'constants' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedClassConstantNode::__set_state(array(
               'name' => 'LENGTH',
               'value' => '14',
               'attributes' => 
              array (
              ),
            )),
          ),
           'public' => true,
           'private' => false,
           'final' => false,
           'phpDoc' => NULL,
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'fromString',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Build a validated CNPJ, throwing when the input is not a valid CNPJ.
     *
     * @throws InvalidCnpjException
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'invalidcnpjexception' => 'App\\Support\\Exceptions\\InvalidCnpjException',
              'stringable' => 'Stringable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'raw',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'tryFrom',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Build a validated CNPJ or return null when the input is invalid.
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'invalidcnpjexception' => 'App\\Support\\Exceptions\\InvalidCnpjException',
              'stringable' => 'Stringable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => '?self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'raw',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        3 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'normalize',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Strip every non-digit and left-pad to 14 characters.
     *
     * Left-padding recovers CNPJs whose leading zeros were dropped by
     * spreadsheets (a very common CSV import problem).
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'invalidcnpjexception' => 'App\\Support\\Exceptions\\InvalidCnpjException',
              'stringable' => 'Stringable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'string',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'raw',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        4 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'isValid',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Validate an arbitrary (possibly formatted) CNPJ.
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'invalidcnpjexception' => 'App\\Support\\Exceptions\\InvalidCnpjException',
              'stringable' => 'Stringable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'bool',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'raw',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
        5 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'formatted',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Render as the masked form 00.000.000/0000-00.
     */',
             'namespace' => 'App\\Support',
             'uses' => 
            array (
              'invalidcnpjexception' => 'App\\Support\\Exceptions\\InvalidCnpjException',
              'stringable' => 'Stringable',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        6 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => '__toString',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'string',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\Support\\Exceptions\\InvalidCnpjException.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\Support\\Exceptions\\InvalidCnpjException',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => true,
       'extends' => 'InvalidArgumentException',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'for',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => true,
           'returnType' => 'self',
           'parameters' => 
          array (
            0 => 
            \PHPStan\Dependency\ExportedNode\ExportedParameterNode::__set_state(array(
               'name' => 'value',
               'type' => 'string',
               'byRef' => false,
               'variadic' => false,
               'hasDefault' => false,
               'attributes' => 
              array (
              ),
               'phpDoc' => NULL,
               'flags' => 0,
            )),
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\View\\Components\\AppLayout.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\View\\Components\\AppLayout',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\View\\Component',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'render',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the view / contents that represents the component.
     */',
             'namespace' => 'App\\View\\Components',
             'uses' => 
            array (
              'component' => 'Illuminate\\View\\Component',
              'view' => 'Illuminate\\View\\View',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\View\\View',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\app\\View\\Components\\GuestLayout.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'App\\View\\Components\\GuestLayout',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\View\\Component',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'render',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Get the view / contents that represents the component.
     */',
             'namespace' => 'App\\View\\Components',
             'uses' => 
            array (
              'component' => 'Illuminate\\View\\Component',
              'view' => 'Illuminate\\View\\View',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'Illuminate\\View\\View',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\ChangeEventFactory.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Database\\Factories\\ChangeEventFactory',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @extends Factory<ChangeEvent>
 */',
         'namespace' => 'Database\\Factories',
         'uses' => 
        array (
          'changetype' => 'App\\Enums\\ChangeType',
          'severity' => 'App\\Enums\\Severity',
          'changeevent' => 'App\\Models\\ChangeEvent',
          'companysnapshot' => 'App\\Models\\CompanySnapshot',
          'monitoredcompany' => 'App\\Models\\MonitoredCompany',
          'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'definition',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'Database\\Factories',
             'uses' => 
            array (
              'changetype' => 'App\\Enums\\ChangeType',
              'severity' => 'App\\Enums\\Severity',
              'changeevent' => 'App\\Models\\ChangeEvent',
              'companysnapshot' => 'App\\Models\\CompanySnapshot',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'acknowledged',
           'phpDoc' => NULL,
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'static',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\CompanyPartnerFactory.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Database\\Factories\\CompanyPartnerFactory',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @extends Factory<CompanyPartner>
 */',
         'namespace' => 'Database\\Factories',
         'uses' => 
        array (
          'companypartner' => 'App\\Models\\CompanyPartner',
          'companysnapshot' => 'App\\Models\\CompanySnapshot',
          'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'definition',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'Database\\Factories',
             'uses' => 
            array (
              'companypartner' => 'App\\Models\\CompanyPartner',
              'companysnapshot' => 'App\\Models\\CompanySnapshot',
              'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\CompanySnapshotFactory.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Database\\Factories\\CompanySnapshotFactory',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @extends Factory<CompanySnapshot>
 */',
         'namespace' => 'Database\\Factories',
         'uses' => 
        array (
          'companysnapshot' => 'App\\Models\\CompanySnapshot',
          'monitoredcompany' => 'App\\Models\\MonitoredCompany',
          'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'definition',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'Database\\Factories',
             'uses' => 
            array (
              'companysnapshot' => 'App\\Models\\CompanySnapshot',
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\MonitoredCompanyFactory.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Database\\Factories\\MonitoredCompanyFactory',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @extends Factory<MonitoredCompany>
 */',
         'namespace' => 'Database\\Factories',
         'uses' => 
        array (
          'monitoredcompany' => 'App\\Models\\MonitoredCompany',
          'portfolio' => 'App\\Models\\Portfolio',
          'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'definition',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'Database\\Factories',
             'uses' => 
            array (
              'monitoredcompany' => 'App\\Models\\MonitoredCompany',
              'portfolio' => 'App\\Models\\Portfolio',
              'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\OrganizationFactory.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Database\\Factories\\OrganizationFactory',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @extends Factory<Organization>
 */',
         'namespace' => 'Database\\Factories',
         'uses' => 
        array (
          'organization' => 'App\\Models\\Organization',
          'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'definition',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'Database\\Factories',
             'uses' => 
            array (
              'organization' => 'App\\Models\\Organization',
              'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioFactory.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Database\\Factories\\PortfolioFactory',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @extends Factory<Portfolio>
 */',
         'namespace' => 'Database\\Factories',
         'uses' => 
        array (
          'organization' => 'App\\Models\\Organization',
          'portfolio' => 'App\\Models\\Portfolio',
          'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'definition',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'Database\\Factories',
             'uses' => 
            array (
              'organization' => 'App\\Models\\Organization',
              'portfolio' => 'App\\Models\\Portfolio',
              'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\PortfolioScheduledRunFactory.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Database\\Factories\\PortfolioScheduledRunFactory',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @extends Factory<PortfolioScheduledRun>
 */',
         'namespace' => 'Database\\Factories',
         'uses' => 
        array (
          'portfolio' => 'App\\Models\\Portfolio',
          'portfolioscheduledrun' => 'App\\Models\\PortfolioScheduledRun',
          'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'definition',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * @return array<string, mixed>
     */',
             'namespace' => 'Database\\Factories',
             'uses' => 
            array (
              'portfolio' => 'App\\Models\\Portfolio',
              'portfolioscheduledrun' => 'App\\Models\\PortfolioScheduledRun',
              'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\database\\factories\\UserFactory.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Database\\Factories\\UserFactory',
       'phpDoc' => 
      \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
         'phpDocString' => '/**
 * @extends Factory<User>
 */',
         'namespace' => 'Database\\Factories',
         'uses' => 
        array (
          'organization' => 'App\\Models\\Organization',
          'user' => 'App\\Models\\User',
          'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
          'hash' => 'Illuminate\\Support\\Facades\\Hash',
          'str' => 'Illuminate\\Support\\Str',
        ),
         'constUses' => 
        array (
        ),
      )),
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedPropertiesNode::__set_state(array(
           'names' => 
          array (
            0 => 'password',
          ),
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * The current password being used by the factory.
     */',
             'namespace' => 'Database\\Factories',
             'uses' => 
            array (
              'organization' => 'App\\Models\\Organization',
              'user' => 'App\\Models\\User',
              'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
              'hash' => 'Illuminate\\Support\\Facades\\Hash',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'type' => '?string',
           'public' => false,
           'private' => false,
           'static' => true,
           'readonly' => false,
           'abstract' => false,
           'final' => false,
           'publicSet' => false,
           'protectedSet' => false,
           'privateSet' => false,
           'virtual' => false,
           'attributes' => 
          array (
          ),
           'hooks' => 
          array (
          ),
        )),
        1 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'definition',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Define the model\'s default state.
     *
     * @return array<string, mixed>
     */',
             'namespace' => 'Database\\Factories',
             'uses' => 
            array (
              'organization' => 'App\\Models\\Organization',
              'user' => 'App\\Models\\User',
              'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
              'hash' => 'Illuminate\\Support\\Facades\\Hash',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'array',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
        2 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'unverified',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Indicate that the model\'s email address should be unverified.
     */',
             'namespace' => 'Database\\Factories',
             'uses' => 
            array (
              'organization' => 'App\\Models\\Organization',
              'user' => 'App\\Models\\User',
              'factory' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
              'hash' => 'Illuminate\\Support\\Facades\\Hash',
              'str' => 'Illuminate\\Support\\Str',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'static',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
  'C:\\laragon\\www\\vigilo\\database\\seeders\\DatabaseSeeder.php' => 
  array (
    0 => 
    \PHPStan\Dependency\ExportedNode\ExportedClassNode::__set_state(array(
       'name' => 'Database\\Seeders\\DatabaseSeeder',
       'phpDoc' => NULL,
       'abstract' => false,
       'final' => false,
       'extends' => 'Illuminate\\Database\\Seeder',
       'implements' => 
      array (
      ),
       'usedTraits' => 
      array (
        0 => 'Illuminate\\Database\\Console\\Seeds\\WithoutModelEvents',
      ),
       'traitUseAdaptations' => 
      array (
      ),
       'statements' => 
      array (
        0 => 
        \PHPStan\Dependency\ExportedNode\ExportedMethodNode::__set_state(array(
           'name' => 'run',
           'phpDoc' => 
          \PHPStan\Dependency\ExportedNode\ExportedPhpDocNode::__set_state(array(
             'phpDocString' => '/**
     * Seed a demo organization with one user and a portfolio of real CNPJs so
     * the app can be exercised end-to-end (run the refresh to populate data).
     */',
             'namespace' => 'Database\\Seeders',
             'uses' => 
            array (
              'organization' => 'App\\Models\\Organization',
              'portfolio' => 'App\\Models\\Portfolio',
              'user' => 'App\\Models\\User',
              'cnpj' => 'App\\Support\\Cnpj',
              'withoutmodelevents' => 'Illuminate\\Database\\Console\\Seeds\\WithoutModelEvents',
              'seeder' => 'Illuminate\\Database\\Seeder',
              'hash' => 'Illuminate\\Support\\Facades\\Hash',
            ),
             'constUses' => 
            array (
            ),
          )),
           'byRef' => false,
           'public' => true,
           'private' => false,
           'abstract' => false,
           'final' => false,
           'static' => false,
           'returnType' => 'void',
           'parameters' => 
          array (
          ),
           'attributes' => 
          array (
          ),
        )),
      ),
       'attributes' => 
      array (
      ),
    )),
  ),
); },
];
