<?php

declare(strict_types=1);

/**
 * @var DataReaderInterface|string[][] $archive
 * @var TranslatorInterface            $translator
 * @var UrlGeneratorInterface          $urlGenerator
 * @var WebView                        $this
 */

use Yiisoft\Data\Reader\DataReaderInterface;
use Yiisoft\Html\Html;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\View\WebView;

$this->setTitle($translator->translate('layout.archive'));

?>
<h1><?= Html::encode($this->getTitle()) ?></h1>
<div class="row">
    <div class="col-sm-12">
        <?php
        $currentYear = null;
        $sectionBegin = Html::openTag(
            'li',
            ['class' => 'list-group-item d-flex flex-column justify-content-between lh-condensed']
        );
        $sectionEnd = Html::closeTag('li');
        if (count($archive)) {
            foreach ($archive->read() as $item) {
                $year = $item['year'];
                $month = $item['month'];
                $count = $item['count'];

                if ($year === null || $month === null) {
                    continue;
                }

                if ($currentYear !== $year) {
                    // print Year
                    echo $sectionBegin, Html::a(
                        $year,
                        $urlGenerator->generate('blog/archive/year', ['year' => $year]),
                        ['class' => 'h5']
                    ), Html::openTag(
                        'div',
                        ['class' => 'd-flex flex-wrap']
                    );
                }
                echo Html::openTag('div', ['class' => 'mx-2 my-1']);
                // Print month name
                echo Html::a(
                    date('F', mktime(0, 0, 0, (int) $month, 1, (int) $year)),
                    $urlGenerator->generate('blog/archive/month', [
                        'year' => $year,
                        'month' => $month,
                    ]),
                    ['class' => 'text-muted']
                ), ' ', Html::tag('sup', $count, ['class' => '']);
                echo Html::closeTag('div');
                $currentYear = $year;
            }
            echo Html::closeTag('div'), $sectionEnd;
        } else {
            echo $sectionBegin, $translator->translate('layout.no-records'), $sectionEnd;
        }
        ?>
    </div>
</div>
