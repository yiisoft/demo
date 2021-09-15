<?php

declare(strict_types=1);

use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Translator\Formatter\Intl\IntlMessageFormatter;

return [
    MessageFormatterInterface::class => IntlMessageFormatter::class,
];
