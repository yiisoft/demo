<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var string $csrf
 */

$error = $error ?? null;
?>

<?php if ($error !== null) : ?>
  <div class="alert alert-danger" role="alert">
    <?= Html::encode($error) ?>
  </div>
<?php endif ?>

<form id="signupForm" method="POST" action="<?= $urlGenerator->generate('site/signup') ?>" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= $csrf ?>">
  <div class="mb-3">
    <label for="login" class="form-label required">Login</label>
    <?= Html::textInput('login', $body['login'] ?? '', [
      'id' => 'login',
      'class' => 'form-control',
      'required' => true,
    ]) ?>
  </div>
  <div class="mb-3">
    <label for="password" class="form-label required">Password</label>
    <?= Html::passwordInput('password', $body['password'] ?? '', [
      'id' => 'password',
      'class' => 'form-control',
      'required' => true,
    ]) ?>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
