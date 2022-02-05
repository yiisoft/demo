<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\Data\Paginator\OffsetPaginator $paginator;
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 * @var int $currentPage
 */

use App\Widget\OffsetPagination;
use Yiisoft\Html\Html;
use Yiisoft\Yii\DataView\GridView;

$this->setTitle($translator->translate('menu.users'));
?>

<div class="container">
    <div class="text-end">
        <?= Html::a('API v1 Info', $urlGenerator->generate('api/info/v1'), ['class' => 'btn btn-link']) ?>
        <?= Html::a('API v2 Info', $urlGenerator->generate('api/info/v2'), ['class' => 'btn btn-link']) ?>
        <?= Html::a('API Users List Data', $urlGenerator->generate('api/user/index'), ['class' => 'btn btn-link'])?>
    </div>

    <div class="card shadow">
        <h5 class="card-header bg-primary text-white">
            <i class="bi bi-people"></i> List users
        </h5>
        <?= GridView::widget()->columns(
            [
                [
                    'attribute()' => ['id'],
                    'value()' => [static function ($model): string {
                        return $model->getId();
                    }],
                ],
                [
                    'attribute()' => ['login'],
                    'value()' => [static fn ($model): string => $model->getLogin()],
                ],
                [
                    'header()' => ['create_at'],
                    'value()' => [static fn ($model): string => $model->getCreatedAt()->format('r')],
                ],
                [
                    'header()' => ['api'],
                    'value()' => [
                        static function ($model) use ($urlGenerator): string {
                            return Html::a(
                                'API User Data',
                                $urlGenerator->generate('api/user/profile', ['login' => $model->getLogin()]),
                                ['class' => 'btn btn-link', 'target' => '_blank'],
                            )->render();
                        },
                    ],
                ],
                [
                    'header()' => ['profile'],
                    'value()' => [
                        static function ($model) use ($urlGenerator): string {
                            return Html::a(
                                Html::tag('i', '', [
                                    'class' => 'bi bi-person-fill ms-1',
                                    'style' => 'font-size: 1.5em;',
                                ]),
                                $urlGenerator->generate('user/profile', ['login' => $model->getLogin()]),
                                ['class' => 'btn btn-link'],
                            )->render();
                        },
                    ],
                ],
            ]
        )
        ->currentPage($page)
        ->pageArgument(true)
        ->paginator($paginator)
        ->requestArguments(['sort' => $sortOrder, 'page' => $page])
        ->rowOptions(['class' => 'align-middle'])
        ->summaryOptions(['class' => 'mt-3 me-3 summary text-end'])
        ->tableOptions(['class' => 'table table-striped text-center h-75']) ?>
    </div>
</div>
