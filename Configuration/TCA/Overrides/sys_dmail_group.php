<?php
$tempColumns = [
    'tx_directmailuserfunc_itemsprocfunc' => [
        'exclude' => false,
        'label' => 'LLL:EXT:direct_mail_userfunc/Resources/Private/Language/locallang_db.xlf:sys_dmail_group.tx_directmailuserfunc_itemsprocfunc',
        'config' => [
            'type' => 'input',
            'size' => 40,
            'fieldControl' => [
                'checkControl' => [
                    'renderType' => 'checkUserfuncControl'
                ]
            ],
            'fieldWizard' => [
                'providerSelector' => [
                    'renderType' => 'providerSelector',
                ]
            ],
            'required' => true,
        ],
    ],
    'tx_directmailuserfunc_params' => [
        'exclude' => false,
        'label' => 'LLL:EXT:direct_mail_userfunc/Resources/Private/Language/locallang_db.xlf:sys_dmail_group.tx_directmailuserfunc_params',
        'config' => [
            'type' => 'text',
            'cols' => 40,
            'rows' => 2,
            'fieldControl' => [
                'invokeControl' => [
                    'renderType' => 'invokeUserJsControl'
                ]
            ],
            'fieldWizard' => [
                'providerSelector' => [
                    'renderType' => 'jsProviderWizard',
                ]
            ],
        ]
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_dmail_group', $tempColumns);

$GLOBALS['TCA']['sys_dmail_group']['columns']['type']['config']['items'][] = [
    'label' => 'LLL:EXT:direct_mail_userfunc/Resources/Private/Language/locallang_tca.xlf:sys_dmail_group.type.I.5',
    'value' => '5',
];
$GLOBALS['TCA']['sys_dmail_group']['types']['5'] = [
    'showitem' => 'type;;;;1-1-1, title;;;;3-3-3, description, tx_directmailuserfunc_itemsprocfunc;;;;5-5-5, tx_directmailuserfunc_params;;;;7-7-7'
];
