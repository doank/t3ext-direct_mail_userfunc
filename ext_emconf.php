<?php

// -------------------------------------------------------------------------
// Extension Manager/Repository config file for ext: "direct_mail_userfunc"
// -------------------------------------------------------------------------

$EM_CONF[$_EXTKEY] = [
    'title' => 'External Providers for Direct Mail',
    'description' => 'Adds support for external providers to Direct Mail. This extension extends the types of recipient lists handled by Direct Mail with an entry for parameterized custom lists. These custom lists are prepared by user functions and may easily reuse your own business logic.',
    'category' => 'module',
    'author' => 'Xavier Perseguers',
    'author_company' => 'Causal Sàrl',
    'author_email' => 'xavier@causal.ch',
    'state' => 'stable',
    'version' => '2.5.0-dev',
    'constraints' => [
        'depends' => [
            'php' => '8.1.0-8.4.99',
            'typo3' => '12.4.0-12.4.99',
            'direct_mail' => '10.0.0-10.0.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => ['Causal\\DirectMailUserfunc\\' => 'Classes']
    ],
];
