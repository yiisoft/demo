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
        $current = $paginator->getPage();
        $pagesCount = $paginator->countPages();
        if ($pagesCount > 9) {
            if ($current <= 4) {
                $pages = [...range(1, 5), null, ...range($pagesCount - 2, $pagesCount)];
            } elseif ($pagesCount - $current <= 4) {
                $pages = [1, 2, null, ...range($pagesCount - 5, $pagesCount)];
            } else {
                $pages = [1, 2, null, $current - 1, $current, $current + 1, null, $pagesCount - 1, $pagesCount];
            }
        } else {
            $pages = range(1, $pagesCount);
        }

        foreach ($pages as $page) {
            $isDisabled = $current === $page || $page === null;
            echo Html::beginTag('li', ['class' => $isDisabled ? 'page-item disabled' : 'page-item']);
            if ($page === null) {
                echo Html::tag('span', '…', ['class' => 'page-link']);
            } else {
                echo Html::a($page, $pageUrlGenerator($page), ['class' => 'page-link']);
            }
            echo Html::endTag('li');
        }
        ?>
        <li class="page-item <?php echo $next === null ? 'disabled' : '' ?>">
            <?php echo Html::a('Next', $next ? $pageUrlGenerator($next) : null, ['class' => 'page-link']) ?>
        </li>
    </ul>
</nav>
