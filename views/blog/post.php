<?php
/**
 * @var \App\Entity\Post $item
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

#todo: escape strings
?>

<h1><?php echo $item->getTitle() ?></h1>
<div class="">
    <span class="text-muted"><?php echo $item->getPublishedAt()->format('H:i:s d.m.Y') ?> by</span>
    <?php
    echo Html::a(
        Html::encode($item->getUser()->getLogin()),
        $urlGenerator->generate('user/profile', ['login' => $item->getUser()->getLogin()])
    );
    ?>
</div>
<?php echo Html::tag('article', $item->getContent()) ?>
