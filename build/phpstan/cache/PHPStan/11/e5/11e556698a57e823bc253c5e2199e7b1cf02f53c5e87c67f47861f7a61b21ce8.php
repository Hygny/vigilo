<?php declare(strict_types = 1);

// ftm-C:\laragon\www\vigilo\app\Jobs\RefreshMonitoredCompanyJob.php
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v5-2.3.3',
   'data' => 
  array (
    0 => 
    array (
      '49b93930eb9bb0fd6835375b6f964e87' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'f45bb4525e2ad553374d065af501d866' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'container' => 'Illuminate\\Container\\Container',
          'str' => 'Illuminate\\Support\\Str',
          'batchfake' => 'Illuminate\\Support\\Testing\\Fakes\\BatchFake',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Batchable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Batchable',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'd6d1be1a0473541b663d0d307975901f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'container' => 'Illuminate\\Container\\Container',
          'str' => 'Illuminate\\Support\\Str',
          'batchfake' => 'Illuminate\\Support\\Testing\\Fakes\\BatchFake',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'batch',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Batchable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Batchable',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '2126e5100773135efd6aa8220f28cfe2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'container' => 'Illuminate\\Container\\Container',
          'str' => 'Illuminate\\Support\\Str',
          'batchfake' => 'Illuminate\\Support\\Testing\\Fakes\\BatchFake',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'batching',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Batchable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Batchable',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '06bf442817aeed5581bab208ffeecf9d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'container' => 'Illuminate\\Container\\Container',
          'str' => 'Illuminate\\Support\\Str',
          'batchfake' => 'Illuminate\\Support\\Testing\\Fakes\\BatchFake',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'withBatchId',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Batchable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Batchable',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'be86708b33445c9b03fd0670bc23d7e8' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'carbonimmutable' => 'Carbon\\CarbonImmutable',
          'container' => 'Illuminate\\Container\\Container',
          'str' => 'Illuminate\\Support\\Str',
          'batchfake' => 'Illuminate\\Support\\Testing\\Fakes\\BatchFake',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'withFakeBatch',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Batchable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Batchable',
          3 => NULL,
          4 => NULL,
        ),
      )),
      'be0acfc1c0bd015ad578a80641ec5eb0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Foundation\\Queue',
         'uses' => 
        array (
          'queueablebybus' => 'Illuminate\\Bus\\Queueable',
          'dispatchable' => 'Illuminate\\Foundation\\Bus\\Dispatchable',
          'interactswithqueue' => 'Illuminate\\Queue\\InteractsWithQueue',
          'serializesmodels' => 'Illuminate\\Queue\\SerializesModels',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Foundation\\Queue\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Foundation\\Queue\\Queueable',
          3 => NULL,
          4 => NULL,
        ),
      )),
      '37d6c3b3417bc78f5f8392ef488b4e01' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Foundation\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'dispatcher' => 'Illuminate\\Contracts\\Bus\\Dispatcher',
          'fluent' => 'Illuminate\\Support\\Fluent',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Foundation\\Bus\\Dispatchable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Foundation\\Bus\\Dispatchable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '4450ddad3d23054ad7dcdb589ef915af' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Foundation\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'dispatcher' => 'Illuminate\\Contracts\\Bus\\Dispatcher',
          'fluent' => 'Illuminate\\Support\\Fluent',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'dispatch',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Foundation\\Bus\\Dispatchable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Foundation\\Bus\\Dispatchable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'f4cbf6c26eef398517c4a3aa551dab16' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Foundation\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'dispatcher' => 'Illuminate\\Contracts\\Bus\\Dispatcher',
          'fluent' => 'Illuminate\\Support\\Fluent',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'dispatchIf',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Foundation\\Bus\\Dispatchable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Foundation\\Bus\\Dispatchable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '5ff355e5d728acb4e643193f686d2e6a' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Foundation\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'dispatcher' => 'Illuminate\\Contracts\\Bus\\Dispatcher',
          'fluent' => 'Illuminate\\Support\\Fluent',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'dispatchUnless',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Foundation\\Bus\\Dispatchable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Foundation\\Bus\\Dispatchable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '3f23158cc03b19e7ecd1e2a0bf993bfb' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Foundation\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'dispatcher' => 'Illuminate\\Contracts\\Bus\\Dispatcher',
          'fluent' => 'Illuminate\\Support\\Fluent',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'dispatchSync',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Foundation\\Bus\\Dispatchable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Foundation\\Bus\\Dispatchable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '2fb806dc621c20181041b4b5097803de' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Foundation\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'dispatcher' => 'Illuminate\\Contracts\\Bus\\Dispatcher',
          'fluent' => 'Illuminate\\Support\\Fluent',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'dispatchAfterResponse',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Foundation\\Bus\\Dispatchable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Foundation\\Bus\\Dispatchable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '0d0160183f8d5b31c18ac73973e301aa' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Foundation\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'dispatcher' => 'Illuminate\\Contracts\\Bus\\Dispatcher',
          'fluent' => 'Illuminate\\Support\\Fluent',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'withChain',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Foundation\\Bus\\Dispatchable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Foundation\\Bus\\Dispatchable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '27cd64f1a201933b534774d85b16868f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Foundation\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'dispatcher' => 'Illuminate\\Contracts\\Bus\\Dispatcher',
          'fluent' => 'Illuminate\\Support\\Fluent',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'newPendingDispatch',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Foundation\\Bus\\Dispatchable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Foundation\\Bus\\Dispatchable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '7047b00740684a435e70aaf30e4e263e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '9bf6b5e6665b34281c306491fecd9821' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Support',
         'uses' => 
        array (
          'carboninterval' => 'Carbon\\CarbonInterval',
          'dateinterval' => 'DateInterval',
          'datetimeinterface' => 'DateTimeInterface',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Support\\InteractsWithTime',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Support\\InteractsWithTime',
          3 => 'Illuminate\\Queue\\InteractsWithQueue',
          4 => NULL,
        ),
      )),
      '7b2540f4510ccfdfb5f3431f5663a9b4' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Support',
         'uses' => 
        array (
          'carboninterval' => 'Carbon\\CarbonInterval',
          'dateinterval' => 'DateInterval',
          'datetimeinterface' => 'DateTimeInterface',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'secondsUntil',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Support\\InteractsWithTime',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Support\\InteractsWithTime',
          3 => 'Illuminate\\Queue\\InteractsWithQueue',
          4 => NULL,
        ),
      )),
      '696345addaa4f8869c8d4c178f88115f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Support',
         'uses' => 
        array (
          'carboninterval' => 'Carbon\\CarbonInterval',
          'dateinterval' => 'DateInterval',
          'datetimeinterface' => 'DateTimeInterface',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'availableAt',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Support\\InteractsWithTime',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Support\\InteractsWithTime',
          3 => 'Illuminate\\Queue\\InteractsWithQueue',
          4 => NULL,
        ),
      )),
      'b6f38959e2d42a31b613fe422be3a609' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Support',
         'uses' => 
        array (
          'carboninterval' => 'Carbon\\CarbonInterval',
          'dateinterval' => 'DateInterval',
          'datetimeinterface' => 'DateTimeInterface',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'parseDateInterval',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Support\\InteractsWithTime',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Support\\InteractsWithTime',
          3 => 'Illuminate\\Queue\\InteractsWithQueue',
          4 => NULL,
        ),
      )),
      '3c75137e9c6afad3b7d3196f97b7d4e5' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Support',
         'uses' => 
        array (
          'carboninterval' => 'Carbon\\CarbonInterval',
          'dateinterval' => 'DateInterval',
          'datetimeinterface' => 'DateTimeInterface',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'currentTime',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Support\\InteractsWithTime',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Support\\InteractsWithTime',
          3 => 'Illuminate\\Queue\\InteractsWithQueue',
          4 => NULL,
        ),
      )),
      'b1d77ed010d5fc792a2b55718460b73c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Support',
         'uses' => 
        array (
          'carboninterval' => 'Carbon\\CarbonInterval',
          'dateinterval' => 'DateInterval',
          'datetimeinterface' => 'DateTimeInterface',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'runTimeForHumans',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Support\\InteractsWithTime',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Support\\InteractsWithTime',
          3 => 'Illuminate\\Queue\\InteractsWithQueue',
          4 => NULL,
        ),
      )),
      '053298bfedd4011772cfcbc160a8deaf' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'attempts',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '2a4ce12d74807b255c22eaba9aedff7b' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'delete',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'f7a77f1953e0d8769633432b1ae8408f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'fail',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'a3f2947c4f256b5f9b348d436ee8c251' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'release',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'c58a0bf3e59d2be5ceaa6aa2c20eb081' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'withFakeQueueInteractions',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '4da681a5297678961291103da34a1b9f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'assertDeleted',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '0b9788e2bdb6b618ef9edb3bc869dc80' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'assertNotDeleted',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'cb7ac0b6c47f79cfde35635367ba18e0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'assertFailed',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'e702d7c50d4642737b2d4fbcea4e2e36' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'assertFailedWith',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '924e6b9b1290bfa52c17fb10b746bb58' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'assertNotFailed',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '56a0069a12e38ea0acbe0f89c02aa558' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'assertReleased',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '6828df634ec73829a5efba300277714d' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'assertNotReleased',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '163cf3fbcecf9abc295a62b0d76fba74' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'ensureQueueInteractionsHaveBeenFaked',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '1ec6a7567468938fbaf49295af183aa7' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'datetimeinterface' => 'DateTimeInterface',
          'jobcontract' => 'Illuminate\\Contracts\\Queue\\Job',
          'fakejob' => 'Illuminate\\Queue\\Jobs\\FakeJob',
          'interactswithtime' => 'Illuminate\\Support\\InteractsWithTime',
          'invalidargumentexception' => 'InvalidArgumentException',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
          'throwable' => 'Throwable',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'setJob',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\InteractsWithQueue',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\InteractsWithQueue',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '81f5b6952d819d25ed2f3eb3ab323738' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '4e09a96c3d75e0fe846d4979a2f6dc2c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'onConnection',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'd2c01776869b4d225e9508e0faaa5ee3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'onQueue',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'c30b4a69473d6eb3ce0378cc9b3310fa' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'onGroup',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '4d9c70ea80f914f59e56b812ca6a66c5' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'withDeduplicator',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'f4f4f91a5c1a0331d5d2b5f4dc891c30' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'allOnConnection',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'b6d60dbe488ebbb64b76405bdcd8a756' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'allOnQueue',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'b6c4ce43881fb25f27e2b101647d0fe7' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'delay',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '6c811c1b265932535f884183e41a850f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'withoutDelay',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '7d39aab6ad5cb7896dfa5cd6aad5989c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'afterCommit',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'f0b9b48dd8ae5921608aedb4f2760acc' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'beforeCommit',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '8b156f1ab727f11b373d228f98bf6a4c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'through',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'afed05456ef1ff0b11d093956bee8366' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'chain',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '93e1574f5037c188fd59763ed21cc590' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'prependToChain',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '8c22131e3bc61cef750ea3f3e433caa2' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'appendToChain',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '90f42ca8a37db6066fc12a8b20c6559e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'serializeJob',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'b0b2c2f506e1c838d5a03af0442a5b2c' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'dispatchNextJobInChain',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      'f25c26f9b0a142546828c1fee699e45f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'invokeChainCatchCallbacks',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '1608f77caa44ce8e9a18ac4e1820e794' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'assertHasChain',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '7967de837e642860ef0c096f4861f940' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Bus',
         'uses' => 
        array (
          'closure' => 'Closure',
          'callqueuedclosure' => 'Illuminate\\Queue\\CallQueuedClosure',
          'arr' => 'Illuminate\\Support\\Arr',
          'collection' => 'Illuminate\\Support\\Collection',
          'serializableclosure' => 'Laravel\\SerializableClosure\\SerializableClosure',
          'phpunit' => 'PHPUnit\\Framework\\Assert',
          'runtimeexception' => 'RuntimeException',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'assertDoesntHaveChain',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Bus\\Queueable',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Bus\\Queueable',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '4c33f99fc9724e9c705f4e5a2ba309dd' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'withoutrelations' => 'Illuminate\\Queue\\Attributes\\WithoutRelations',
          'reflector' => 'Illuminate\\Support\\Reflector',
          'reflectionclass' => 'ReflectionClass',
          'reflectionproperty' => 'ReflectionProperty',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\SerializesModels',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\SerializesModels',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '1a9f71caaee51241525361f0a4f1f523' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'modelidentifier' => 'Illuminate\\Contracts\\Database\\ModelIdentifier',
          'queueablecollection' => 'Illuminate\\Contracts\\Queue\\QueueableCollection',
          'queueableentity' => 'Illuminate\\Contracts\\Queue\\QueueableEntity',
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'aspivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\AsPivot',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'collection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => NULL,
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers',
          3 => 'Illuminate\\Queue\\SerializesModels',
          4 => NULL,
        ),
      )),
      '8e157538a2d41e13958e84fe6435ab30' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'modelidentifier' => 'Illuminate\\Contracts\\Database\\ModelIdentifier',
          'queueablecollection' => 'Illuminate\\Contracts\\Queue\\QueueableCollection',
          'queueableentity' => 'Illuminate\\Contracts\\Queue\\QueueableEntity',
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'aspivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\AsPivot',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'collection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'getSerializedPropertyValue',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers',
          3 => 'Illuminate\\Queue\\SerializesModels',
          4 => NULL,
        ),
      )),
      'f475919c718c2b78df3a60337d7b09b5' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'modelidentifier' => 'Illuminate\\Contracts\\Database\\ModelIdentifier',
          'queueablecollection' => 'Illuminate\\Contracts\\Queue\\QueueableCollection',
          'queueableentity' => 'Illuminate\\Contracts\\Queue\\QueueableEntity',
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'aspivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\AsPivot',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'collection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'getRestoredPropertyValue',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers',
          3 => 'Illuminate\\Queue\\SerializesModels',
          4 => NULL,
        ),
      )),
      '74c83b2da9a8cf0f5997341ccb2d1e86' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'modelidentifier' => 'Illuminate\\Contracts\\Database\\ModelIdentifier',
          'queueablecollection' => 'Illuminate\\Contracts\\Queue\\QueueableCollection',
          'queueableentity' => 'Illuminate\\Contracts\\Queue\\QueueableEntity',
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'aspivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\AsPivot',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'collection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'restoreCollection',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers',
          3 => 'Illuminate\\Queue\\SerializesModels',
          4 => NULL,
        ),
      )),
      '661bdd639e1790a74b588b9aa5607c5e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'modelidentifier' => 'Illuminate\\Contracts\\Database\\ModelIdentifier',
          'queueablecollection' => 'Illuminate\\Contracts\\Queue\\QueueableCollection',
          'queueableentity' => 'Illuminate\\Contracts\\Queue\\QueueableEntity',
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'aspivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\AsPivot',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'collection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'restoreModel',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers',
          3 => 'Illuminate\\Queue\\SerializesModels',
          4 => NULL,
        ),
      )),
      '8c131e4ea262dea4e3ad63caabc88841' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'modelidentifier' => 'Illuminate\\Contracts\\Database\\ModelIdentifier',
          'queueablecollection' => 'Illuminate\\Contracts\\Queue\\QueueableCollection',
          'queueableentity' => 'Illuminate\\Contracts\\Queue\\QueueableEntity',
          'eloquentcollection' => 'Illuminate\\Database\\Eloquent\\Collection',
          'aspivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Concerns\\AsPivot',
          'pivot' => 'Illuminate\\Database\\Eloquent\\Relations\\Pivot',
          'collection' => 'Illuminate\\Support\\Collection',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'getQueryForModelRestoration',
         'templatePhpDocNodes' => 
        array (
          'TModel' => 
          array (
            0 => '@template',
            1 => 
            \PHPStan\PhpDocParser\Ast\PhpDoc\TemplateTagValueNode::__set_state(array(
               'name' => 'TModel',
               'bound' => 
              \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode::__set_state(array(
                 'name' => '\\Illuminate\\Database\\Eloquent\\Model',
                 'attributes' => 
                array (
                  'startLine' => 4,
                  'endLine' => 4,
                ),
              )),
               'default' => NULL,
               'lowerBound' => NULL,
               'description' => '',
               'attributes' => 
              array (
                'startLine' => 4,
                'endLine' => 4,
              ),
            )),
          ),
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers',
          3 => 'Illuminate\\Queue\\SerializesModels',
          4 => NULL,
        ),
      )),
      '4511d6a26f5b1938ef96b952322a59f5' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'withoutrelations' => 'Illuminate\\Queue\\Attributes\\WithoutRelations',
          'reflector' => 'Illuminate\\Support\\Reflector',
          'reflectionclass' => 'ReflectionClass',
          'reflectionproperty' => 'ReflectionProperty',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => '__serialize',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\SerializesModels',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\SerializesModels',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '7253166a9b61b229c85548e202223cda' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'withoutrelations' => 'Illuminate\\Queue\\Attributes\\WithoutRelations',
          'reflector' => 'Illuminate\\Support\\Reflector',
          'reflectionclass' => 'ReflectionClass',
          'reflectionproperty' => 'ReflectionProperty',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => '__unserialize',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\SerializesModels',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\SerializesModels',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '4625bf1635ccd0f3771e644e12bcabb3' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
         'namespace' => 'Illuminate\\Queue',
         'uses' => 
        array (
          'withoutrelations' => 'Illuminate\\Queue\\Attributes\\WithoutRelations',
          'reflector' => 'Illuminate\\Support\\Reflector',
          'reflectionclass' => 'ReflectionClass',
          'reflectionproperty' => 'ReflectionProperty',
        ),
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'getPropertyValue',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => NULL,
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => 'Illuminate\\Queue\\SerializesModels',
         'traitData' => 
        array (
          0 => 'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php',
          1 => 'App\\Jobs\\RefreshMonitoredCompanyJob',
          2 => 'Illuminate\\Queue\\SerializesModels',
          3 => 'Illuminate\\Foundation\\Queue\\Queueable',
          4 => NULL,
        ),
      )),
      '4ede215e2b69fb2e1874920c3efbc643' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => '__construct',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
           'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '72b8fe549bac026237c2553f64734508' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'uniqueId',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
           'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '8a9ff4f415c41b3d539dd8f1bb69ae77' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'retryUntil',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
           'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '722f98ed659c5e844eb550373e70a06e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'middleware',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
           'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '7ea67cef7a74e4870ee6b42367e7a12f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'handle',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
           'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '0908c86dd2bd02f989a5892c4f3df4d0' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'failed',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
           'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'a5ccb560576b30a66fbcf08c9cead64e' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'persistChanges',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
           'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      '0e82beecbff73a8f6b28c37b3286b58f' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'notifyOrganization',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
           'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
      'b6db6a2e01a844ede224f7cb955d4420' => 
      \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
         'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
         'functionName' => 'markRefreshed',
         'templatePhpDocNodes' => 
        array (
        ),
         'parent' => 
        \PHPStan\Analyser\IntermediaryNameScope::__set_state(array(
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
           'className' => 'App\\Jobs\\RefreshMonitoredCompanyJob',
           'functionName' => NULL,
           'templatePhpDocNodes' => 
          array (
          ),
           'parent' => NULL,
           'typeAliasesMap' => 
          array (
          ),
           'bypassTypeAliases' => false,
           'constUses' => 
          array (
          ),
           'typeAliasClassName' => NULL,
           'traitData' => NULL,
        )),
         'typeAliasesMap' => 
        array (
        ),
         'bypassTypeAliases' => false,
         'constUses' => 
        array (
        ),
         'typeAliasClassName' => NULL,
         'traitData' => NULL,
      )),
    ),
    1 => 
    array (
      'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php' => '158f01189507dec37aff6574398766cf91d3465a54a7690d22176284f2918245',
      'C:\\laragon\\www\\vigilo\\vendor\\composer\\..\\laravel\\framework\\src\\Illuminate\\Bus\\Batchable.php' => '7d56393405cc5d406732779c525bdc026c18f22699cd7e1acc5a441b6939de6d',
      'C:\\laragon\\www\\vigilo\\vendor\\composer\\..\\laravel\\framework\\src\\Illuminate\\Foundation\\Queue\\Queueable.php' => '3f02abd5d38d7cf07e64a46b9cc5e578004e5ff10401432f683ef354bc8f3419',
      'C:\\laragon\\www\\vigilo\\vendor\\composer\\..\\laravel\\framework\\src\\Illuminate\\Foundation\\Bus\\Dispatchable.php' => '551294291775e57fbd590f0ed288a91cca683d42fac08e60c87e39b73617d47b',
      'C:\\laragon\\www\\vigilo\\vendor\\composer\\..\\laravel\\framework\\src\\Illuminate\\Queue\\InteractsWithQueue.php' => '315b11da66ac6485726ba7406ccafce8e8b633f9e6d9a09ed0eea239c6aeffee',
      'C:\\laragon\\www\\vigilo\\vendor\\composer\\..\\laravel\\framework\\src\\Illuminate\\Support\\InteractsWithTime.php' => 'ee4ef3a2e714fa539b223287a3a62b618b1d3a9e44f2e1f92981f2c3e2773ad5',
      'C:\\laragon\\www\\vigilo\\vendor\\composer\\..\\laravel\\framework\\src\\Illuminate\\Bus\\Queueable.php' => '9c435cc66baff3e0c9688f91a815830d1c36d14ab2e3cbc9633177d83c904c9a',
      'C:\\laragon\\www\\vigilo\\vendor\\composer\\..\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesModels.php' => 'c8fa6bd3fbc479228c4957878377cfa936e96b3c2f43c338e78a45279d48aa37',
      'C:\\laragon\\www\\vigilo\\vendor\\composer\\..\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php' => 'e55ed2ef9bd3d8898b152f55dda7e6e1825cd5d016b4271949bdf1efe4fc2812',
    ),
  ),
));