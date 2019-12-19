<?php
/**
 * @var \Cycle\ORM\Iterator|\App\Entity\Post[] $items
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

#todo: escape strings
?>
<div class="card-columns">

    <?php
    foreach ($items as $item) {
        $url = $urlGenerator->generate('blog/page', ['slug' => $item->getSlug()]);
        ?>
            <div class="card">
                <div class="card-body d-flex flex-column align-items-start">
                    <h4 class="mb-0">
                        <a class="text-decoration-none" href="<?php echo $url ?>">
                            <?php echo $item->getTitle(); ?>
                        </a>
                    </h4>
                    <div class="mb-1 text-muted"><?php echo $item->getPublishedAt()->format('M, d'); ?></div>
                    <p class="card-text mb-auto"><?php echo mb_substr($item->getContent(), 0, 200); ?>…</p>
                </div>
            </div>
        <?php
    }
    ?>

</div>
