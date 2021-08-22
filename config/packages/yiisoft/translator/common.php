<?php

declare(strict_types=1);

use Psr\EventDispatcher\EventDispatcherInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Factory\Definition\Reference;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Translator\Translator;
use Yiisoft\Translator\TranslatorInterface;

/** @var array $params */

return [
    // Configure application CategorySource
    'translation.app' => static function (Aliases $aliases, MessageFormatterInterface $messageFormatter) use ($params) {
        $messageSource = new MessageSource($aliases->get('@messages'));

        return new CategorySource(
            $params['yiisoft/translator']['defaultCategory'],
            $messageSource,
            $messageFormatter,
        );
    },

    TranslatorInterface::class => [
        'class' => Translator::class,
        '__construct()' => [
            $params['yiisoft/translator']['locale'],
            $params['yiisoft/translator']['fallbackLocale'],
            Reference::to(EventDispatcherInterface::class),
        ],
        'addCategorySources()' => [
            $params['yiisoft/translator']['categorySources'],
        ],
        'reset' => function () use ($params) {
            $this->setLocale($params['yiisoft/translator']['locale']);
        },
    ],
];
