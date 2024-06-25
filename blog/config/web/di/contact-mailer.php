<?php

declare(strict_types=1);

use App\Contact\ContactMailer;

/** 
 * @var array $params
 * @var array $params['mailer']
 * @var string $params['mailer']['senderEmail']
 * @var string $params['mailer']['adminEmail']  
 */

return [
    ContactMailer::class => [
        'class' => ContactMailer::class,
        '__construct()' => [
            'sender' => $params['mailer']['senderEmail'],
            'to' => $params['mailer']['adminEmail'],
        ],
    ],
];
