<?php

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


<div class="card mt-3 col-md-6">
    <div class="card-body">
        <h2 class="card-title">Console</h2>
        <?php $binPath = strtr('./vendor/bin/yii', '/', DIRECTORY_SEPARATOR); ?>
        <h4 class="card-title text-muted">Create new user</h4>
        <div>
            <code><?= "{$binPath} user/create &lt;login&gt; &lt;password&gt;" ?></code>
        </div>
        <h4 class="card-title text-muted">Add random content</h4>
        <div>
            <code><?= "{$binPath} fixture/add [count = 10]" ?></code>
        </div>
        <h4 class="card-title text-muted">Migrations</h4>
        <div>
            <code><?= "{$binPath} migrate/create" ?></code>
            <br><code><?= "{$binPath} migrate/generate" ?></code>
            <br><code><?= "{$binPath} migrate/up" ?></code>
            <br><code><?= "{$binPath} migrate/down" ?></code>
            <br><code><?= "{$binPath} migrate/list" ?></code>
        </div>
        <h4 class="card-title text-muted">DB Schema</h4>
        <div>
            <code><?= "{$binPath} cycle/schema" ?></code>
        </div>
    </div>
</div>
