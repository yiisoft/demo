<?php
/**
 * @var \App\Entity\User[] $items
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

?>
Users count: <?php echo count($items) ?>

<?php
foreach ($items as $item) {
    echo '<p>' . $item->getLogin() . '</p>';
}
