<?php

declare(strict_types=1);

namespace App\Contact;

use Exception;
use Psr\Log\LoggerInterface;
use Yiisoft\Mailer\File;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Mailer\Message;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\View\View;

/**
 * ContactMailer sends an email from the contact form.
 */
final class ContactMailer
{
    public function __construct(
        private FlashInterface $flash,
        private LoggerInterface $logger,
        private MailerInterface $mailer,
        private string $sender,
        private string $to
    ) {
    }

    public function send(ContactForm $form): void
    {
        $message = new Message(
            from: [$form->getPropertyValue('email') => $form->getPropertyValue('name')],
            to: $this->to,
            subject: $form->getPropertyValue('subject'),
            sender: $this->sender,
            htmlBody: (new View())->render(__DIR__ . '/mail/contact-email.php', [
                'content' => $form->getPropertyValue('body'),
            ])
        );

        foreach ($form->getPropertyValue('attachFiles') as $attachFile) {
            foreach ($attachFile as $file) {
                if ($file[0]?->getError() === UPLOAD_ERR_OK) {
                    $message = $message->withAddedAttachments(
                        File::fromContent(
                            (string) $file->getStream(),
                            $file->getClientFilename(),
                            $file->getClientMediaType()
                        ),
                    );
                }
            }
        }

        try {
            $this->mailer->send($message);
            $flashMsg = 'Thank you for contacting us, we\'ll get in touch with you as soon as possible.';
        } catch (Exception $e) {
            $flashMsg = $e->getMessage();
            $this->logger->error($flashMsg);
        } finally {
            $this->flash->add(
                isset($e) ? 'danger' : 'success',
                [
                    'body' => $flashMsg,
                ],
                true
            );
        }
    }
}
