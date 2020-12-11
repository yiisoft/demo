<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var $this \Yiisoft\View\View
 * @var $urlGenerator \Yiisoft\Router\UrlGeneratorInterface
 * @var $csrf string
 */

$error = $error ?? null;
?>

<?php if ($error !== null): ?>
<div class="alert alert-danger" role="alert">
  <?= Html::encode($error) ?>
</div>
<?php endif ?>

<form id="loginForm" method="POST" action="<?= $urlGenerator->generate('site/login') ?>" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= $csrf ?>">
  <div class="form-group">
    <label for="subject">Login</label>
      <?= Html::textInput('login', $body['login'] ?? '', [
          'class' => 'form-control',
          'required' => true,
      ]) ?>
  </div>
  <div class="form-group mt-3">
    <label for="email">Password</label>
      <?= Html::passwordInput('password', $body['password'] ?? '', [
        'class' => 'form-control',
        'required' => true,
    ]) ?>
  </div>
  <button type="submit" class="btn btn-primary mt-3">Submit</button>
</form>

