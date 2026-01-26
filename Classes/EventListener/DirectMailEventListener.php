<?php

namespace Causal\DirectMailUserfunc\EventListener;

use Causal\DirectMailUserfunc\Utility\ItemsProcFunc;
use DirectMailTeam\DirectMail\Event\DmailCompileMailGroupEvent;
use DirectMailTeam\DirectMail\Event\RecipientListCompileMailGroupEvent;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

class DirectMailEventListener
{
    /**
     * Post-processes the list of recipients.
     *
     * @param RecipientListCompileMailGroupEvent $event
     * @return void
     */
    public function compileRecipientList(
        RecipientListCompileMailGroupEvent|DmailCompileMailGroupEvent $event
    ): void
    {
        $mailGroups = [];

        $groups = $event->getMailGroup();
        $idLists = $event->getIdLists();

        if ($event instanceof DmailCompileMailGroupEvent) {
            foreach ($groups as $group) {
                MathUtility::convertToPositiveInteger($group);
                if (!$group) {
                    continue;
                }

                $mailGroup = BackendUtility::getRecord('sys_dmail_group', $group);
                if (is_array($mailGroup)) {
                    $mailGroups[] = $mailGroup;
                }
            }
        } else {
            $mailGroups[] = $groups;
        }

        foreach ($mailGroups as $mailGroup) {
            if ($mailGroup['type'] === 5) {
                $recipientList = $this->generateRecipientList($mailGroup);
                $idLists = array_merge_recursive($idLists, $recipientList);
            }
        }

        $event->setIdLists($idLists);
    }

    /**
     * Generates the list of recipients.
     *
     * @param array $mailGroup
     * @return array
     */
    protected function generateRecipientList(array $mailGroup): array
    {
        $recipientList = [
            'tt_address' => [],
            'fe_users' => [],
            'PLAINLIST' => [],
        ];
        $itemsProcFunc = $mailGroup['tx_directmailuserfunc_itemsprocfunc'];
        if ($itemsProcFunc !== null && ItemsProcFunc::isMethodValid($itemsProcFunc)) {
            $userParams = $mailGroup['tx_directmailuserfunc_params'];
            if (ItemsProcFunc::hasWizardFields($itemsProcFunc)) {
                $fields = ItemsProcFunc::callWizardFields($itemsProcFunc);
                if ($fields !== null) {
                    $userParams = count($fields) === 0
                        ? []
                        : ItemsProcFunc::decodeUserParameters($mailGroup);
                }
            }

            $params = [
                'groupUid' => $mailGroup['uid'],
                'lists' => &$recipientList,
                'userParams' => $userParams,
            ];
            GeneralUtility::callUserFunction($itemsProcFunc, $params);

            $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('direct_mail_userfunc');
            if ((bool)$extConf['makeEntriesUnique']) {
                // Make unique entries
                $recipientList['tt_address'] = array_unique($recipientList['tt_address']);
                $recipientList['fe_users'] = array_unique($recipientList['fe_users']);
                $recipientList['PLAINLIST'] = $this->cleanPlainList($recipientList['PLAINLIST']);
            }
        }

        return $recipientList;
    }

    /**
     * Removes double record in an array.
     *
     * $plainlist is a multidimensional array.
     *
     * This method only remove if a value has the same array
     * $plainlist = [
     *     0 => [
     *         name => '',
     *         email => '',
     *     ],
     *     1 => [
     *         name => '',
     *         email => '',
     *     ],
     * ];
     *
     * @param array $plainlist Email addresses of the recipients
     * @return array Cleaned array
     */
    protected function cleanPlainList(array $plainlist): array
    {
        $plainlist = array_map('unserialize', array_unique(array_map('serialize', $plainlist)));

        return $plainlist;
    }
}
