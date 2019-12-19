<?php
/**
 * @var \App\Entity\Post[] $items
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

?>
Items count: <?php echo count($items) ?>

<?php
foreach ($items as $item) {
    echo '<p>' . $item->getTitle() . '</p>';
}
