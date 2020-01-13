<?php
/**
 * @var \Spiral\Pagination\Paginator $paginator
 * @var \Closure $pageUrlGenerator Single argument function (page number)
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use Yiisoft\Html\Html;

$prev = $paginator->previousPage();
$next = $paginator->nextPage();

?>
<nav aria-label="Page navigation">
    <ul class="pagination">
        <li class="page-item <?php echo $prev === null ? 'disabled' : '' ?>">
            <?php echo Html::a('Previous', $prev ? $pageUrlGenerator($prev) : null, ['class' => 'page-link']) ?>
        </li>
        <?php
        for ($page = 1, $current = $paginator->getPage(), $pages = $paginator->countPages(); $page <= $pages; ++$page) {
            echo Html::beginTag('li', ['class' => $current === $page ? 'page-item disabled' : 'page-item']);
            echo Html::a($page, $pageUrlGenerator($page), ['class' => 'page-link']);
            echo Html::endTag('li');
        }
        ?>
        <li class="page-item <?php echo $next === null ? 'disabled' : '' ?>">
            <?php echo Html::a('Next', $next ? $pageUrlGenerator($next) : null, ['class' => 'page-link']) ?>
        </li>
    </ul>
</nav>
