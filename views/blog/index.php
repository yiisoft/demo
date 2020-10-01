<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\Data\Paginator\OffsetPaginator $paginator;
 * @var \Yiisoft\Data\Reader\DataReaderInterface|string[][] $archive
 * @var \Yiisoft\Data\Reader\DataReaderInterface|string[][] $tags
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 * @var bool $isGuest
 */

use App\Blog\Entity\Post;
use App\Blog\Widget\PostCard;
use App\Widget\OffsetPagination;
use Yiisoft\Html\Html;

$pagination = OffsetPagination::widget()
                              ->paginator($paginator)
                              ->urlGenerator(fn ($page) => $urlGenerator->generate('blog/index', ['page' => $page]));
?>
<h1>Blog</h1>
<div class="row">
    <div class="col-sm-8 col-md-8 col-lg-9">
        <?php
        $pageSize = $paginator->getCurrentPageSize();
        if ($pageSize > 0) {
            echo Html::tag(
                'p',
                sprintf('Showing %s out of %s posts', $pageSize, $paginator->getTotalItems()),
                ['class' => 'text-muted']
            );
        } else {
            echo Html::tag('p', 'No records');
        }
        /** @var Post $item */
        foreach ($paginator->read() as $item) {
            echo PostCard::widget()->post($item);
        }
        if ($pagination->isRequired()) {
            echo $pagination;
        }
        ?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-3">
        <?php
        if (!$isGuest) {
            echo Html::a(
                'Add post',
                $urlGenerator->generate('blog/add'),
                ['class' => 'btn btn-outline-secondary btn-md-12 mb-3']
            );
        } ?>
        <?= $this->render('_topTags', ['tags' => $tags]) ?>
        <?= $this->render('_archive', ['archive' => $archive]) ?>
    </div>
</div>
