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
    'swiftmailer/swiftmailer' => [
        'SwiftSmtpTransport' => [
            'host' => 'smtp.example.com',
            'port' => 25,
            'encryption' => null,
            'username' => 'admin@example.com',
            'password' => '',
        ],
    ],
];
