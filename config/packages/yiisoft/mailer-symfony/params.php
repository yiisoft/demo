<?php

declare(strict_types=1);

return [
    'yiisoft/mailer' => [
        'messageBodyTemplate' => [
            'viewPath' => '@resources/mail',
        ],
        'fileMailer' => [
            'fileMailerStorage' => '@runtime/mail',
        ],
        'useSendmail' => false,
        'writeToFiles' => true,
    ],
    'symfony/mailer' => [
        'esmtpTransport' => [
            'host' => 'smtp.example.com',
            'port' => 465,
            'tls' => true,
            'username' => 'admin@example.com',
            'password' => '',
        ],
    ],
];
