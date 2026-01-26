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

namespace Causal\DirectMailUserfunc\Utility;

/**
 * Various ItemsProcFunc utility methods.
 *
 * @category    Utility
 * @author      Xavier Perseguers <xavier@causal.ch>
 * @copyright   2013-2026 Causal Sàrl
 * @license     https://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class ItemsProcFunc
{

    /**
     * Returns true if $itemsProcFunc has a method getWizard().
     *
     * @param string $itemsProcFunc
     * @return bool
     */
    public static function hasWizard(string $itemsProcFunc): bool
    {
        list($className,) = explode('->', $itemsProcFunc);
        $ret = false;
        if (!empty($itemsProcFunc) && static::isMethodValid($itemsProcFunc)) {
            $ret = method_exists($className, 'getWizard');
        }
        return $ret;
    }

    /**
     * Invokes method getWizard() from $itemsProcFunc.
     *
     * @param string $itemsProcFunc
     * @param array $PA
     * @param array $row
     * @param bool $checkOnly
     * @return string|null
     * @throws \RuntimeException
     */
    public static function callWizard(string $itemsProcFunc, array &$PA, array $row, bool $checkOnly = false): ?string
    {
        if (!static::hasWizard($itemsProcFunc)) {
            throw new \RuntimeException($itemsProcFunc . ' has no method getWizard', 1383559688);
        }

        list($className, $methodName) = explode('->', $itemsProcFunc);
        $wizardJS = trim(call_user_func_array(
            [$className, 'getWizard'],
            [$methodName, &$PA, $row, $checkOnly]
        ));
        return $wizardJS;
    }

    /**
     * Returns true if $itemsProcFunc has a method getWizardFields().
     *
     * @param string $itemsProcFunc
     * @return bool
     */
    public static function hasWizardFields(string $itemsProcFunc): bool
    {
        list($className,) = explode('->', $itemsProcFunc);
        $ret = false;
        if (!empty($itemsProcFunc) && static::isMethodValid($itemsProcFunc)) {
            $ret = method_exists($className, 'getWizardFields');
        }
        return $ret;
    }

    /**
     * Invokes method getWizardFields from $itemsProcFunc.
     *
     * @param string $itemsProcFunc
     * @return array|null
     * @throws \RuntimeException
     */
    public static function callWizardFields(string $itemsProcFunc): ?array
    {
        if (!static::hasWizardFields($itemsProcFunc)) {
            throw new \RuntimeException($itemsProcFunc . ' has no method getWizardFields', 1383559998);
        }

        list($className, $methodName) = explode('->', $itemsProcFunc);
        $wizardFields = call_user_func_array(
            [$className, 'getWizardFields'],
            [$methodName]
        );
        return $wizardFields;
    }

    /**
     * Checks whether the class part of a given itemsProcFunc definition is valid.
     *
     * @param string $itemsProcFunc
     * @return bool
     */
    public static function isClassValid(string $itemsProcFunc): bool
    {
        list($className,) = explode('->', $itemsProcFunc);

        if (!empty($className) && class_exists($className)) {
            return true;
        }
        return false;
    }

    /**
     * Checks whether the method part of a given itemsProcFunc definition is valid.
     *
     * @param string $itemsProcFunc
     * @return bool
     * @api
     */
    public static function isMethodValid(string $itemsProcFunc): bool
    {
        if (!static::isClassValid($itemsProcFunc)) {
            return false;
        }

        list($className, $methodName) = explode('->', $itemsProcFunc);

        if (!empty($methodName) && method_exists($className, $methodName)) {
            return true;
        }
        return false;
    }

    /**
     * Encodes user parameters in a database record.
     *
     * @param array $row
     * @param array $values
     */
    public static function encodeUserParameters(array &$row, array $values)
    {
        $row['tx_directmailuserfunc_params'] = json_encode($values);
    }

    /**
     * Decodes user parameters from a database record.
     *
     * @param array $row
     * @return array
     */
    public static function decodeUserParameters(array $row): array
    {
        $values = !empty($row['tx_directmailuserfunc_params'])
            ? json_decode($row['tx_directmailuserfunc_params'], true)
            : [];
        if (!is_array($values)) {
            $values = [];
        }

        return $values;
    }
}
