<?php

declare(strict_types=1);

namespace App\Contact;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Form\FormModelInterface;
use Yiisoft\Mailer\File;
use Yiisoft\Mailer\MailerInterface;
use Yiisoft\Session\Flash\FlashInterface;

/**
 * ContactMailer sends an email from the contact form
 */
class ContactMailer
{
    private FlashInterface $flash;
    private LoggerInterface $logger;
    private MailerInterface $mailer;
    private string $to;

    public function __construct(
        FlashInterface $flash,
        LoggerInterface $logger,
        MailerInterface $mailer,
        string $to
    ) {
        $this->flash = $flash;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->to = $to;
    }

    public function send(FormModelInterface $form, ServerRequestInterface $request)
    {
        $message = $this->mailer->compose(
            'contact',
            [
                'content' => $form->getAttributeValue('body'),
            ]
        )
            ->withSubject($form->getAttributeValue('subject'))
            ->withFrom([$form->getAttributeValue('email') => $form->getAttributeValue('name')])
            ->withTo($this->to);

        $attachFiles = $request->getUploadedFiles();
        foreach ($attachFiles as $attachFile) {
            foreach ($attachFile as $file) {
                if ($file->getError() === UPLOAD_ERR_OK) {
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
