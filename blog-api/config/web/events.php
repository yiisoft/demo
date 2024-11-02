<?php

declare(strict_types=1);

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\Middleware\Event\SetLocaleEvent;

return [
    SetLocaleEvent::class => [
        static fn(TranslatorInterface $translator, SetLocaleEvent $event) => $translator->setLocale($event->getLocale()),
    ],
];
