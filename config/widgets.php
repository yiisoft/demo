<?php

declare(strict_types=1);

use Yiisoft\Form\Widget\Field;

/** @var array $params */

return [
    Field::class => [
        'ariaDescribedBy()'  => [],
        'containerClass()' => $params['yiisoft/forms']['containerClass'],
        'errorClass()' => $params['yiisoft/forms']['errorClass'],
        'hintClass()' => $params['yiisoft/forms']['hintClass'],
        'inputClass()' => $params['yiisoft/forms']['inputClass'],
        'invalidClass()' => $params['yiisoft/forms']['invalidClass'],
        'labelClass()' => $params['yiisoft/forms']['labelClass'],
        'template()' => $params['yiisoft/forms']['template'],
        'validClass()' => $params['yiisoft/forms']['validClass'],
    ],
];
