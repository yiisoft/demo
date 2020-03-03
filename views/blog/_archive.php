<?php

/**
 * @var \Yiisoft\Data\Reader\DataReaderInterface|string[][] $archive
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

?>
<h4 class="text-muted mb-3">Archive</h4>
<ul class="list-group mb-3">
    <?php
    $currentYear = null;
    $blockBegin = Html::beginTag(
        'li',
        ['class' => 'list-group-item d-flex flex-column justify-content-between lh-condensed']
    );
    $blockEnd = Html::endTag('li');
    if (count($archive)) {
        foreach ($archive->read() as $item) {
            $year = $item['year'];
            $month = $item['month'];
            $count = $item['count'];

            if ($currentYear !== $year) {
                // print Year
                echo $blockBegin, Html::tag('h6', $year, ['class' => 'my-0']);
            }
            echo Html::beginTag('div', ['class' => 'd-flex justify-content-between align-items-center']);
            // Print month name
            echo Html::a(
                Date('F', mktime(0, 0, 0, (int)$month, 1, (int)$year)),
                $urlGenerator->generate('blog/archive/month', [
                    'year' => $year,
                    'month' => $month,
                ]),
                ['class' => 'text-muted']
            ), ' ', Html::tag('span', $count, ['class' => 'badge badge-secondary badge-pill']);
            echo Html::endTag('div');
            $currentYear = $year;
        }
        echo Html::a('Open archive', $urlGenerator->generate('blog/archive/index'), ['class' => 'mt-2']);
        echo $blockEnd;
    } else {
        echo $blockBegin, 'No records', $blockEnd;
    }
    ?>
</ul>
