<?php

declare(strict_types=1);

use Yiisoft\Form\Widget\Field;
use Yiisoft\Form\Widget\Form;

return [
    Field::class => [
        'ariaDescribedBy()' => $params['yiisoft/forms']['field']['ariaDescribedBy'],
        'containerClass()' => $params['yiisoft/forms']['field']['containerClass'],
        'defaultValues()' => $params['yiisoft/forms']['field']['defaultValues'],
        'errorClass()' => $params['yiisoft/forms']['field']['errorClass'],
        'hintClass()' => $params['yiisoft/forms']['field']['hintClass'],
        'inputClass()' => $params['yiisoft/forms']['field']['inputClass'],
        'invalidClass()' => $params['yiisoft/forms']['field']['invalidClass'],
        'labelClass()' => $params['yiisoft/forms']['field']['labelClass'],
        'template()' => $params['yiisoft/forms']['field']['template'],
        'validClass()' => $params['yiisoft/forms']['field']['validClass'],
    ],

    Form::class => [
        'attributes()' => $params['yiisoft/forms']['form']['attributes'],
    ],
];
