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
<!--    <span class="text-left"> --><?php //echo $item->getUser()->getLogin() ?><!--</span>-->
    <span class="text-left"> <?php print_r($item->getUser()) ?></span>
    <span class="text-right"><?php echo $item->getPublishedAt()->format('r') ?></span>
</div>
<article><?php echo $item->getContent() ?></article>
