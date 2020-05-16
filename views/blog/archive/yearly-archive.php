<?php

/**
 * @var int $year
 * @var Post[]|\Yiisoft\Data\Reader\DataReaderInterface $items
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use App\Blog\Entity\Post;
use Yiisoft\Html\Html;

?>
<h1>Archive <small class="text-muted">Year <?= $year ?></small></h1>
<div class="row">
    <div class="col-sm-8 col-md-8 col-lg-9">
        <?php
        if (count($items) > 0) {
            echo Html::tag(
                'p',
                sprintf('Total %d posts', count($items)),
                ['class' => 'text-muted']
            );
        } else {
            echo Html::tag('p', 'No records');
        }
        $currentMonth = null;
        $monthName = '';
        /** @var Post $item */
        foreach ($items as $item) {
            $month = (int)$item->getPublishedAt()->format('m');

            if ($currentMonth !== $month) {
                $currentMonth = $month;
                $monthName = DateTime::createFromFormat('!m', $month)->format('F');
                echo Html::tag('div', "{$year} {$monthName}", ['class' => 'lead']);
            }
            echo Html::beginTag('div');
            echo Html::a(
                Html::encode($item->getTitle()),
                $urlGenerator->generate('blog/post', ['slug' => $item->getSlug()])
            );
            echo ' by ';
            $login = $item->getUser()->getLogin();
            echo Html::a(Html::encode($login), $urlGenerator->generate(
                'user/profile',
                ['login' => $login]
            ));
            echo Html::endTag('div');
        }
        ?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-3"></div>
</div>
