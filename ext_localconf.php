<?php
defined('TYPO3') || die();

(static function () {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1535016737] = [
        'nodeName' => 'checkUserfuncControl',
        'priority' => 30,
        'class' => \Causal\DirectMailUserfunc\FormEngine\FieldControl\CheckUserfuncControl::class
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1535361874] = [
        'nodeName' => 'invokeUserJsControl',
        'priority' => 30,
        'class' => \Causal\DirectMailUserfunc\FormEngine\FieldControl\InvokeUserJsControl::class
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1597059292] = [
        'nodeName' => 'providerSelector',
        'priority' => 70,
        'class' => \Causal\DirectMailUserfunc\FormEngine\FieldWizard\ProviderSelector::class
    ];
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1597061623] = [
        'nodeName' => 'jsProviderWizard',
        'priority' => 70,
        'class' => \Causal\DirectMailUserfunc\FormEngine\FieldWizard\JsProviderWizard::class
    ];
})();
