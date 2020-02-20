<?php

/**
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\WebView $this
 */

use App\LazyRendering\Http\StreamedController;
use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap4\Carousel;

echo Carousel::widget()
    ->items([
        [
            'content' => '<div class="d-block w-100 bg-info" style="height: 200px"></div>',
            'caption' => '<h5>First slide label</h5><p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>',
            'captionOptions' => ['class' => ['d-none', 'd-md-block']],
        ],
        [
            'content' => '<div class="d-block w-100 bg-secondary" style="height: 200px"></div>',
            'caption' => '<h5>Second slide label</h5><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>',
            'captionOptions' => ['class' => ['d-none', 'd-md-block']],
        ],
        [
            'content' => '<div class="d-block w-100 bg-dark" style="height: 200px"></div>',
            'caption' => '<h5>Third slide label</h5><p>Praesent commodo cursus magna, vel scelerisque nisl.</p>',
            'captionOptions' => ['class' => ['d-none', 'd-md-block']],
        ],
    ]);
?>


<div class="row">
    <div class="mt-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Console</h2>
                <?php $binPath = strtr('./vendor/bin/yii', '/', DIRECTORY_SEPARATOR); ?>
                <h4 class="card-title text-muted">Create new user</h4>
                <div>
                    <code><?php echo "{$binPath} user/create &lt;login&gt; &lt;password&gt;" ?></code>
                </div>
                <h4 class="card-title text-muted">Add random content</h4>
                <div>
                    <code><?php echo "{$binPath} fixture/add [count = 10]" ?></code>
                </div>
                <h4 class="card-title text-muted">Migrations</h4>
                <div>
                        <code><?php echo "{$binPath} migrate/create" ?></code>
                    <br><code><?php echo "{$binPath} migrate/generate" ?></code>
                    <br><code><?php echo "{$binPath} migrate/up" ?></code>
                    <br><code><?php echo "{$binPath} migrate/down" ?></code>
                    <br><code><?php echo "{$binPath} migrate/list" ?></code>
                </div>
                <h4 class="card-title text-muted">DB Schema</h4>
                <div>
                    <code><?php echo "{$binPath} cycle/schema" ?></code>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Lazy page rendering</h2>
                <p><?php
                    echo Html::a('Index page with all Lazy rendering experiments', $urlGenerator->generate(StreamedController::PAGE_ROUTE))
                    ?></p>
                <p class="lead">All post page</p>
                <div>
                    <?php
                    echo Html::a('Lazy', $urlGenerator->generate(StreamedController::PAGE_ROUTE, ['page' => 'allPosts']));
                    echo ' vs ';
                    echo Html::a('Full Classic', $urlGenerator->generate('blog/allPosts'))
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
