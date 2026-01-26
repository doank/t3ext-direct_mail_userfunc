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

namespace Causal\DirectMailUserfunc\Hook;

use Causal\DirectMailUserfunc\Utility\ItemsProcFunc;
use Causal\DirectMailUserfunc\Utility\TcaUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class hooks into \TYPO3\CMS\Core\DataHandling\DataHandler to process the virtual fields.
 *
 * @category    Hook
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   2013-2025 Causal Sàrl
 * @license     https://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class DataHandler
{

    /**
     * Stores virtual field values into column tx_directmailuserfunc_params.
     * Hook in \TYPO3\CMS\Core\DataHandling\DataHandler
     *
     * @param array $incomingFieldArray
     * @param string $table
     * @param int|string $id
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     */
    public function processDatamap_preProcessFieldArray(array &$incomingFieldArray, $table, $id, \TYPO3\CMS\Core\DataHandling\DataHandler $pObj)
    {
        if ($table !== 'sys_dmail_group') {
            return;
        }

        if (strpos($id, 'NEW') !== false) {
            $row = [];
        } else {
            $row = \TYPO3\CMS\Backend\Utility\BackendUtility::getRecord($table, $id);
        }
        $currentValues = ItemsProcFunc::decodeUserParameters($row);

        if (isset($incomingFieldArray['tx_directmailuserfunc_itemsprocfunc'])) {
            $itemsProcFunc = $incomingFieldArray['tx_directmailuserfunc_itemsprocfunc'];
        } else {
            $itemsProcFunc = $row['tx_directmailuserfunc_itemsprocfunc'] ?? null;
        }

        if ($itemsProcFunc !== null && ItemsProcFunc::hasWizardFields($itemsProcFunc)) {
            $wizardFields = ItemsProcFunc::callWizardFields($itemsProcFunc, $pObj);
            TcaUtility::reconfigureTCA($wizardFields, $row);
        }

        $virtualValues = [];
        foreach ($incomingFieldArray as $field => $value) {
            if (str_starts_with($field, TcaUtility::VIRTUAL_PREFIX)) {
                $virtualField = substr($field, strlen('tx_directmailuserfunc_virtual_'));

                // Evaluate field
                $curValue = isset($currentValues[$virtualField]) ? $currentValues[$virtualField] : '';
                $res = $this->checkValue($table, $field, $value, $curValue, $id, 'update', $row['pid'], $pObj);
                if (isset($res['value'])) {
                    $virtualValues[$virtualField] = $res['value'];
                }

                unset($incomingFieldArray[$field]);
            }
        }

        if (!empty($virtualValues)) {
            $newValues = array_merge($currentValues, $virtualValues);
            ItemsProcFunc::encodeUserParameters($incomingFieldArray, $newValues);
        }

        TcaUtility::resetTCA();
    }

    /**
     * @param string $table Table name
     * @param string $field Field name
     * @param string $value Value to be evaluated. Notice, this is the INPUT value from the form
     * @param string $curValue The original value (from existing record)
     * @param string $id The record-uid, mainly - but not exclusively - used for logging
     * @param string $status 'update' or 'new' flag
     * @param int $realPid The real PID value of the record. For updates, this is just the pid of the record.
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $pObj
     * @return array Returns the evaluated $value as key "value" in this array. Can be checked with isset($res['value']) ...
     * @see \TYPO3\CMS\Core\DataHandling\DataHandler::checkValue()
     */
    protected function checkValue($table, $field, $value, $curValue, $id, $status, $realPid, \TYPO3\CMS\Core\DataHandling\DataHandler $pObj)
    {
        // Result array
        $res = [];

        if (!isset($GLOBALS['TCA'][$table]['columns'][$field]['config'])) {
            // Happens when switching from a wizard-based TCA to another type of provider without wizard
            return $res;
        }

        // For our use $tscPID is always the real PID
        $tscPID = $realPid;
        // Getting config for the field
        $tcaFieldConf = $GLOBALS['TCA'][$table]['columns'][$field]['config'];

        // Perform processing:
        $recFID = '';
        $res = $pObj->checkValue_SW($res, $value, $tcaFieldConf, $table, $id, $curValue, $status, $realPid, $recFID, $field, $tscPID, []);
        return $res;
    }
}
