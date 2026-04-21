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

class CheckUserfuncControl extends AbstractNode
{

    /**
     * @return array
     */
    public function render(): array
    {
        // TODO: Evaluate dynamically when the provider is updated
        //       @see https://docs.typo3.org/typo3cms/CoreApiReference/ApiOverview/FormEngine/Rendering/Index.html#formengine-rendering-nodeexpansion

        $itemsProcFunc = $this->data['databaseRow']['tx_directmailuserfunc_itemsprocfunc'];
        $class = '';

        if (empty($itemsProcFunc) || ItemsProcFunc::isMethodValid($itemsProcFunc)) {
            $iconIdentifier = 'status-status-checked';
            $class = 'btn-success ';
            $titleKey = 'wizard.itemsProcFunc.valid';
        } elseif (!ItemsProcFunc::isClassValid($itemsProcFunc)) {
            $iconIdentifier = 'status-dialog-warning';
            $titleKey = 'wizard.itemsProcFunc.invalidClass';
        } else {
            $iconIdentifier = 'status-dialog-warning';
            $titleKey = 'wizard.itemsProcFunc.invalidMethod';
        }

        $result = [
            'iconIdentifier' => $iconIdentifier,
            'title' => $this->sL($titleKey),
            'linkAttributes' => [
                'class' => $class,
                'data-id' => 'check-' . $this->data['databaseRow']['uid'],
            ]
        ];
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
