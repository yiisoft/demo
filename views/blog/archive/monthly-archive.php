<?php

/**
 * @var int $year
 * @var int $month
 * @var \App\Pagination\PaginationSet $paginationSet;
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use App\Blog\Entity\Post;
use Yiisoft\Html\Html;

$monthName = DateTime::createFromFormat('!m', $month)->format('F');
?>
<h1>Archive <small class="text-muted"><?php echo "$monthName $year" ?></small></h1>
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
            echo $this->render('../_pagination', ['paginationSet' => $paginationSet]);
        }
        ?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-3">
    </div>
</div>
