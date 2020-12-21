<?php

declare(strict_types=1);

namespace App\Contact;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Form\FormModelInterface;
use Yiisoft\Mailer\MailerInterface;

/**
 * ContactMailer sends an email from the contact form
 */
class ContactMailer
{
    private MailerInterface $mailer;
    private string $to;

    public function __construct(MailerInterface $mailer, string $to)
    {
        $this->mailer = $mailer;
        $this->to = $to;
    }

    public function send(FormModelinterface $form, ServerRequestInterface $request)
    {
        $message = $this->mailer->compose(
            'contact',
            [
                'name' => $form->getAttributeValue('name'),
                'content' => $form->getAttributeValue('body'),
            ]
        )
            ->setSubject($form->getAttributeValue('subject'))
            ->setFrom($form->getAttributeValue('email'))
            ->setTo($this->to);

        $attachFiles = $request->getUploadedFiles();
        foreach ($attachFiles as $attachFile) {
            foreach ($attachFile as $file) {
                if ($file->getError() === UPLOAD_ERR_OK) {
                    $message->attachContent(
                        (string) $file->getStream(),
                        [
                            'fileName' => $file->getClientFilename(),
                            'contentType' => $file->getClientMediaType(),
                        ]
                    );
                }
            }
        }

        $message->send();
    }
}
