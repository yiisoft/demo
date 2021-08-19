<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\Router\CurrentRouteInterface $currentRoute
 */

$this->setTitle('Not found');
?>

<div class="card shadow p-5 my-5 mx-5 bg-white rounded">
    <div class="card-body text-center ">
        <h1 class="card-title display-1 fw-bold">404</h1>
        <p class="card-text">
            <?php echo "The page "
                . Html::span(
                    Html::encode($currentRoute->getUri()->getPath()),
                    ['class' => 'text-muted']
                )
                . " could not be found."
            ?>
        </p>
        <p>
            <?php echo Html::a(
                'Go Back Home',
                $urlGenerator->generate('site/index'),
                ['class' => 'btn btn-outline-primary mt-5']
            );
            ?>
        </p>
    </div>
</div>
