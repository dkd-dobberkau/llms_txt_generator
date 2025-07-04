<?php
defined('TYPO3') or die();

(function () {
    // Register CLI command
    if (TYPO3_MODE === 'BE') {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = 
            \Dkd\LlmsTxtGenerator\Command\GenerateCommand::class;
    }

    // Register scheduler task
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Dkd\LlmsTxtGenerator\Task\GenerateLlmsTask::class] = [
        'extension' => 'llms_txt_generator',
        'title' => 'LLMs.txt Generator Task',
        'description' => 'Automatically generates llms.txt files for configured sites',
        'additionalFields' => \Dkd\LlmsTxtGenerator\Task\GenerateLlmsTaskAdditionalFieldProvider::class
    ];

    // Register hooks for content changes
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 
        \Dkd\LlmsTxtGenerator\Hooks\DataHandlerHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 
        \Dkd\LlmsTxtGenerator\Hooks\DataHandlerHook::class;

    // Register cache
    if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['llms_txt_generator'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['llms_txt_generator'] = [
            'frontend' => \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend::class,
            'backend' => \TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
            'options' => [
                'defaultLifetime' => 86400, // 24 hours
            ],
        ];
    }

    // Register console commands for TYPO3 v11+
    if (class_exists(\Symfony\Component\Console\Application::class)) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['llms_txt_generator']['commands'] = [
            'llms:generate' => [
                'class' => \Dkd\LlmsTxtGenerator\Command\GenerateCommand::class,
                'schedulable' => false,
            ],
        ];
    }
})();