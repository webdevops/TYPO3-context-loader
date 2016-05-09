# WebDevOps TYPO3 context configuration loader (TYPO3_CONTEXT)

## Installation

Install this extension via `composer` or TYPO3-TER and add following snippet to your `typo3conf/AdditionalConfiguration.php`:

    <?php
    defined('TYPO3_MODE') || exit('Access denied.');

    require_once __DIR__ . '/ext/context_loader/ContextInit.php';

As example configuration copy `EXT:context_loader/Examples/` to `typo3conf/AdditionalConfiguration/`

## Configuration

### Context examples

TYPO3_CONTEXT=Production (default):
- `typo3conf/AdditionalConfiguration/Production.php`
- `typo3conf/AdditionalConfiguration/Local.php`

TYPO3_CONTEXT=Testing (eg. for Unit tests):
- `typo3conf/AdditionalConfiguration/Testing.php`
- `typo3conf/AdditionalConfiguration/Local.php`

TYPO3_CONTEXT=Development (for development):
- `typo3conf/AdditionalConfiguration/Development.php`
- `typo3conf/AdditionalConfiguration/Local.php`

TYPO3_CONTEXT=Development/Docker (for development inside TYPO3 docker boilerplate):
- `typo3conf/AdditionalConfiguration/Development.php`
- `typo3conf/AdditionalConfiguration/Development/Docker.php`
- `typo3conf/AdditionalConfiguration/Local.php`

TYPO3_CONTEXT=Production/Preview (for preview):
- `typo3conf/AdditionalConfiguration/Development.php`
- `typo3conf/AdditionalConfiguration/Development/Preview.php`
- `typo3conf/AdditionalConfiguration/Local.php`

TYPO3_CONTEXT=Production/Live/Server4711 (specific live server configuration):
- `typo3conf/AdditionalConfiguration/Development.php`
- `typo3conf/AdditionalConfiguration/Development/Live.php`
- `typo3conf/AdditionalConfiguration/Development/Live/Server123.php`
- `typo3conf/AdditionalConfiguration/Local.php`

## Extension configuration

You can also manipulate extension configuration in Context files:

    <?php
    \WebDevOps\ContextLoader\ContextLoader::getInstance()
        ->setExtensionConfiguration('metaseo', 'fookey', 'barvalue');
        ->setExtensionConfigurationList('metaseo', [
            'fookey1' => 'barval1',
            'fookey2' => 'barval2',
        ]);
