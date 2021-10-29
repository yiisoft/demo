<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\GridView;
use Yiisoft\Form\Widget\Field;
use Yiisoft\Form\Widget\Select;
use Yiisoft\Form\Widget\Form;

return [
    \Yiisoft\Factory\Factory::class => [
        '__construct()' => [
            'definitions' => [
                Select::class => function () {
                    echo 'hello, factory';
                    die();
                },
            ],
        ],
    ],
    GridView::class => static fn () => GridView::widget([
        'layout()' => [
            Html::div('{items}{pager}', ['class' => 'table-responsive'])->render(),
        ],
        'options()' => [
            [
                'class' => 'border bg-white shadow-sm p-3',
            ],
        ],
        'tableOptions()' => [
            [
                'class' => 'table table-sm table-striped table-hover mb-0',
            ],
        ],
    ]),

    Form::class => static fn () => Form::widget()->attributes([
        'class' => 'container-fluid bg-white border shadow-sm py-3',
        'style' => [
            'border' => '1px solid #000',
            'padding' => '100px',
        ],
    ]),

    Field::class => [
        'template()' => [
            '{label}<div class="col-sm-10">{input}{error}</div>',
        ],
        'containerClass()' => [
            'row mb-3',
        ],
        'inputClass()' => [
            'form-control',
        ],
        'labelClass()' => [
            'col-sm-2 col-form-label text-start text-lg-end',
        ],
    ],

    Select::class => function () {
        die();
    },
];
