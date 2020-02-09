<?php

/**
 * @var \Yiisoft\Data\Reader\DataReaderInterface|string[][] $archive
 * @var \Yiisoft\Data\Reader\DataReaderInterface|string[][] $tags
 * @var \App\Pagination\PaginationSet $paginationSet;
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use App\Blog\Entity\Post;
use Yiisoft\Html\Html;

?>
<h1>Blog</h1>
<div class="row">
    <div class="col-sm-8 col-md-8 col-lg-9">
        <?php
        $pageSize = $paginationSet->getPaginator()->getCurrentPageSize();
        if ($pageSize > 0) {
            echo Html::tag(
                'p',
                sprintf('Showing %s out of %s posts', $pageSize, $paginationSet->getPaginator()->getTotalItems()),
                ['class' => 'text-muted']
            );
        } else {
            echo Html::tag('p', 'No records');
        }
        /** @var Post $item */
        foreach ($paginationSet->getPaginator()->read() as $item) {
            echo \App\Blog\Widget\PostCard::widget()->post($item);
        }
        if ($paginationSet->needToPaginate()) {
            echo $this->render('_pagination', ['paginationSet' => $paginationSet]);
        }
        ?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-3">
        <?php echo $this->render('_topTags', ['tags' => $tags]) ?>
        <?php echo $this->render('_archive', ['archive' => $archive]) ?>
    </div>
</div>
