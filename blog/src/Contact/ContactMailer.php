<?php

declare(strict_types=1);

namespace App\Contact;

use Exception;
use Psr\Log\LoggerInterface;
use Yiisoft\Mailer\File;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Mailer\MessageBodyTemplate;
use Yiisoft\Session\Flash\FlashInterface;

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
        $this->mailer = $this->mailer->withTemplate(new MessageBodyTemplate(__DIR__ . '/mail/'));
    }

    public function send(ContactForm $form): void
    {
        $message = $this->mailer
            ->compose(
                'contact-email',
                [
                    'content' => $form->getPropertyValue('body'),
                ]
            )
            ->withSubject($form->getPropertyValue('subject'))
            ->withFrom([$form->getPropertyValue('email') => $form->getPropertyValue('name')])
            ->withSender($this->sender)
            ->withTo($this->to);

        foreach ($form->getPropertyValue('attachFiles') as $attachFile) {
            foreach ($attachFile as $file) {
                if ($file[0]?->getError() === UPLOAD_ERR_OK) {
                    $message = $message->withAttached(
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
