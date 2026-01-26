<?php
defined('TYPO3_MODE') || defined('TYPO3') || die();

(static function (string $_EXTKEY) {
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY])) {
        // Allow extensions to register themselves as userfunc providers
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['userFunc'] = [];
    }

    // Register a custom data provider for \TYPO3\CMS\Backend\Form\FormEngine
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord']
        [\Causal\DirectMailUserfunc\FormEngine\FormDataProvider\DatabaseEditVirtualRow::class] = [
            'depends' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseEditRow::class,
            ],
            'before' => [
                \TYPO3\CMS\Backend\Form\FormDataProvider\InlineOverrideChildTca::class,
            ],
        ];

    // Register hook into \TYPO3\CMS\Backend\Form\FormEngine
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = \Causal\DirectMailUserfunc\Hook\DataHandler::class;

    // Register sample user functions if needed
    $typo3Branch = class_exists(\TYPO3\CMS\Core\Information\Typo3Version::class)
        ? (new \TYPO3\CMS\Core\Information\Typo3Version())->getBranch()
        : TYPO3_branch;
    if (version_compare($typo3Branch, '9.5', '<')) {
        $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][$_EXTKEY]);
    } else {
        $extConf = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get($_EXTKEY);
    }
    if ($extConf['enableSamples']) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['direct_mail_userfunc']['userFunc'][] = [
            'class' => \Causal\DirectMailUserfunc\Samples\TestList::class,
            'method' => 'myRecipientList',
            'label' => 'LLL:EXT:direct_mail_userfunc/Resources/Private/Language/locallang.xlf:userfunction.myRecipientList'
        ];

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['userFunc'][] = [
            'class' => \Causal\DirectMailUserfunc\Samples\TestListTca::class,
            'method' => 'myRecipientList',
            'label' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:userfunction.myRecipientListTca'
        ];
    }
})('direct_mail_userfunc');
