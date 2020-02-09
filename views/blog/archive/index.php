<?php

/**
 * @var \Yiisoft\Data\Reader\DataReaderInterface|string[][] $archive
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use App\Blog\Entity\Post;
use Yiisoft\Html\Html;

?>
<h1>Archive</h1>
<div class="row">
    <div class="col-sm-12">
        <?php
        $currentYear = null;
        $yBlockBegin = Html::beginTag(
            'li',
            ['class' => 'list-group-item d-flex flex-column justify-content-between lh-condensed']
        );
        $yBlockEnd = Html::endTag('li');
        $mBlockBegin = Html::beginTag(
            'div',
            ['class' => 'd-flex flex-wrap']
        );
        $mBlockEnd = Html::endTag('div');
        if (count($archive)) {
            foreach ($archive->read() as $aValue) {
                $year = $aValue['year'];
                $month = $aValue['month'];
                $count = $aValue['count'];
                $isNewBlock = $currentYear !== $year;

                if ($isNewBlock) {
                    // print Year
                    echo $yBlockBegin, Html::a(
                        $year,
                        $urlGenerator->generate('blog/archive/year', ['year' => $year]),
                        ['class' => 'h5']
                    ), $mBlockBegin;
                }
                echo Html::beginTag('div', ['class' => 'mx-2 my-1']);
                // Print month name
                echo Html::a(
                    Date('F', mktime(0, 0, 0, (int)$month, 1, (int)$year)),
                    $urlGenerator->generate('blog/archive/month', [
                        'year' => $year,
                        'month' => $month,
                    ]),
                    ['class' => 'text-muted']
                ), ' ', Html::tag('sup', $count, ['class' => '']);
                echo Html::endTag('div');
                $currentYear = $year;
            }
            echo $mBlockEnd, $yBlockEnd;
        } else {
            echo $yBlockBegin, 'No records', $yBlockEnd;
        }
        ?>
    </div>
</div>
