<?php

declare(strict_types=1);

use App\Timer;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Yii\Http\Event\ApplicationStartup;
use Yiisoft\Yii\Middleware\Event\SetLocaleEvent;

return [
    ApplicationStartup::class => [
        static fn(Timer $timer) => $timer->start('overall'),
    ],
    SetLocaleEvent::class => [
        static fn(TranslatorInterface $translator, SetLocaleEvent $event) => $translator->setLocale($event->getLocale()),
    ],
];
