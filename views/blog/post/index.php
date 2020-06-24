<?php

/**
 * @var \App\Blog\Entity\Post $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

?>
    <h1><?= Html::encode($item->getTitle()) ?></h1>
    <div>
        <span class="text-muted"><?= $item->getPublishedAt()->format('H:i:s d.m.Y') ?> by</span>
        <?php
        echo Html::a(
    Html::encode($item->getUser()->getLogin()),
    $urlGenerator->generate('user/profile', ['login' => $item->getUser()->getLogin()])
);
        ?>
    </div>
<?php

echo Html::tag('article', Html::encode($item->getContent()), ['class' => 'text-justify']);

if ($item->getTags()) {
    echo Html::beginTag('div', ['class' => 'mt-3']);
    foreach ($item->getTags() as $tag) {
        echo Html::a(
            Html::encode($tag->getLabel()),
            $urlGenerator->generate('blog/tag', ['label' => $tag->getLabel()]),
            ['class' => 'btn btn-outline-secondary btn-sm m-1']
        );
    }
    echo Html::endTag('div');
}

echo Html::tag('h2', 'Comments', ['class' => 'mt-4 text-muted']);
echo Html::beginTag('div', ['class' => 'mt-3']);
if ($item->getComments()) {
    foreach ($item->getComments() as $comment) {
        ?>
        <div class="media mt-4 shadow p-3 rounded">
            <div class="media-body">
                <div>
                    <?= Html::a(
            Html::encode($comment->getUser()->getLogin()),
            $urlGenerator->generate('user/profile', ['login' => $comment->getUser()->getLogin()])
        ) ?>
                    <span class="text-muted">
                        <i>created at</i> <?= $comment->getCreatedAt()->format('H:i d.m.Y') ?>
                    </span>
                    <?php if ($comment->isPublic()) { ?>
                        <span class="text-muted">
                            <i>published at</i> <?= $comment->getPublishedAt()->format('d.m.Y') ?>
                        </span>
                    <?php } ?>
                    <span><?= $comment->isPublic()
                            ? ''
                            : Html::tag(
                                'span',
                                'hidden',
                                ['class' => 'border border-info rounded px-2 text-muted']
                            ) ?></span>
                </div>
                <div class="mt-1 text-justify">
                    <?= Html::encode($comment->getContent()) ?>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    echo Html::tag('p', 'No comments', ['class' => 'lead']);
}
echo Html::endTag('div');
