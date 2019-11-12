<?php

use Yiisoft\Html\Html;

/**
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var \Yiisoft\View\View $this
 * @var \App\Entity\User $user
 * @var string $content
 */
?><!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Yii Demo</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light container">
    <a class="navbar-brand" href="<?= $urlGenerator->generate('site/index') ?>">Yii Demo</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?= $urlGenerator->generate('site/contact') ?>">Contact</a>
            </li>
            <?php if ($user->getId() !== null): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $urlGenerator->generate('site/logout') ?>">Logout (<?= Html::encode($user->getLogin()) ?>)</a>
                </li>
            <?php else: ?>
            <li class="nav-item">
                <a class="nav-link" href="<?= $urlGenerator->generate('site/login') ?>">Login</a>
            </li>
            <?php endif ?>

        </ul>
    </div>
</nav>
<main role="main" class="container">
    <?= $content ?>
</main>
</body>
</html>
