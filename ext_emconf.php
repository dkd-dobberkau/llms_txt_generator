<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'LLMs.txt Generator',
    'description' => 'Automatically generates llms.txt files for TYPO3 websites to optimize them for Large Language Model consumption',
    'category' => 'plugin',
    'author' => 'Your Name',
    'author_email' => 'your.email@example.com',
    'state' => 'alpha',
    'clearCacheOnLoad' => true,
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
            'php' => '7.4.0-8.3.99',
        ],
        'conflicts' => [],
        'suggests' => [
            'scheduler' => '',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Dkd\\LlmsTxtGenerator\\' => 'Classes/',
        ],
    ],
];