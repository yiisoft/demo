<?php

/**
 * @var \App\Entity\User $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

echo Html::tag('h1', Html::encode($item->getLogin()));
?>
<div>
    <span class="text-muted">Created at <?php echo $item->getCreatedAt()->format('H:i:s d.m.Y') ?></span>
</div>
