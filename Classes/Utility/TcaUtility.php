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

namespace Causal\DirectMailUserfunc\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class allows the TCA to be extended with virtual fields.
 *
 * @category    Utility
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   2018-2026 Causal Sàrl
 * @license     https://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class TcaUtility
{
    const VIRTUAL_PREFIX = 'tx_directmailuserfunc_virtual_';

    /**
     * Reconfigures the TCA with custom fields.
     *
     * @param array|null $fields
     * @param array $row
     * @param array $processedTca
     */
    public static function reconfigureTCA(?array $fields, array &$row, array &$processedTca = [])
    {
        if ($fields === null) {
            // The user class is used for both TCA and non TCA-based additional parameters
            // and the standard text area should be shown
            // see https://forge.typo3.org/issues/53287
            return;
        }

        // Transform standard textarea field into "passthrough" so that DataHandler allows changes on it
        // while FormEngine does not render it anymore
        $GLOBALS['TCA']['sys_dmail_group']['columns']['tx_directmailuserfunc_params']['config']['type'] = 'passthrough';

        if (count($fields) === 0) {
            // The user class does not need any additional parameters
            return;
        }

        $populateProcessedTca = !empty($processedTca);

        // BEWARE: this one is done here to prevent side effect when returning above
        $processedTca['columns']['tx_directmailuserfunc_params']['config']['type'] = 'passthrough';

        $currentValues = ItemsProcFunc::decodeUserParameters($row);

        if (!isset($fields['types']) || !isset($fields['types']['5'])) {
            // Without a definition for type "5", custom fields are not shown
            // so, we automatically create a basic configuration to show them
            $fields['types']['5'] = [
                'showitem' => implode(', ', array_keys($fields['columns']))
            ];
        }

        // Prefix each additional field
        $prefixedFields = [
            'columns' => [],
            'types' => $fields['types'],
            'palettes' => isset($fields['palettes']) ? $fields['palettes'] : [],
            'ctrl' => isset($fields['ctrl']) ? $fields['ctrl'] : [],
        ];
        foreach ($fields['columns'] as $field => $fieldInfo) {
            $virtualField = static::VIRTUAL_PREFIX . $field;
            $prefixedFields['columns'][$virtualField] = $fieldInfo;

            if (isset($currentValues[$field])) {
                // Populate virtual field's value
                $row[$virtualField] = $currentValues[$field];
            }

            foreach ($prefixedFields['types'] as &$typeInfo) {
                $typeInfo['showitem'] = preg_replace(
                    '/(^|[, ])' . preg_quote($field, '/') . '([,; ]|$)/',
                    '$1' . $virtualField . '$2',
                    $typeInfo['showitem']
                );
            }

            foreach ($prefixedFields['palettes'] as &$paletteInfo) {
                $paletteInfo['showitem'] = preg_replace(
                    '/(^|[, ])' . preg_quote($field, '/') . '([,; ]|$)/',
                    '$1' . $virtualField . '$2',
                    $paletteInfo['showitem']
                );
            }

            if (!empty($prefixedFields['ctrl']['requestUpdate'])) {
                $prefixedFields['ctrl']['requestUpdate'] = preg_replace(
                    '/(^|[, ])' . preg_quote($field, '/') . '([,; ]|$)/',
                    '$1' . $virtualField . '$2',
                    $prefixedFields['ctrl']['requestUpdate']
                );
            }
        }

        // Reconfigure TCA with virtual fields
        foreach ($prefixedFields['columns'] as $field => $fieldInfo) {
            $GLOBALS['TCA']['sys_dmail_group']['columns'][$field] = $fieldInfo;
            $processedTca['columns'][$field] = $fieldInfo;
        }

        foreach ($prefixedFields['types'] as $type => $typeInfo) {
            if (substr($GLOBALS['TCA']['sys_dmail_group']['types'][$type]['showitem'], -strlen($typeInfo['showitem'])) !== $typeInfo['showitem']) {
                $GLOBALS['TCA']['sys_dmail_group']['types'][$type]['showitem'] .= ',' . $typeInfo['showitem'];
            }
            if (substr($processedTca['types'][$type]['showitem'] ?? '', -strlen($typeInfo['showitem'])) !== $typeInfo['showitem']) {
                $processedTca['types'][$type]['showitem'] = ($processedTca['types'][$type]['showitem'] ?? '') . ',' . $typeInfo['showitem'];
            }
        }

        // sys_dmail_group has no initial palettes
        $GLOBALS['TCA']['sys_dmail_group']['palettes'] = $prefixedFields['palettes'];
        $processedTca['palettes'] = $prefixedFields['palettes'];

        if (!empty($prefixedFields['ctrl']['requestUpdate'])) {
            $GLOBALS['TCA']['sys_dmail_group']['ctrl']['requestUpdate'] .= ',' . $prefixedFields['ctrl']['requestUpdate'];
            $processedTca['ctrl']['requestUpdate'] = ($processedTca['ctrl']['requestUpdate'] ?? '') . ',' . $prefixedFields['ctrl']['requestUpdate'];
        }

        if (!$populateProcessedTca) {
            // Prevent side effects if we shouldn't modify the processedTca at this point
            $processedTca = [];
        }
    }

    /**
     * Resets the TCA so that it does not contain any virtual column anymore.
     *
     * This is needed to prevent virtual columns to be queried by
     * \TYPO3\CMS\Core\Database\ReferenceIndex::updateRefIndexTable()
     * after DataHanlder persisted changes to the database.
     */
    public static function resetTCA()
    {
        foreach ($GLOBALS['TCA']['sys_dmail_group']['columns'] as $field => $_) {
            if (str_starts_with($field, TcaUtility::VIRTUAL_PREFIX)) {
                unset($GLOBALS['TCA']['sys_dmail_group']['columns'][$field]);
            }
        }
    }
}
