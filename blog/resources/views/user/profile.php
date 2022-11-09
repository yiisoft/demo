<?php

declare(strict_types=1);

/**
 * @var User    $item
 * @var WebView $this
 */

use App\User\User;
use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\H2;
use Yiisoft\View\WebView;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\Field\DataField;

$this->setTitle('Profile');

$title = Html::encode($this->getTitle());
?>

<?= DetailView::widget()
    ->attributes(['class' => 'container'])
    ->containerAttributes(['class' => 'row flex-column justify-content-center align-items-center'])
    ->data($item)
    ->dataAttributes(['class' => 'col-xl-5'])
    ->fields(
        DataField::create()
            ->attribute('id')
            ->label('ID')
            ->value($item->getId()),
        DataField::create()
            ->attribute('login')
            ->label($translator->translate('gridview.login'))
            ->value($item->getLogin()),
        DataField::create()
            ->attribute('create_at')
            ->label($translator->translate('gridview.create.at'))
            ->value($item->getCreatedAt()->format('H:i:s d.m.Y')),
    )
    ->header(H2::tag()->class('text-center')->content("<strong>$title</strong>")->encode(false)->render())
    ->labelAttributes(['class' => 'fw-bold'])
    ->valueAttributes(['class' => 'alert alert-info']);
