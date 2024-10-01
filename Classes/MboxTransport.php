<?php

namespace T3\Mbox;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Locking\Exception\LockAcquireException;
use TYPO3\CMS\Core\Locking\Exception\LockAcquireWouldBlockException;
use TYPO3\CMS\Core\Locking\Exception\LockCreateException;
use TYPO3\CMS\Core\Locking\LockFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MboxTransport extends AbstractTransport
{
    private array $mailSettings;

    public function __construct(
        array $mailSettings,
        ?EventDispatcherInterface $dispatcher = null,
        ?LoggerInterface $logger = null
    ) {
        parent::__construct($dispatcher, $logger);
        $this->setMaxPerSecond(0);
        $this->mailSettings = $mailSettings;
    }

    /**
     * Outputs the mail to a text file according to RFC 4155, really!
     *
     * The original MboxTransport just saves the RFC 2822 (mail message) to the mbox file, without respecting the
     * need of RFC 4155 to always start a new message with "From ". This XClass solves it.
     *
     * @throws LockAcquireException
     * @throws LockAcquireWouldBlockException
     * @throws LockCreateException
     */
    protected function doSend(SentMessage $message): void
    {
        // Add the complete mail inclusive headers
        $lockFactory = GeneralUtility::makeInstance(LockFactory::class);
        $lockObject = $lockFactory->createLocker('mbox');
        $lockObject->acquire();
        // Write the mbox file
        $file = @fopen($this->mailSettings['transport_mbox_file'], 'a');
        if (!$file) {
            $lockObject->release();
            throw new \RuntimeException(sprintf('Could not write to file "%s" when sending an email to debug transport', $this->mailSettings['transport_mbox_file']), 1291064151);
        }

        // Creating "From " line start
        $emailAsString = $message->toString();

        $from = $message->getOriginalMessage()->getFrom();
        $mboxFromLine = sprintf("From %s %s\n", reset($from)->getAddress(), date('D M j H:i:s Y'));
        $mboxEmail = $mboxFromLine . trim($emailAsString) . PHP_EOL . PHP_EOL;
        // Creating "From " line end

        @fwrite($file, $mboxEmail);
        @fclose($file);
        GeneralUtility::fixPermissions($this->mailSettings['transport_mbox_file']);
        $lockObject->release();
    }

    public function __toString(): string
    {
        return $this->mailSettings['transport_mbox_file'];
    }
}
