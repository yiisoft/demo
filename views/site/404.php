<?php

declare(strict_types=1);

/**
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 */

use Yiisoft\Html\Html;
?>

<div class="card shadow p-5 my-5 mx-5 bg-white rounded">
    <div class="card-body text-center ">
        <h1 class="card-title display-1 fw-bold">404</h1>
        <p class="card-text">
            <?php echo "The page "
                . Html::span(
                    Html::encode($urlMatcher->getCurrentUri()->getPath()),
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