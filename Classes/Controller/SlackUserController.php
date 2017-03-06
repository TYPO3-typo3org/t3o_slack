<?php
namespace T3o\T3oSlack\Controller;

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use T3o\T3oSlack\Domain\Model\SlackUser;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package t3o_slack
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class SlackUserController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    public function initializeAction()
    {

        if (!$GLOBALS['TSFE']->fe_user->user['uid']) {
            $this->redirect('noAccess');
        }

        // configuration check
        if (!$this->settings['Slack']['TeamUrl'] or !$this->settings['Slack']['token']) {
            $this->redirect('noAccess');
        }
    }

    /**
     * action new
     *
     * @param \T3o\T3oSlack\Domain\Model\SlackUser $newSlackUser
     * @dontvalidate $newSlackUser
     * @return void
     */
    public function newAction(SlackUser $newSlackUser = null)
    {

        if ($newSlackUser == null) {
            /** @var  \T3o\T3oSlack\Domain\Model\SlackUser $newSlackUser */
            $newSlackUser = $this->objectManager->get('\T3o\T3oSlack\Domain\Model\SlackUser');

            // initialize user with fe_user data if there is no object
            $newSlackUser->setFirstname($GLOBALS['TSFE']->fe_user->user['first_name']);
            $newSlackUser->setLastname($GLOBALS['TSFE']->fe_user->user['last_name']);
            $newSlackUser->setEmail($GLOBALS['TSFE']->fe_user->user['email']);
        }

        $this->view->assign('newSlackUser', $newSlackUser);
    }

    /**
     * action new
     *
     * @param \T3o\T3oSlack\Domain\Model\SlackUser $newSlackUser
     * @dontvalidate $newSlackUser
     * @return void
     */
    public function noAccessAction(SlackUser $newSlackUser = null)
    {

    }

    /**
     * action create
     *
     * @param \T3o\T3oSlack\Domain\Model\SlackUser $newSlackUser
     * @return void
     */
    public function createAction(SlackUser $newSlackUser)
    {


        // Initialize options for REST interface
        $adb_url = $this->settings['Slack']['TeamUrl'];
        $adb_option_defaults = array(
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 2
        );

        $adb_handle = curl_init();

        $options = array(
            CURLOPT_URL           => $adb_url . '/api/users.admin.invite',
            CURLOPT_CUSTOMREQUEST => 'POST', // GET POST PUT PATCH DELETE HEAD OPTIONS
            CURLOPT_POSTFIELDS    => 'email=' . urlencode($newSlackUser->getEmail()) . '&first_name=' . urlencode($newSlackUser->getFirstname()) . '&last_name=' . urlencode($newSlackUser->getLastname()) . '&token=' . $this->settings['Slack']['token'] . '&set_active=true'
        );
        curl_setopt_array($adb_handle, ($options + $adb_option_defaults));

        // send request and wait for responce
        $responce = json_decode(curl_exec($adb_handle), true);

        switch ($responce['error']) {
            case 'already_in_team':
                $this->addFlashMessage(LocalizationUtility::translate('tx_t3oslack.existingAccount', $this->extensionName) . $newSlackUser->getEmail(), '', AbstractMessage::WARNING);
                break;
            case 'missing_scope':
                $this->addFlashMessage(LocalizationUtility::translate('tx_t3oslack.apiKeyMissing', $this->extensionName), '', AbstractMessage::ERROR);
                break;
            default:
                if ($responce['error']) {
                    $message = ' Error code: ' . $responce['error'];
                }
                $this->addFlashMessage(LocalizationUtility::translate('tx_t3oslack.noConnection', $this->extensionName) . $message, '', AbstractMessage::ERROR);
                break;
        }

        if ($responce['ok'] == 1) {
            $this->addFlashMessage(LocalizationUtility::translate('tx_t3oslack.userCreated', $this->extensionName));
        }
    }

}

?>
