<?php
defined('TYPO3') or die();

(function () {
    // Register backend module
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'LlmsTxtGenerator',
        'tools',
        'llms',
        '',
        [
            \Dkd\LlmsTxtGenerator\Controller\LlmsBackendController::class => 'index, generate, preview, settings, save',
        ],
        [
            'access' => 'admin',
            'icon' => 'EXT:llms_txt_generator/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:llms_txt_generator/Resources/Private/Language/locallang_mod.xlf',
        ]
    );

    // Add module configuration
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
        'tx_llmstxtgenerator',
        'EXT:llms_txt_generator/Resources/Private/Language/locallang_csh.xlf'
    );
})();