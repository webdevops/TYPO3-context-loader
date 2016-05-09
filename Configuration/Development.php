<?php

// Disable frontend caching (single hit caching)
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_pagesection']['options'] =
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_hash']['options'] =
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_pages']['options'] = [];
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_pagesection']['backend'] =
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_hash']['backend'] =
$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_pages']['backend'] =
    'TYPO3\\CMS\\Core\\Cache\\Backend\\TransientMemoryBackend';

return [
    'SYS' => [
        'trustedHostsPattern'  => '.*',
        'devIPmask'            => '*',
        'sqlDebug'             => 1,
        'displayErrors'        => 1,
        'enableDeprecationLog' => 'file',
        'systemLogLevel'       => 0,
    ],
    'BE'  => [
		// set installer password to 'dev'
        'installToolPassword' => '$P$C4a1FXXNaZmkWi6LUxrDKSXjMBToX0/',
        'debug'               => true,
        'sessionTimeout'      => '360000'
    ],
    'FE'  => [
        'disableNoCacheParameter' => false,
        'debug'                   => true,
    ],
];
