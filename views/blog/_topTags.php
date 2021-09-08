<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\Data\Reader\DataReaderInterface|string[][] $tags
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

?>
<h4 class="text-muted mb-3">
    Popular tags
</h4>
<ul class="list-group mb-3">
    <?php
    $blockBegin = Html::openTag(
        'li',
        ['class' => 'list-group-item d-flex flex-column justify-content-between lh-condensed']
    );
    $blockEnd = Html::closeTag('li');
    echo $blockBegin;
    if (count($tags)) {
        foreach ($tags->read() as $tagValue) {
            $label = $tagValue['label'];
            $count = (string) $tagValue['count'];

            echo Html::openTag('div', ['class' => 'd-flex justify-content-between align-items-center']);
            echo Html::a(
                Html::encode($label),
                $urlGenerator->generate('blog/tag', ['label' => $label]),
                ['class' => 'text-muted overflow-hidden']
            ), ' ', Html::span($count, ['class' => 'badge rounded-pill bg-secondary']);
            echo Html::closeTag('div');
        }
    } else {
        echo 'tags not found';
    }
    echo $blockEnd;
    ?>
</ul>
