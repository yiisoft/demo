<?php

declare(strict_types=1);

return [
    'yiisoft/form' => [
        'bootstrap5' => [
            'enabled' => true,
            'fieldConfig' => [
                'enclosedByContainer()' => [true, ['class' => 'mb-3']],
                'errorCssClass()' => ['is-invalid'],
                'errorOptions()' => [['class' => 'text-danger fst-italic']],
                'hintOptions()' => [['class' => 'form-text']],
                'inputCssClass()' => ['form-control'],
                'labelOptions()' => [['class' => 'form-label']],
                'successCssClass()' => ['is-valid'],
            ],
        ],
    ],
];
