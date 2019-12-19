<?php
/**
 * @var \App\Entity\Post $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */


#todo: escape strings
?>

<h1><?php echo $item->getTitle() ?></h1>
<div class="">
    <span class="text-muted"><?php echo $item->getPublishedAt()->format('H:i:s d.m.Y') ?> by</span>
    <a href="<?php echo $urlGenerator->generate('user/profile', ['login' => $item->getUser()->getLogin()]) ?>">
        <?php echo $item->getUser()->getLogin() ?>
    </a>
</div>
<article><?php echo $item->getContent() ?></article>
