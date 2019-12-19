<?php
/**
 * @var \App\Entity\User $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */


#todo: escape strings
?>

<h1><?php echo $item->getLogin() ?></h1>
<div class="">
    <span class="text-muted">Created at <?php echo $item->getCreatedAt()->format('H:i:s d.m.Y') ?></span>
</div>
