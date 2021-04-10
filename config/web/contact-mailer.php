<?php

declare(strict_types=1);

use App\Contact\ContactMailer;

/** @var array $params */

return [
    ContactMailer::class => [
        'class' => ContactMailer::class,
        'constructor' => [
            'to' => $params['mailer']['adminEmail'],
        ],
    ],
];
