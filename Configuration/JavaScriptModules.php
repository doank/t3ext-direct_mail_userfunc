<?php
return [
    // Unsure about the need for those dependencies at this point:
    'dependencies' => [
        'backend',
        'core',
    ],
    'imports' => [
        '@causal/direct_mail_userfunc/' => 'EXT:direct_mail_userfunc/Resources/Public/ECMAScript6/',
    ],
];
