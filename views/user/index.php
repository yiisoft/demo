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
use Yiisoft\Yii\DataView\Column\DataColumn;
use Yiisoft\Yii\DataView\GridView;

$this->setTitle($translator->translate('menu.users'));
?>

<div>
    <div class="text-end">
        <?= Html::a('API v1 Info', $urlGenerator->generate('api/info/v1'), ['class' => 'btn btn-link']) ?>
        <?= Html::a('API v2 Info', $urlGenerator->generate('api/info/v2'), ['class' => 'btn btn-link']) ?>
        <?= Html::a('API Users List Data', $urlGenerator->generate('api/user/index'), ['class' => 'btn btn-link'])?>
    </div>

    <?= $this->render(
        '_gridview',
        ['csrf' => $csrf, 'page' => $page, 'paginator' => $paginator, 'pageSize' => $pageSize],
    ) ?>
</div>
