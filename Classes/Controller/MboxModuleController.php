<?php

namespace T3\Mbox\Controller;

use Armin\MboxParser\Mailbox;
use Armin\MboxParser\Parser;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class MboxModuleController extends ActionController
{
    /**
     * @var Mailbox|null
     */
    private $mailbox = null;


    public function initializeAction()
    {
        $mboxParser = new Parser();
        $this->mailbox = $mboxParser->parse($GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file']);
    }


    public function indexAction()
    {
        $this->view->assign('transportIsMbox', $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport'] === 'mbox');
        $this->view->assign('transport', $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport']);
        $this->view->assign('mboxPath', $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file']);

        $this->view->assign('mailbox', $this->mailbox);
    }


    public function showAction(string $messageId)
    {
        $mail = $this->mailbox->getMessageById($messageId);
        if (!$mail) {
            throw new \InvalidArgumentException('No message with ID "' . $messageId . '" found!');
        }

        $this->view->assign('mail', $mail);
    }
}
