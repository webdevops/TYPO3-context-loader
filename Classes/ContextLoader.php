<?php

namespace WebDevOps\ContextLoader;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Context loader (TYPO3_CONTEXT)
 *
 * Examples:
 *
 * TYPO3_CONTEXT=Production
 *    -> typo3conf/AdditionalConfiguration/Production.php
 *
 * TYPO3_CONTEXT=Testing
 *    -> typo3conf/AdditionalConfiguration/Testing.php
 *
 * TYPO3_CONTEXT=Development
 *    -> typo3conf/AdditionalConfiguration/Development.php
 *
 * TYPO3_CONTEXT=Production/Staging
 *    -> typo3conf/AdditionalConfiguration/Production.php
 *    -> typo3conf/AdditionalConfiguration/Production/Staging.php
 *
 * TYPO3_CONTEXT=Production/Live
 *    -> typo3conf/AdditionalConfiguration/Production.php
 *    -> typo3conf/AdditionalConfiguration/Production/Live.php
 *
 * TYPO3_CONTEXT=Production/Live/Server4711
 *    -> typo3conf/AdditionalConfiguration/Production.php
 *    -> typo3conf/AdditionalConfiguration/Production/Live.php
 *    -> typo3conf/AdditionalConfiguration/Production/Live/Server123.php
 *
 */
class ContextLoader
{
	/**
	 * @var ContextLoader
	 */
	static $instance;

	/**
	 * Load indicator
	 *
	 * @var bool
	 */
	static $configurationLoaded = false;

    /**
     * @var \TYPO3\CMS\Core\Core\ApplicationContext
     */
    protected $applicationContext;

    /**
     * Context list (reversed)
     *
     * @var array
     */
    protected $contextList = [];

    /**
     * Configuration path list (simple files)
     *
     * @var array
     */
    protected $confPathList = [];

    /**
     * List of extension configuration directives (overwrites)
     *
     * @var array
     */
    protected $extensionConfList = [];

	/**
	 * Check if configuration is already loaded
	 *
	 * @return bool
	 */
	public function isConfigurationLoaded()
	{
		return $this::$configurationLoaded;
	}

	/**
	 * @return ContextLoader
	 */
	public static function getInstance()
	{
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	protected function __construct()
	{

	}

    /**
     * @return $this
     */
    public function init()
    {
		$this
			->checkEnvironment()
			->buildContextList();

        return $this;
    }

    /**
     * @return $this
     */
    public function checkEnvironment()
    {
        // Check CLI mode
        if (defined('TYPO3_cliMode')) {
            $contextEnv = getenv('TYPO3_CONTEXT');

            if (empty($contextEnv)) {
                echo '[ERROR] TYPO3_CONTEXT not set or found for AdditionalConfiguration.php' . "\n";
                exit(1);
            }
        }

        return $this;
    }

    /**
     * @param string $path Path to directory
     *
     * @return $this
     */
    public function addContextConfigurationDirectory($path)
    {
        $this->confPathList['context'][] = $path;

        return $this;
    }

    /**
     * @param string $path Path to configuration file
     *
     * @return $this
     */
    public function addConfigurationFile($path)
    {
        $this->confPathList['file'][] = $path;

        return $this;
    }

    /**
     * @return $this
     */
    public function loadConfiguration()
    {
		$this
			->loadConfigurationFromContextDirectories()
			->loadConfigurationFromConfigurationFiles()
			->applyExtensionConfiguration();

		$this::$configurationLoaded = true;

        return $this;
    }

    /**
     * Build context list
     *
     * @return $this
     */
    protected function buildContextList()
    {
		$this->applicationContext = GeneralUtility::getApplicationContext();

        $contextList    = [];
        $currentContext = $this->applicationContext;
        do {
            $contextList[] = (string)$currentContext;
        } while ($currentContext = $currentContext->getParent());

        // Reverse list, general first (eg. PRODUCTION), then specific last (eg. SERVER)
        $this->contextList = array_reverse($contextList);

        return $this;
    }

    /**
     * Load configuration based on current context
     *
     * @return $this
     */
    protected function loadConfigurationFromContextDirectories()
    {
        if (!empty($this->confPathList['context'])) {
            foreach ($this->confPathList['context'] as $path) {
                foreach ($this->contextList as $context) {
                    // Sanitize context name
                    $context = preg_replace('/[^-_\.a-zA-Z0-9\/]/', '', $context);

                    // Build config file name
                    $this->loadAndApplyConfigurationFile($path . '/' . $context . '.php');
                }
            }
        }

        return $this;
    }

    /**
     * Load simple file configuration
     *
     * @return $this
     */
    protected function loadConfigurationFromConfigurationFiles()
    {
        if (!empty($this->confPathList['file'])) {
            foreach ($this->confPathList['file'] as $path) {
                $this->loadAndApplyConfigurationFile($path);
            }
        }

        return $this;
    }

    /**
     * @param string $configurationFile Configuration file
     *
     * @return $this
     */
    protected function loadAndApplyConfigurationFile($configurationFile)
    {
        // Load config file
        if (file_exists($configurationFile)) {
            // Keep this variable for automatic injection into requried files!
            $contextLoader = $this;

            // Load configuration file
            $retConf = require $configurationFile;

            // Apply return'ed configuration (if available)
            if (!empty($retConf) && is_array($retConf)) {
                $GLOBALS['TYPO3_CONF_VARS'] = array_replace_recursive($GLOBALS['TYPO3_CONF_VARS'], $retConf);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function applyExtensionConfiguration()
    {
        if (!empty($this->extensionConfList)) {
            $extConf = &$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'];

            foreach ($this->extensionConfList as $extension => $settingList) {
				$conf = [];

                if (!empty($extConf[$extension])) {
					$conf = unserialize($extConf[$extension]);
				}

				$conf = array_merge($conf, $settingList);
				$extConf[$extension] = serialize($conf);
            }
        }

        return $this;
    }

	/**
	 * @return $this
	 */
	public function appendContextNameToSitename()
	{
		$GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] = sprintf(
			'%s [[%s]]',
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'],
			strtoupper((string)$this->applicationContext
			));

		return $this;
	}

    /**
     * @return $this
     */
    public function appendContextNameToSitenameInDevelopment()
    {
		if ($this->isDevelopmentContext()) {
			$this->appendContextNameToSitename();
		}

        return $this;
    }

	/**
	 * @return bool
	 */
	public function isDevelopmentContext()
	{
		return $this->applicationContext->isDevelopment();
	}

	/**
	 * @return bool
	 */
	public function isProductionContext()
	{
		return $this->applicationContext->isProduction();
	}

	/**
	 * @return bool
	 */
	public function isTestingContext()
	{
		return $this->applicationContext->isTesting();
	}

	/**
     * Set extension configuration value
     *
     * @param string $extension Extension name
     * @param string $setting   Configuration setting name
     * @param mixed  $value     Configuration value
     *
     * @return $this
     */
    public function setExtensionConfiguration($extension, $setting, $value = null)
    {
        $this->extensionConfList[$extension][$setting] = $value;

        return $this;
    }

    /**
     * Set extension configuration value (by list)
     *
     * @param string $extension   Extension name
     * @param array  $settingList List of settings
     *
     * @return $this
     */
    public function setExtensionConfigurationList($extension, array $settingList)
    {
        if (empty($this->extensionConfList[$extension])) {
            $this->extensionConfList[$extension] = $settingList;
        } else {
            $this->extensionConfList[$extension] = array_merge($this->extensionConfList[$extension], $settingList);
        }

        return $this;
    }
}
