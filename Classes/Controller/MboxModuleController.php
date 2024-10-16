<?php

namespace T3\Mbox\Controller;

use Armin\MboxParser\Parser;
use Psr\Http\Message\ResponseInterface;
use T3\Mbox\MboxTransport;
use T3\Mbox\Pagination\MboxPaginator;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class MboxModuleController extends ActionController
{
    private ModuleTemplateFactory $moduleTemplateFactory;
    private ModuleTemplate $moduleTemplate;

    public function __construct(ModuleTemplateFactory $moduleTemplateFactory)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    public function initializeAction(): void
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setTitle('EXT:mbox');
        $this->moduleTemplate->getDocHeaderComponent()->disable();
    }

    public function indexAction(int $currentPage = 1): ResponseInterface
    {
        // Get settings
        /** @var array<string, mixed> $extensionConfiguration */
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('mbox');
        $mailsPerPage = !empty($extensionConfiguration['mailsPerPage']) ? ((int) $extensionConfiguration['mailsPerPage']) : 10;
        $this->moduleTemplate->assign('transportIsMbox', $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport'] === MboxTransport::class);
        $this->moduleTemplate->assign('transport', $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport']);
        $this->moduleTemplate->assign('mboxPath', $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file']);
        $this->moduleTemplate->assign('mboxExists', file_exists($GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file']));

        // Parse mbox
        $total = 0;
        if (isset($GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file']) &&
            file_exists($GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file']))
        {
            $mboxParser = new Parser();
            $total = $mboxParser->getTotalEntries($GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file']);
        }

        if ($total) {
            // Pagination
            /** @var MboxPaginator $paginator */
            $paginator = GeneralUtility::makeInstance(MboxPaginator::class, $GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file'], $currentPage, $mailsPerPage, $total);
            /** @var SimplePagination $pagination */
            $pagination = GeneralUtility::makeInstance(SimplePagination::class, $paginator);

            $this->moduleTemplate->assign('total', $total);
            $this->moduleTemplate->assign('paginator', $paginator);
            $this->moduleTemplate->assign('pagination', $pagination);
        }

        return $this->moduleTemplate->renderResponse('MboxModule/Index');
    }

    public function showAction(string $messageId): ResponseInterface
    {
        $parser = new Parser();
        $mail = $parser->getMessageById($GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file'], $messageId);
        if (!$mail) {
            throw new \InvalidArgumentException('No message with ID "' . $messageId . '" found!');
        }

        $this->moduleTemplate->assign('mail', $mail);

        return $this->moduleTemplate->renderResponse('MboxModule/Show');
    }

    public function downloadEmlAction(string $messageId): ResponseInterface
    {
        $parser = new Parser();
        $mail = $parser->getMessageById($GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file'], $messageId);
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
        $parser = new Parser();
        $mail = $parser->getMessageById($GLOBALS['TYPO3_CONF_VARS']['MAIL']['transport_mbox_file'], $messageId);
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
