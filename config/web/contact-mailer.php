<?php

declare(strict_types=1);

use App\Modules\Mail\MailSender;

/** @var array $params */

return [
    MailSender::class => [
        'class' => MailSender::class,
        '__construct()' => [
            'sender' => $params['mailer']['senderEmail'],
            'to' => $params['mailer']['adminEmail'],
        ],
    ],
];
