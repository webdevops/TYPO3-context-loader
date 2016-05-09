<?php

// Docker environment for TYPO3 Docker Boilerplate

return [
	'DB' => [
		'database' => 'typo3',
		'host'     => 'mysql',
		'port'     => '3306',
		'username' => 'dev',
		'password' => 'dev',
	],
    'SYS' => [
		'doNotCheckReferer'   => TRUE,
        'trustedHostsPattern' => '.*',
    ],
    'GFX' => [
		'im_version_5' => 'gm',
        'im_path'      => '/usr/bin/',
        'im_path_lzw'  => '/usr/bin/',
    ],
];
