<?php

namespace T3\Mbox\Controller;

use Armin\MboxParser\Mailbox;
use Armin\MboxParser\Parser;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3Fluid\Fluid\View\ViewInterface;

class MboxModuleController extends ActionController
{
    private ModuleTemplateFactory $moduleTemplateFactory;
    private ModuleTemplate $moduleTemplate;

    /**
     * @var Mailbox|null
     */
    private $mailbox = null;

    public function __construct(ModuleTemplateFactory $moduleTemplateFactory)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    public function initializeAction()
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setTitle('EXT:mbox');
        $this->moduleTemplate->getDocHeaderComponent()->disable();

        $mboxParser = new Parser();
        $this->mailbox = $mboxParser->parse($GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file']);
    }

    /**
     * v12 returns ModuleTemplate, v11 ViewInterface
     *
     * @return ModuleTemplate|ViewInterface
     */
    private function getViewToUse()
    {
        if (method_exists($this->moduleTemplate, 'assign')) {
            return $this->moduleTemplate;
        }
       return $this->view;
    }

    private function renderViewToUse(): ResponseInterface
    {
        if (!$this->getViewToUse() instanceof ModuleTemplate) {
            // v11
            $this->moduleTemplate->setContent($this->getViewToUse()->render());
            return $this->htmlResponse($this->moduleTemplate->renderContent());
        }

        return $this->htmlResponse($this->getViewToUse()->render());
    }

    public function indexAction(?bool $reverse = null): ResponseInterface
    {
        $this->getViewToUse()->assign('transportIsMbox', $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport'] === 'mbox');
        $this->getViewToUse()->assign('transport', $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport']);
        $this->getViewToUse()->assign('mboxPath', $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file']);

        $this->getViewToUse()->assign('mailbox', $this->mailbox);

        /** @var BackendUserAuthentication $beUser */
        $beUser = $GLOBALS['BE_USER'];
        if (null === $reverse) {
            $reverse = (bool)$beUser->getSessionData('mbox-index-reverse');
        } else {
            $beUser->setSessionData('mbox-index-reverse', $reverse);
        }

        $this->getViewToUse()->assign('reverse', $reverse);

        return $this->renderViewToUse();
    }


    public function showAction(string $messageId): ResponseInterface
    {
        $mail = $this->mailbox->getMessageById($messageId);
        if (!$mail) {
            throw new \InvalidArgumentException('No message with ID "' . $messageId . '" found!');
        }

        $this->getViewToUse()->assign('mail', $mail);

        return $this->renderViewToUse();
    }


    public function downloadEmlAction(string $messageId): ResponseInterface
    {
        $mail = $this->mailbox->getMessageById($messageId);
        if (!$mail) {
            throw new \InvalidArgumentException('No message with ID "' . $messageId . '" found!');
        }

        $response = new Response();
        $response->getBody()->write($mail->getMessageSource());
        $response = $response->withHeader('Content-Type', 'application/octet-stream');
        $response = $response->withHeader('Content-Disposition', 'attachment;filename=' . $mail->getMessageId() . '.eml');

        return $response;
    }


    public function downloadAttachmentAction(string $messageId, string $fileName): ResponseInterface
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

                return $response;
            }
        }

        throw new \RuntimeException(sprintf('No attachment with filename "%s" found in mail message with id "%s"', $fileName, $messageId));
    }


    public function clearMboxAction(): ResponseInterface
    {
        file_put_contents($GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file'], '');

        return $this->redirect('index');
    }
}
