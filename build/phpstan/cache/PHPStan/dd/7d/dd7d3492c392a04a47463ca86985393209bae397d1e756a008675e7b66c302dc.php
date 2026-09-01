<?php declare(strict_types = 1);

// odsl-C:\laragon\www\vigilo\app
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v1-enums',
   'data' => 
  array (
    'C:\\laragon\\www\\vigilo\\app\\Contracts\\CnpjDataProvider.php' => 
    array (
      0 => '2fefb735de786c9e694c916b23fd229fcd4f9a1c60ca4bbedf04ab9b2e9b6c18',
      1 => 
      array (
        0 => 'app\\contracts\\cnpjdataprovider',
      ),
      2 => 
      array (
        0 => 'app\\contracts\\fetch',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\DTO\\ChangeEventData.php' => 
    array (
      0 => 'fb47cf4f9f134f52008d52f04ac4165b4cd0444039fe915d412b0f23f499eb9f',
      1 => 
      array (
        0 => 'app\\dto\\changeeventdata',
      ),
      2 => 
      array (
        0 => 'app\\dto\\__construct',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\DTO\\CompanyData.php' => 
    array (
      0 => '91478d177c239304b744519c6369c3ce5bcc454ff87c1b04848c69cad3242f0d',
      1 => 
      array (
        0 => 'app\\dto\\companydata',
      ),
      2 => 
      array (
        0 => 'app\\dto\\__construct',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\DTO\\ImportReport.php' => 
    array (
      0 => '4c46c15ddcf25a3f6db799fb7effdc5441c5fa2dbd49612baebb6db6892a66a7',
      1 => 
      array (
        0 => 'app\\dto\\importreport',
      ),
      2 => 
      array (
        0 => 'app\\dto\\__construct',
        1 => 'app\\dto\\importedcount',
        2 => 'app\\dto\\rejectedcount',
        3 => 'app\\dto\\rejectedwithreason',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\DTO\\PartnerData.php' => 
    array (
      0 => 'e8957462b3009ab830062095dc4d21b713757ead4dc603cc285bda393d030d0d',
      1 => 
      array (
        0 => 'app\\dto\\partnerdata',
      ),
      2 => 
      array (
        0 => 'app\\dto\\__construct',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Enums\\ChangeType.php' => 
    array (
      0 => '2aa8b1255c2d4c5a82c2dca04e15862793a40fe14506cc1d6319bf80106e61a0',
      1 => 
      array (
        0 => 'app\\enums\\changetype',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Enums\\RefreshStatus.php' => 
    array (
      0 => 'caa76bc27660662b381cb5f9c623b802808e98cca56bfa8fe36c572aee5b497a',
      1 => 
      array (
        0 => 'app\\enums\\refreshstatus',
      ),
      2 => 
      array (
        0 => 'app\\enums\\label',
        1 => 'app\\enums\\badgeclasses',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Enums\\Severity.php' => 
    array (
      0 => '21cb3594002ed2c91adbb52c67885984ffcdddbf52e1ec9bccb92933af33f9b9',
      1 => 
      array (
        0 => 'app\\enums\\severity',
      ),
      2 => 
      array (
        0 => 'app\\enums\\weight',
        1 => 'app\\enums\\label',
        2 => 'app\\enums\\badgeclasses',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Auth\\VerifyEmailController.php' => 
    array (
      0 => 'd33fb522e3610364dca2391076aae007c56018df1f6b2b201667da428af697f8',
      1 => 
      array (
        0 => 'app\\http\\controllers\\auth\\verifyemailcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\auth\\__invoke',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Controller.php' => 
    array (
      0 => '09e5cac5a69959ccf23d756cda697b993df937c106655d124ff27ca5fe24a705',
      1 => 
      array (
        0 => 'app\\http\\controllers\\controller',
      ),
      2 => 
      array (
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Jobs\\ImportCompaniesCsvJob.php' => 
    array (
      0 => '64c44ec317ccb79902f5fddc09b4608f37eb7067e826d3fec0dd94d5dbaa657a',
      1 => 
      array (
        0 => 'app\\jobs\\importcompaniescsvjob',
      ),
      2 => 
      array (
        0 => 'app\\jobs\\__construct',
        1 => 'app\\jobs\\handle',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Jobs\\RefreshMonitoredCompanyJob.php' => 
    array (
      0 => '158f01189507dec37aff6574398766cf91d3465a54a7690d22176284f2918245',
      1 => 
      array (
        0 => 'app\\jobs\\refreshmonitoredcompanyjob',
      ),
      2 => 
      array (
        0 => 'app\\jobs\\__construct',
        1 => 'app\\jobs\\uniqueid',
        2 => 'app\\jobs\\retryuntil',
        3 => 'app\\jobs\\middleware',
        4 => 'app\\jobs\\handle',
        5 => 'app\\jobs\\failed',
        6 => 'app\\jobs\\persistchanges',
        7 => 'app\\jobs\\notifyorganization',
        8 => 'app\\jobs\\markrefreshed',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Livewire\\Actions\\Logout.php' => 
    array (
      0 => '2e97be3671f060ca8d24a1565f24ba64b77f5085df7007fecd7e8977f3a6a265',
      1 => 
      array (
        0 => 'app\\livewire\\actions\\logout',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\actions\\__invoke',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Livewire\\Alerts\\Inbox.php' => 
    array (
      0 => '3e588b610f8f02cfd7c45aaab2762b3dc1953d735be91c7487ec01ea60f252ee',
      1 => 
      array (
        0 => 'app\\livewire\\alerts\\inbox',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\alerts\\setseverity',
        1 => 'app\\livewire\\alerts\\acknowledge',
        2 => 'app\\livewire\\alerts\\acknowledgeall',
        3 => 'app\\livewire\\alerts\\render',
        4 => 'app\\livewire\\alerts\\openfilteredevents',
        5 => 'app\\livewire\\alerts\\scopedevents',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Livewire\\Companies\\Show.php' => 
    array (
      0 => 'ab43604464353a87bdefe04ddf60c24eb9fda53cff6431bd72baf7f785cf1aac',
      1 => 
      array (
        0 => 'app\\livewire\\companies\\show',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\companies\\mount',
        1 => 'app\\livewire\\companies\\queuerefresh',
        2 => 'app\\livewire\\companies\\render',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Livewire\\Concerns\\InteractsWithCurrentOrganization.php' => 
    array (
      0 => 'f3e8e378e67fbf3b105ad67c44eb1404b8dfefd2c514ad7a575bd5e3571ee00d',
      1 => 
      array (
        0 => 'app\\livewire\\concerns\\interactswithcurrentorganization',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\concerns\\currentuser',
        1 => 'app\\livewire\\concerns\\currentorganizationid',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Livewire\\Dashboard.php' => 
    array (
      0 => '55ae19ab9ef32f659a20629e6533a72f8f42a3e9fcb446e68e0d4a86755bd29e',
      1 => 
      array (
        0 => 'app\\livewire\\dashboard',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\render',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Livewire\\Forms\\LoginForm.php' => 
    array (
      0 => '73b57e08a448424a197ec2bd71b2fa1c8fd592900b5e8313d58099857e197a43',
      1 => 
      array (
        0 => 'app\\livewire\\forms\\loginform',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\forms\\authenticate',
        1 => 'app\\livewire\\forms\\ensureisnotratelimited',
        2 => 'app\\livewire\\forms\\throttlekey',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Index.php' => 
    array (
      0 => '3297b0c7971bb0b1ce4e19828c596b626f2678d32a2f62ace25e9a8565bedb87',
      1 => 
      array (
        0 => 'app\\livewire\\portfolios\\index',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\portfolios\\create',
        1 => 'app\\livewire\\portfolios\\startedit',
        2 => 'app\\livewire\\portfolios\\saveedit',
        3 => 'app\\livewire\\portfolios\\canceledit',
        4 => 'app\\livewire\\portfolios\\delete',
        5 => 'app\\livewire\\portfolios\\queuerefresh',
        6 => 'app\\livewire\\portfolios\\render',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Livewire\\Portfolios\\Show.php' => 
    array (
      0 => '96f8f58eec789e9951138584378ed601b4cc4d419bd76919636a94526c5aba26',
      1 => 
      array (
        0 => 'app\\livewire\\portfolios\\show',
      ),
      2 => 
      array (
        0 => 'app\\livewire\\portfolios\\mount',
        1 => 'app\\livewire\\portfolios\\togglescheduleday',
        2 => 'app\\livewire\\portfolios\\applypreset',
        3 => 'app\\livewire\\portfolios\\clearscheduledays',
        4 => 'app\\livewire\\portfolios\\discardschedulechanges',
        5 => 'app\\livewire\\portfolios\\saveschedule',
        6 => 'app\\livewire\\portfolios\\normalizedscheduledays',
        7 => 'app\\livewire\\portfolios\\nextscheduledrun',
        8 => 'app\\livewire\\portfolios\\schedulefireson',
        9 => 'app\\livewire\\portfolios\\addcompany',
        10 => 'app\\livewire\\portfolios\\import',
        11 => 'app\\livewire\\portfolios\\queuerefresh',
        12 => 'app\\livewire\\portfolios\\queuerefreshall',
        13 => 'app\\livewire\\portfolios\\setstatusfilter',
        14 => 'app\\livewire\\portfolios\\reprocessfiltered',
        15 => 'app\\livewire\\portfolios\\statusconstraint',
        16 => 'app\\livewire\\portfolios\\removecompany',
        17 => 'app\\livewire\\portfolios\\render',
        18 => 'app\\livewire\\portfolios\\basequery',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Models\\ChangeEvent.php' => 
    array (
      0 => '04cdf530b41ec96ac66f5ff38b29bac1bdf9decacaa94599188c59203f08d800',
      1 => 
      array (
        0 => 'app\\models\\changeevent',
      ),
      2 => 
      array (
        0 => 'app\\models\\casts',
        1 => 'app\\models\\monitoredcompany',
        2 => 'app\\models\\fromsnapshot',
        3 => 'app\\models\\tosnapshot',
        4 => 'app\\models\\isacknowledged',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Models\\CompanyPartner.php' => 
    array (
      0 => 'b2b4bed1748daf292554ea1ddf6baef7391a50f66ff128cfe9d3856f3f7f3e76',
      1 => 
      array (
        0 => 'app\\models\\companypartner',
      ),
      2 => 
      array (
        0 => 'app\\models\\companysnapshot',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Models\\CompanySnapshot.php' => 
    array (
      0 => '3e0f6b0b5c1195bbbce457ff0a3249e6a9871d7fc01807224dbb526564a9694e',
      1 => 
      array (
        0 => 'app\\models\\companysnapshot',
      ),
      2 => 
      array (
        0 => 'app\\models\\casts',
        1 => 'app\\models\\monitoredcompany',
        2 => 'app\\models\\partners',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Models\\Concerns\\BelongsToOrganization.php' => 
    array (
      0 => '7736cbf860e1488ec1a6da861371084f63ea87c95574b0da9225126668dbbca0',
      1 => 
      array (
        0 => 'app\\models\\concerns\\belongstoorganization',
      ),
      2 => 
      array (
        0 => 'app\\models\\concerns\\bootbelongstoorganization',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Models\\MonitoredCompany.php' => 
    array (
      0 => '28b64c40543042a2b2b0f9dc32d4a909b707311297561d1a5a4e1d7a89a16a51',
      1 => 
      array (
        0 => 'app\\models\\monitoredcompany',
      ),
      2 => 
      array (
        0 => 'app\\models\\casts',
        1 => 'app\\models\\formattedcnpj',
        2 => 'app\\models\\portfolio',
        3 => 'app\\models\\snapshots',
        4 => 'app\\models\\latestsnapshot',
        5 => 'app\\models\\changeevents',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Models\\Organization.php' => 
    array (
      0 => '17ac6e2c0593b9997e12dc36c332a3a1c6ab83eefbefec81e2a7fb63a0aa1494',
      1 => 
      array (
        0 => 'app\\models\\organization',
      ),
      2 => 
      array (
        0 => 'app\\models\\users',
        1 => 'app\\models\\portfolios',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Models\\Portfolio.php' => 
    array (
      0 => '7b87d7205006639c583dc4872b152df05952185c8a1772d6f9f37f4fd1138e32',
      1 => 
      array (
        0 => 'app\\models\\portfolio',
      ),
      2 => 
      array (
        0 => 'app\\models\\casts',
        1 => 'app\\models\\organization',
        2 => 'app\\models\\monitoredcompanies',
        3 => 'app\\models\\scheduledruns',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Models\\Scopes\\OrganizationScope.php' => 
    array (
      0 => '2ccdd53c8844f7d0a5a0c644c37b582826a0e6162c4242e8944321675614c321',
      1 => 
      array (
        0 => 'app\\models\\scopes\\organizationscope',
      ),
      2 => 
      array (
        0 => 'app\\models\\scopes\\apply',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Models\\User.php' => 
    array (
      0 => '2ec5de35b06a382872fc17322b1383cf156ae134330b50df29180a3adbd6ff26',
      1 => 
      array (
        0 => 'app\\models\\user',
      ),
      2 => 
      array (
        0 => 'app\\models\\organization',
        1 => 'app\\models\\casts',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompaniesImported.php' => 
    array (
      0 => '19b94be1dcc03f671cb58c9f73d31ae4cb4feb042e679527cef7351574f78614',
      1 => 
      array (
        0 => 'app\\notifications\\companiesimported',
      ),
      2 => 
      array (
        0 => 'app\\notifications\\__construct',
        1 => 'app\\notifications\\via',
        2 => 'app\\notifications\\toarray',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Notifications\\CompanyChangeDetected.php' => 
    array (
      0 => '209e337eac59d7a2374baab8ecf64d615b8f90d0bd2a7a6963cc02faf759fcf4',
      1 => 
      array (
        0 => 'app\\notifications\\companychangedetected',
      ),
      2 => 
      array (
        0 => 'app\\notifications\\__construct',
        1 => 'app\\notifications\\via',
        2 => 'app\\notifications\\tomail',
        3 => 'app\\notifications\\toarray',
        4 => 'app\\notifications\\highestseverity',
        5 => 'app\\notifications\\describe',
        6 => 'app\\notifications\\formattedcnpj',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Policies\\MonitoredCompanyPolicy.php' => 
    array (
      0 => '7d8defe8bb086a9ce40c348882f76bfda585e806d75dafea9e82af0acff2a680',
      1 => 
      array (
        0 => 'app\\policies\\monitoredcompanypolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\view',
        1 => 'app\\policies\\update',
        2 => 'app\\policies\\delete',
        3 => 'app\\policies\\owns',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Policies\\PortfolioPolicy.php' => 
    array (
      0 => '9315f984855fce8c50dff8749a88d9fbf4c8775d2166e9b530e2044df9b4efd8',
      1 => 
      array (
        0 => 'app\\policies\\portfoliopolicy',
      ),
      2 => 
      array (
        0 => 'app\\policies\\viewany',
        1 => 'app\\policies\\view',
        2 => 'app\\policies\\create',
        3 => 'app\\policies\\update',
        4 => 'app\\policies\\delete',
        5 => 'app\\policies\\owns',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Providers\\AppServiceProvider.php' => 
    array (
      0 => 'b1d5988f2ad301ad94b8a50f40d40bf5b248609c8f6410776fe6e0b375454015',
      1 => 
      array (
        0 => 'app\\providers\\appserviceprovider',
      ),
      2 => 
      array (
        0 => 'app\\providers\\register',
        1 => 'app\\providers\\boot',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\BrasilApiProvider.php' => 
    array (
      0 => '190ae36d635a9e153090b8f43d492a998703e433c0c73015ddc03824732c814e',
      1 => 
      array (
        0 => 'app\\providers\\cnpj\\brasilapiprovider',
      ),
      2 => 
      array (
        0 => 'app\\providers\\cnpj\\__construct',
        1 => 'app\\providers\\cnpj\\fetch',
        2 => 'app\\providers\\cnpj\\throttle',
        3 => 'app\\providers\\cnpj\\map',
        4 => 'app\\providers\\cnpj\\mappartners',
        5 => 'app\\providers\\cnpj\\buildlogradouro',
        6 => 'app\\providers\\cnpj\\stringornull',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Providers\\Cnpj\\Exceptions\\CnpjProviderException.php' => 
    array (
      0 => '006c32426102cfb227799369d7e9bebfecacf137d87228f520131fd8c77a22b5',
      1 => 
      array (
        0 => 'app\\providers\\cnpj\\exceptions\\cnpjproviderexception',
      ),
      2 => 
      array (
        0 => 'app\\providers\\cnpj\\exceptions\\requestfailed',
        1 => 'app\\providers\\cnpj\\exceptions\\unreachable',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Providers\\VoltServiceProvider.php' => 
    array (
      0 => '4b0f313a107b9c3c17a7dd9036dd36a06745aa51dae27ef1d066134aa657b411',
      1 => 
      array (
        0 => 'app\\providers\\voltserviceprovider',
      ),
      2 => 
      array (
        0 => 'app\\providers\\register',
        1 => 'app\\providers\\boot',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyDiffer.php' => 
    array (
      0 => 'd8291ff1c32e4a94b56251b799ad583da6ecc13a0bff9d78a790fcb8dd3c6ec2',
      1 => 
      array (
        0 => 'app\\services\\companydiffer',
      ),
      2 => 
      array (
        0 => 'app\\services\\diff',
        1 => 'app\\services\\comparesituacao',
        2 => 'app\\services\\comparepartners',
        3 => 'app\\services\\comparescalar',
        4 => 'app\\services\\keypartners',
        5 => 'app\\services\\partnerlabel',
        6 => 'app\\services\\isnegativesituacao',
        7 => 'app\\services\\normalize',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Services\\CompanyImporter.php' => 
    array (
      0 => '8915c8fd62f517d7a11ca8faf21e063f28f4d9c3116ce3ad97a5f4a046c89a00',
      1 => 
      array (
        0 => 'app\\services\\companyimporter',
      ),
      2 => 
      array (
        0 => 'app\\services\\import',
        1 => 'app\\services\\importfromfile',
        2 => 'app\\services\\readcsv',
        3 => 'app\\services\\sniffdelimiter',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Services\\SnapshotService.php' => 
    array (
      0 => 'bf02ab90411c4e8258495a4de4118ccf2984722fd05e70aaac4af97f80f6319e',
      1 => 
      array (
        0 => 'app\\services\\snapshotservice',
      ),
      2 => 
      array (
        0 => 'app\\services\\store',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Support\\Cnpj.php' => 
    array (
      0 => '58db6f8e4c34ce0a87266d4e85532f9086bc29dc75840c0ec26e7a8ffa40f2f6',
      1 => 
      array (
        0 => 'app\\support\\cnpj',
      ),
      2 => 
      array (
        0 => 'app\\support\\__construct',
        1 => 'app\\support\\fromstring',
        2 => 'app\\support\\tryfrom',
        3 => 'app\\support\\normalize',
        4 => 'app\\support\\isvalid',
        5 => 'app\\support\\formatted',
        6 => 'app\\support\\__tostring',
        7 => 'app\\support\\isvalidnormalized',
        8 => 'app\\support\\checkdigit',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Support\\Exceptions\\InvalidCnpjException.php' => 
    array (
      0 => '6371aedacdf23b7d8e406c88d7aeb4a8eb4daf0a19c9b152a30b98ea0e7d1233',
      1 => 
      array (
        0 => 'app\\support\\exceptions\\invalidcnpjexception',
      ),
      2 => 
      array (
        0 => 'app\\support\\exceptions\\for',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\View\\Components\\AppLayout.php' => 
    array (
      0 => '3ca6426929b27a88ad8fa55304249004be01d1f436e0f1194bd81488888f55ab',
      1 => 
      array (
        0 => 'app\\view\\components\\applayout',
      ),
      2 => 
      array (
        0 => 'app\\view\\components\\render',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\View\\Components\\GuestLayout.php' => 
    array (
      0 => '4f0964e6c277da1854b3c369f390361c7d8517884f79878fc5b355da5dc67322',
      1 => 
      array (
        0 => 'app\\view\\components\\guestlayout',
      ),
      2 => 
      array (
        0 => 'app\\view\\components\\render',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Actions\\QueuePortfolioRefresh.php' => 
    array (
      0 => '9b4156b19ae2732fd8de8cb3548d2a0bfdd456adee1ce38e57710f6df7c1b10b',
      1 => 
      array (
        0 => 'app\\actions\\queueportfoliorefresh',
      ),
      2 => 
      array (
        0 => 'app\\actions\\handle',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Actions\\RunDuePortfolioSchedules.php' => 
    array (
      0 => '09fa74a068d850d9244f9f37e9b630cc707d98bcc4597bae313224df9f02519d',
      1 => 
      array (
        0 => 'app\\actions\\rundueportfolioschedules',
      ),
      2 => 
      array (
        0 => 'app\\actions\\__construct',
        1 => 'app\\actions\\handle',
        2 => 'app\\actions\\matchedday',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Console\\Commands\\RunScheduledPortfolioRefreshes.php' => 
    array (
      0 => '490634e567f8c8e3a75a41631eb7fb4f2e6e736c2430e2b7554d8f69b1e7d194',
      1 => 
      array (
        0 => 'app\\console\\commands\\runscheduledportfoliorefreshes',
      ),
      2 => 
      array (
        0 => 'app\\console\\commands\\handle',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Models\\PortfolioScheduledRun.php' => 
    array (
      0 => '10d7f72bb437420f90e3a81b151e3b453181842e6cfcfc9225670a3b86c3dd25',
      1 => 
      array (
        0 => 'app\\models\\portfolioscheduledrun',
      ),
      2 => 
      array (
        0 => 'app\\models\\casts',
        1 => 'app\\models\\portfolio',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Console\\Commands\\IssueApiToken.php' => 
    array (
      0 => 'bbbcfbedaa8905b8e29a8f2f961c9530bb1bfeb9d49708bad79ada94cb500ae3',
      1 => 
      array (
        0 => 'app\\console\\commands\\issueapitoken',
      ),
      2 => 
      array (
        0 => 'app\\console\\commands\\handle',
      ),
      3 => 
      array (
      ),
    ),
    'C:\\laragon\\www\\vigilo\\app\\Http\\Controllers\\Api\\CnpjLookupController.php' => 
    array (
      0 => '54b3437b0492337dfd4294b932ffd59066929e30df94e16d252e0f9fce1397c9',
      1 => 
      array (
        0 => 'app\\http\\controllers\\api\\cnpjlookupcontroller',
      ),
      2 => 
      array (
        0 => 'app\\http\\controllers\\api\\__invoke',
      ),
      3 => 
      array (
      ),
    ),
  ),
));