<?php

namespace T3\Mbox\ViewHelpers;

use Armin\MboxParser\MailMessage;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ParseHtmlViewHelper extends AbstractViewHelper
{
    protected $escapeOutput = false;
    protected $escapeChildren = false;

    public function initializeArguments()
    {
        $this->registerArgument('mail', 'object', 'The mail message instance', true);
    }

    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string {
        if (empty($arguments['mail']) || !$arguments['mail'] instanceof MailMessage) {
            throw new \InvalidArgumentException(
                'ParseHtmlViewHelper of EXT:mbox requires argument "mail" to be an instance of MailMessage!'
            );
        }

        $html = $renderChildrenClosure();
        $attachments = $arguments['mail']->getAttachments();

        // Find all inline images in HTMLL
        preg_match_all('/[\'"]cid:(.*?)[\'"]/', $html, $matches);
        foreach ($matches[1] as $contentId) {
            foreach ($attachments as $attachment) {
                if ($attachment->getContentId() && $attachment->getContentId() === $contentId) {
                    // Replace contentId in HTML with actual image data (base64 encoded)
                    $html = str_replace('cid:' . $contentId, 'data:' . $attachment->getContentMimeType() . ';base64, ' . base64_encode($attachment->getContent()), $html);
                    break;
                }
            }
        }

        return $html;
    }
}
