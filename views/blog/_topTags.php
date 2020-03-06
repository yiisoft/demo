<?php

/**
 * @var \Yiisoft\Data\Reader\DataReaderInterface|string[][] $tags
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
    $blockBegin = Html::beginTag(
    'li',
    ['class' => 'list-group-item d-flex flex-column justify-content-between lh-condensed']
);
    $blockEnd = Html::endTag('li');
    echo $blockBegin;
    if (count($tags)) {
        foreach ($tags->read() as $tagValue) {
            $label = $tagValue['label'];
            $count = $tagValue['count'];

            echo Html::beginTag('div', ['class' => 'd-flex justify-content-between align-items-center']);
            echo Html::a(
                Html::encode($label),
                $urlGenerator->generate('blog/tag', ['label' => $label]),
                ['class' => 'text-muted overflow-hidden']
            ), ' ', Html::tag('span', $count, ['class' => 'badge badge-secondary badge-pill']);
            echo Html::endTag('div');
        }
    } else {
        echo 'tags not found';
    }
    echo $blockEnd;
    ?>
</ul>
