<?php

/**
 * @var \Yiisoft\Data\Reader\DataReaderInterface|string[][] $archive
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

?>
<h1>Archive</h1>
<div class="row">
    <div class="col-sm-12">
        <?php
        $currentYear = null;
        $sectionBegin = Html::beginTag(
            'li',
            ['class' => 'list-group-item d-flex flex-column justify-content-between lh-condensed']
        );
        $sectionEnd = Html::endTag('li');
        if (count($archive)) {
            foreach ($archive->read() as $item) {
                $year = $item['year'];
                $month = $item['month'];
                $count = $item['count'];

                if ($currentYear !== $year) {
                    // print Year
                    echo $sectionBegin, Html::a(
                        $year,
                        $urlGenerator->generate('blog/archive/year', ['year' => $year]),
                        ['class' => 'h5']
                    ), Html::beginTag(
                        'div',
                        ['class' => 'd-flex flex-wrap']
                    );
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
            echo Html::endTag('div'), $sectionEnd;
        } else {
            echo $sectionBegin, 'No records', $sectionEnd;
        }
        ?>
    </div>
</div>
