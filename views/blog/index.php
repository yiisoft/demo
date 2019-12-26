<?php
/**
 * @var string[][] $archive
 * @var \Cycle\ORM\Iterator|\App\Entity\Post[] $items
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

?>
<div class="row">
    <div class="col-sm-8 col-md-8 col-lg-9">
        <?php
        foreach ($items as $item) {
            $url = $urlGenerator->generate('blog/page', ['slug' => $item->getSlug()]);
            ?>
            <div class="card mb-4">
                <div class="card-body d-flex flex-column align-items-start">
                    <?php

                    echo Html::a(
                        Html::encode($item->getTitle()),
                        $url,
                        ['class' => 'mb-0 h4 text-decoration-none stretched-link']
                    );
                    echo Html::tag(
                        'div',
                        $item->getPublishedAt()->format('M, d'),
                        ['class' => 'mb-1 text-muted']
                    );
                    echo Html::tag(
                        'p',
                        Html::encode(mb_substr($item->getContent(), 0, 400)) . (mb_strlen($item->getContent()) > 400 ? '…' : ''),
                        ['class' => 'card-text mb-auto']
                    );
                    ?>
                </div>
            </div>
            <?php

        }
        ?>
    </div>
    <div class="col-sm-4 col-md-4 col-lg-3">
        <h4 class="text-muted mb-3">
            Archive
        </h4>
        <ul class="list-group mb-3">
            <?php
            $currentYear = null;
            $blockBegin = Html::beginTag(
                'li',
                ['class' => 'list-group-item d-flex flex-column justify-content-between lh-condensed']
            );
            $blockEnd = Html::endTag('li');
            if (count($archive)) {
                foreach ($archive as $aValue):
                    $year = $aValue['Year'];
                    $month = $aValue['Month'];
                    $count = $aValue['Count'];
                    $isNewBlock = $currentYear !== $year;

                    if ($isNewBlock) {
                        // print Year
                        echo $blockBegin, Html::tag('h6', $year, ['class' => 'my-0']);
                    }
                    echo Html::beginTag('div', ['class' => 'd-flex justify-content-between align-items-center']);
                        // Print month name
                        echo Html::a(
                            Date('F', mktime(0, 0, 0, (int)$month, 1, (int)$year)),
                            '?',
                            ['class' => 'text-muted']
                        ), ' ', Html::tag('span', $count, ['class' => 'badge badge-secondary badge-pill']);
                    echo Html::endTag('div');
                    $currentYear = $year;
                endforeach;
                echo $blockEnd;
            } else {
                echo $blockBegin, 'No records', $blockEnd;
            }
            ?>
        </ul>
    </div>
</div>
