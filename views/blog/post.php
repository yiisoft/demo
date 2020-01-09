<?php
/**
 * @var \App\Entity\Post $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

?>
<h1><?php echo Html::encode($item->getTitle()) ?></h1>
<div class="">
    <span class="text-muted"><?php echo $item->getPublishedAt()->format('H:i:s d.m.Y') ?> by</span>
    <?php
    echo Html::a(
        Html::encode($item->getUser()->getLogin()),
        $urlGenerator->generate('user/profile', ['login' => $item->getUser()->getLogin()])
    );
    ?>
</div>
<?php

echo Html::tag('article', Html::encode($item->getContent()));

if ($item->getTags()->count()) {
    echo Html::beginTag('div', ['class' => 'mt-3']);
    foreach ($item->getTags() as $tag) {
        echo Html::a(
            Html::encode($tag->getLabel()),
            $urlGenerator->generate('blog/tag', ['label' => $tag->getLabel()]),
            ['class' => 'btn btn-outline-secondary btn-sm mx-1']
        );
    }
    echo Html::endTag('div');
}
