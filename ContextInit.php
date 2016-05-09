<?php
defined('TYPO3_MODE') || exit('Access denied.');

call_user_func(function () {
	$confLoader = \WebDevOps\ContextLoader\ContextLoader::getInstance();

	if (!$confLoader->isConfigurationLoaded()) {
		$confLoader
			->init()
			// Add configuration from EXT:context_loader (default context configuration)
			->addContextConfigurationDirectory(PATH_site . '/typo3conf/ext/context_loader/Configuration')
			// Add local context configuration
			->addContextConfigurationDirectory(PATH_site . '/typo3conf/AdditionalConfiguration')
			// Add specific instance configuration
			->addConfigurationFile(PATH_site . '/typo3conf/AdditionalConfiguration/Local.php')
			// Load configuration files
			->loadConfiguration()
			// Add context name to sitename
			->appendContextNameToSitenameInDevelopment();
	}
});
