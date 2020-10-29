<?php

declare(strict_types=1);

use Yiisoft\Form\Widget\Field;

/** @var array $params */

return [
    Field::class => static fn () => Field::Widget($params['yiisoft/form']['fieldConfig'])
];
