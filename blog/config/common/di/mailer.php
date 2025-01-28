<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Mailer\FileMailer;
use Yiisoft\Mailer\MailerInterface;

return [
    MailerInterface::class => [
        'class' => FileMailer::class,
        '__construct()' => [
            'path' => DynamicReference::to(
                static fn(Aliases $aliases) => $aliases->get('@runtime/mail')
            ),
        ],
    ],
];
