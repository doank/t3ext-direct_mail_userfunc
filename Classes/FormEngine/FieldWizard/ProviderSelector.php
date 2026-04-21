<?php
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with TYPO3 source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Causal\DirectMailUserfunc\FormEngine\FieldWizard;

use Causal\DirectMailUserfunc\Utility\ItemsProcFunc;
use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Type\Bitmask\JsConfirmation;

/**
 * This class displays a user wizard.
 *
 * @category    FormEngine\FieldWizard
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   2012-2026 Causal Sàrl
 * @license     https://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class ProviderSelector extends AbstractNode
{

    /**
     * @return array
     */
    public function render(): array
    {
        if (!count($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['direct_mail_userfunc']['userFunc'])) {
            return [];
        }

        // Sort list of providers
        $providers = [];
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['direct_mail_userfunc']['userFunc'] as $provider) {
            $itemsProcFunc = $provider['class'] . '->' . $provider['method'];
            $providers[$itemsProcFunc] = $this->sL($provider['label']);
        }
        asort($providers);

        $hasOptionSelected = false;
        $options = [];
        foreach ($providers as $itemsProcFunc => $label) {
            $selected = '';
            if ($this->data['databaseRow']['tx_directmailuserfunc_itemsprocfunc'] === $itemsProcFunc) {
                $hasOptionSelected = true;
                $selected = ' selected="selected"';
            }
            $options[] = sprintf('<option value="%s"%s>%s</option>', $itemsProcFunc, $selected, $label);
        }

        $providerElID = 'providerSelector' . $this->data['vanillaUid'];

        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create(
            '@causal/direct_mail_userfunc/provider-selector.js'
        )->instance([
            'providerId' => $providerElID,
            'selectorName' => $this->data['parameterArray']['itemFormElName'],
        ]);

        // Class "form-control" is for sure useless in TYPO3 v11
        $selector = '
            <select class="form-control form-select form-control-adapt" id="' . $providerElID . '">
                <option value=""></option>' .
            implode('', $options) . '
            </select>
        ';

        $hideInput = $hasOptionSelected && ItemsProcFunc::isMethodValid($this->data['databaseRow']['tx_directmailuserfunc_itemsprocfunc']);

        $out = [];
        $out[] = '<div class="form-control-wrap">';
        $out[] =    '<label class="t3js-formengine-label">';
        $out[] =        $this->sL('LLL:EXT:direct_mail_userfunc/Resources/Private/Language/locallang_tca.xlf:wizard.itemsProcFunc.providers');
        $out[] =    '</label>';
        $out[] =    '<div class="form-control-wrap">';
        $out[] =        $selector;
        $out[] =        ($hideInput ? '<div style="display:none">' : '') . $this->data['parameterArray']['itemFormElValue'] . ($hideInput ? '</div>' : '');
        $out[] =    '</div>';
        $out[] = '</div>';

        $resultArray['html'] = implode(LF, $out);
        return $resultArray;
    }

    /**
     * @param string $input
     * @return string
     */
    protected function sL(string $input): string
    {
        if (!strcmp(substr($input, 0, 8), 'LLL:EXT:')) {
            $input = $GLOBALS['LANG']->sL($input);
        }

        return htmlspecialchars($input);
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUserAuthentication(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}
