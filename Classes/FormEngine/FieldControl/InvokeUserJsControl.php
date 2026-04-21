<?php
/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Causal\DirectMailUserfunc\FormEngine\FieldControl;

use Causal\DirectMailUserfunc\Utility\ItemsProcFunc;
use TYPO3\CMS\Backend\Form\AbstractNode;

class InvokeUserJsControl extends AbstractNode
{

    /**
     * @return array
     */
    public function render(): array
    {
        $result = [];

        $itemsProcFunc = $this->data['databaseRow']['tx_directmailuserfunc_itemsprocfunc'];

        if ($itemsProcFunc !== null && ItemsProcFunc::hasWizard($itemsProcFunc)) {
            $PA = [];
            $wizardJs = ItemsProcFunc::callWizard($itemsProcFunc, $PA, $this->data['databaseRow'], true);
            if (!empty($wizardJs)) {
                $result = [
                    'iconIdentifier' => 'actions-system-extension-configure',
                    'title' => $this->sL('wizard.parameters.title'),
                    'linkAttributes' => [
                        'data-id' => 'wizard-' . $this->data['databaseRow']['uid'],
                    ]
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $key
     * @return string
     */
    protected function sL(string $key): string
    {
        return $GLOBALS['LANG']->sL('LLL:EXT:direct_mail_userfunc/Resources/Private/Language/locallang_tca.xlf:' . $key);
    }
}
