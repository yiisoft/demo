<?php

declare(strict_types=1);

use Yiisoft\View\WebView;
use Yiisoft\Yii\Bootstrap5\Carousel;

/** @var WebView $this */
$this->setTitle('Home');

echo Carousel::widget()
    ->items([
        [
            'content' => '<div class="d-block w-100 bg-info" style="height: 200px"></div>',
            'caption' => '<h5>Hello, everyone!</h5><p>A great day to try Yii 3, right?</p>',
            'captionOptions' => ['class' => ['d-none', 'd-md-block']],
        ],
        [
            'content' => '<div class="d-block w-100 bg-secondary" style="height: 200px"></div>',
            'caption' => '<h5>Code awaits!</h5><p>Check the project code. It\'s not ideal since it\'s a development sandbox as well, but gives a so-so overview of Yii 3 capabilities.</p>',
            'captionOptions' => ['class' => ['d-none', 'd-md-block']],
        ],
        [
            'content' => '<div class="d-block w-100 bg-dark" style="height: 200px"></div>',
            'caption' => '<h5>We need feedback!</h5><p>Please leave your feedback in either Telegram or Slack mentioned in README.</p>',
            'captionOptions' => ['class' => ['d-none', 'd-md-block']],
        ],
    ]);
?>


<div class="card mt-3 col-md-8">
    <div class="card-body">
        <h2 class="card-title">Console</h2>
        <?php $binPath = str_replace('/', DIRECTORY_SEPARATOR, './yii'); ?>
        <h4 class="card-title text-muted">Create new user</h4>
        <div>
            <code><?= "{$binPath} user/create &lt;login&gt; &lt;password&gt; [isAdmin = 0]" ?></code>
        </div>
        <h4 class="card-title text-muted">Assign RBAC role to user</h4>
        <div>
            <code><?= "{$binPath} user/assignRole &lt;role&gt; &lt;userId&gt;" ?></code>
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
            <br><code><?= "{$binPath} cycle/schema/php" ?></code>
            <br><code><?= "{$binPath} cycle/schema/clear" ?></code>
            <br><code><?= "{$binPath} cycle/schema/rebuild" ?></code>
        </div>
    </div>
</div>
