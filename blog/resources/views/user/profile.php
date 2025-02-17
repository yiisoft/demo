<?php

declare(strict_types=1);

/**
 * @var User $item
 * @var WebView $this
 * @var $translator TranslatorInterface
 */

use Yiisoft\Html\Html;
use Yiisoft\Html\Tag\H2;
use Yiisoft\Yii\DataView\DetailView;
use Yiisoft\Yii\DataView\Field\DataField;

$this->setTitle('Profile');

$title = Html::encode($this->getTitle());
?>

<?= DetailView::widget()
    ->attributes(['class' => 'container'])
    ->containerAttributes(['class' => 'row'])
    ->data($item)
    ->fields(
        DataField::create()->attribute('id')
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
    ->itemTemplate("\n{label}\n{value}\n")
    ->labelAttributes(['class' => 'col-sm-3 text-lg-end'])
    ->valueAttributes(['class' => 'col-sm-7 alert alert-info']);
