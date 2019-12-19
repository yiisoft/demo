<?php
/**
 * @var \App\Entity\User[] $items
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

#todo: escape strings
?>
Users count: <?php echo count($items) ?>

<?php
foreach ($items as $item) {
    echo '<span class="badge badge-pill">' . $item->getLogin() . '</span>';
}
