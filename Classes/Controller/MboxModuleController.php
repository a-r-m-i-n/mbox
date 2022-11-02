<?php

namespace T3\Mbox\Controller;

use Armin\MboxParser\Mailbox;
use Armin\MboxParser\Parser;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\Http\Response;
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


    public function downloadEmlAction(string $messageId)
    {
        $mail = $this->mailbox->getMessageById($messageId);
        if (!$mail) {
            throw new \InvalidArgumentException('No message with ID "' . $messageId . '" found!');
        }

        $response = new Response();
        $response->getBody()->write($mail->getMessageSource());
        $response = $response->withHeader('Content-Type', 'application/octet-stream');
        $response = $response->withHeader('Content-Disposition', 'attachment;filename=' . $mail->getMessageId() . '.eml');

        throw new ImmediateResponseException($response);
    }


    public function downloadAttachmentAction(string $messageId, string $fileName)
    {
        $mail = $this->mailbox->getMessageById($messageId);
        if (!$mail) {
            throw new \InvalidArgumentException('No message with ID "' . $messageId . '" found!');
        }

        foreach ($mail->getAttachments() as $attachment) {
            if ($attachment->getFilename() === $fileName) {

                $response = new Response();
                $response->getBody()->write($attachment->getContent());
                $response = $response->withHeader('Content-Type', $attachment->getContentMimeType());
                $response = $response->withHeader('Content-Disposition', 'attachment;filename=' . $attachment->getFilename());

                throw new ImmediateResponseException($response);
            }
        }

        throw new \RuntimeException(sprintf('No attachment with filename "%s" found in mail message with id "%s"', $fileName, $messageId));
    }

    public function clearMboxAction(): void
    {
        file_put_contents($GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file'], '');

        $this->redirect('index');
    }
}
