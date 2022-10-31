<?php

namespace T3\Mbox\Controller;

use Armin\MboxParser\Parser;
use Armin\MboxParser\Result;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class MboxModuleController extends ActionController
{
    /**
     * @var Result|null
     */
    private $mailbox = null;


    public function initializeAction()
    {
        $mboxParser = new Parser();
        $this->mailbox = $mboxParser->parse($GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file']);
    }


    public function indexAction()
    {
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
