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

/**
 * This class displays a user wizard.
 *
 * @category    FormEngine\FieldWizard
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   2012-2026 Causal Sàrl
 * @license     https://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class JsProviderWizard extends AbstractNode
{

    /**
     * @return array
     */
    public function render()
    {
        if (!count($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['direct_mail_userfunc']['userFunc'])) {
            return [];
        }

        $itemsProcFunc = $this->data['databaseRow']['tx_directmailuserfunc_itemsprocfunc'];

        if ($itemsProcFunc !== null && ItemsProcFunc::hasWizard($itemsProcFunc)) {
            $PA = $this->data['parameterArray'];

            $wizardJs = ItemsProcFunc::callWizard($itemsProcFunc, $PA, $this->data['databaseRow']);
            if (!empty($wizardJs)) {
                $wizardButton = 'a[data-id="wizard-' . $this->data['databaseRow']['uid'] . '"]';
                $js = '<script type="text/javascript">';
                $js .= <<<JS
$(function() {
    $('$wizardButton').click(function() {
        $wizardJs
    });
});
JS;
                $js .= '</script>';
                return [
                    'html' => $js,
                ];
            }
        }

        return [];
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
