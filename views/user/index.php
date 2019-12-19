<?php
/**
 * @var \App\Entity\User[] $items
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

#todo: escape strings
?>
<p>Users count: <?php echo count($items) ?></p>

<?php
foreach ($items as $item) {
    ?>
    <a class="btn btn-link"
       href="<?php echo $urlGenerator->generate('user/profile', ['login' => $item->getLogin()]) ?>"
    ><?php echo $item->getLogin() ?></a><?php
}
