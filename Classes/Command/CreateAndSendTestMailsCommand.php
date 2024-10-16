<?php

namespace T3\Mbox\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CreateAndSendTestMailsCommand extends Command
{
    protected function configure(): void
    {
        $this->addOption('loops', 'l', InputOption::VALUE_OPTIONAL, 'Amount of loops', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Create and send test mails');

        $loops = (int)$input->getOption('loops');
        if ($loops < 1) {
            $loops = 1;
        }

        $io->progressStart(6 * $loops);
        $mailNo = 0;
        for ($loop = 0; $loop < $loops; $loop++) {
            $simpleMail = GeneralUtility::makeInstance(MailMessage::class);
            $simpleMail
                ->from('sender@domain.com')
                ->to('recipient1@domain.com', 'recipient2@domain.com')
                ->cc('cc@domain.com')
                ->bcc('bcc@domain.com')
                ->replyTo('replyTo@domain.com')
                ->subject('Test Mail #' . ++$mailNo)
                ->text('This is the text body of the mail')
                ->html('This is the <strong>HTML body</strong> of the mail')
                ->send()
            ;
            $io->progressAdvance();


            $mailWithAttachment = GeneralUtility::makeInstance(MailMessage::class);
            $mailWithAttachment
                ->priority(Email::PRIORITY_HIGHEST)
                ->from('sender@domain.com')
                ->to(new Address('recipient@domain.com', 'Robert Recipient'))
                ->subject('Test Mail #' . ++$mailNo)
                ->text('This is the text body of the mail, which also contains an attachment')
                ->html('This is the <strong>HTML body</strong> of the mail, whcih also contains an attachment')
                ->attachFromPath(GeneralUtility::getFileAbsFileName('EXT:mbox/Resources/Public/Icons/Extension.svg'), 'Extension.svg', 'text/svg')
                ->attachFromPath(GeneralUtility::getFileAbsFileName('EXT:mbox/README.md'), 'README.md', 'text/markdown')
                ->send()
            ;

            $io->progressAdvance();


            $simpleMail = GeneralUtility::makeInstance(MailMessage::class);
            $simpleMail
                ->from('sender@domain.com')
                ->to(new Address('recipient@domain.com', 'Robert Recipient'), 'recipient1@domain.com', 'recipient2@domain.com')
                ->cc('cc@domain.com')
                ->bcc('bcc@domain.com')
                ->replyTo('replyTo@domain.com')
                ->subject('Test Mail #' . ++$mailNo)
                ->text('This is the text body of the mail')
                ->html('This is the <strong>HTML body</strong> of the mail')
                ->send()
            ;
            $io->progressAdvance();


            $simpleMailPlainTextOnly = GeneralUtility::makeInstance(MailMessage::class);
            $simpleMailPlainTextOnly
                ->from('sender@domain.com')
                ->to(new Address('recipient@domain.com', 'Robert Recipient'), 'recipient1@domain.com', 'recipient2@domain.com')
                ->cc('cc@domain.com')
                ->bcc('bcc@domain.com')
                ->replyTo('replyTo@domain.com')
                ->subject('Test Mail #' .  ++$mailNo . ' (Plaintext only)')
                ->text('This is the text body of the mail')
                ->send()
            ;
            $io->progressAdvance();


            $simpleMailHtmlOnly = GeneralUtility::makeInstance(MailMessage::class);
            $simpleMailHtmlOnly
                ->from('sender@domain.com')
                ->to(new Address('recipient@domain.com', 'Robert Recipient'), 'recipient1@domain.com', 'recipient2@domain.com')
                ->cc('cc@domain.com')
                ->bcc('bcc@domain.com')
                ->replyTo('replyTo@domain.com')
                ->subject('Test Mail #' . ++$mailNo . ' (HTML only)')
                ->html('This is the <strong>HTML body</strong> of the mail')
                ->send()
            ;
            $io->progressAdvance();


            $mailWithBinaryAttachment = GeneralUtility::makeInstance(MailMessage::class);
            $mailWithBinaryAttachment
                ->priority(Email::PRIORITY_HIGHEST)
                ->from('sender@domain.com')
                ->to(new Address('recipient@domain.com', 'Robert Recipient'))
                ->subject('Test Mail #' . ++$mailNo)
                ->text('This is the text body of the mail, which also contains an binary attachment')
                ->embedFromPath(GeneralUtility::getFileAbsFileName('EXT:mbox/Documentation/Screenshots/mbox-inbox.png'), 'inline-test-image')
                ->html('This is the <strong>HTML body</strong> of the mail, which also contains an binary attachment and an inline image: <img src="cid:inline-test-image" alt="Inline Image" />')
                ->attachFromPath(GeneralUtility::getFileAbsFileName('EXT:mbox/Documentation/Screenshots/mbox-detail-html-view.png'), 'mbox-detail-html-view.png', 'image/png')
                ->attachFromPath(GeneralUtility::getFileAbsFileName('EXT:mbox/Documentation/Screenshots/mbox-detail-with-attachments.png'), 'mbox-detail-with-attachments.png', 'image/png')
                ->send()
            ;

            $io->progressAdvance();
        }

        $io->progressFinish();

        return Command::SUCCESS;
    }
}
