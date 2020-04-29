<?php


namespace App\Contact;

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

    public function send(Message $contactMessage)
    {
        $message = $this->mailer->compose(
            'contact',
            [
                'name' => $contactMessage->getName(),
                'content' => $contactMessage->getContent(),
            ]
        )
            ->setSubject($contactMessage->getSubject())
            ->setFrom($contactMessage->getEmail())
            ->setTo($this->to);

        $files = $contactMessage->getFiles();
        foreach ($files as $file) {
            $message->attachContent(
                (string)$file->getStream(),
                [
                    'fileName' => $file->getClientFilename(),
                    'contentType' => $file->getClientMediaType(),
                ]
            );
        }

        $message->send();
    }
}
