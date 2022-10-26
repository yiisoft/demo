<?php

declare(strict_types=1);

/**
 * @var \App\User\User $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\H2;
use Yiisoft\Yii\DataView\Column\DetailView\DataColumn;
use Yiisoft\Yii\DataView\DetailView;

$this->setTitle('Profile');

$title = Html::encode($this->getTitle());
?>

<?= DetailView::widget()
    ->attributes(['class' => 'container'])
    ->columns(
        DataColumn::create()
            ->attribute('id')
            ->label('ID')
            ->value($item->getId()),
        DataColumn::create()
            ->attribute('login')
            ->label($translator->translate('gridview.login'))
            ->value($item->getLogin()),
        DataColumn::create()
            ->attribute('create_at')
            ->label($translator->translate('gridview.create.at'))
            ->value($item->getCreatedAt()->format('H:i:s d.m.Y')),
    )
    ->containerAttributes(['class' => 'row flex-column justify-content-center align-items-center'])
    ->data($item)
    ->dataAttributes(['class' => 'col-xl-5'])
    ->header(H2::tag()->class('text-center')->content("<strong>$title</strong>")->encode(false)->render())
    ->labelAttributes(['class' => 'fw-bold'])
    ->valueAttributes(['class' => 'alert alert-info']);
