<?php

return [
    'llms' => [
        'parent' => 'tools',
        'position' => ['after' => 'extensionmanager'],
        'access' => 'admin',
        'workspaces' => 'live',
        'icon' => 'EXT:llms_txt_generator/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:llms_txt_generator/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'LlmsTxtGenerator',
        'controllerActions' => [
            \Dkd\LlmsTxtGenerator\Controller\LlmsBackendController::class => [
                'index',
                'generate', 
                'preview',
                'settings',
                'save'
            ],
        ],
    ],
];