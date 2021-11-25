<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var string $csrf
 */

$this->setTitle(Html::encode($translator->translate('layout.login')));

$error = $error ?? null;
?>

<?php if ($error !== null) : ?>
  <div class="alert alert-danger" role="alert">
    <?= Html::encode($error) ?>
  </div>
<?php endif ?>

<form id="loginForm" method="POST" action="<?= $urlGenerator->generate('auth/login') ?>" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= $csrf ?>">
  <div class="mb-3">
    <label for="login" class="form-label required"><?= Html::encode($translator->translate('layout.login')) ?></label>
    <?= Html::textInput('login', $body['login'] ?? '', [
      'id' => 'login',
      'class' => 'form-control',
      'required' => true,
    ]) ?>
  </div>
  <div class="mb-3">
    <label for="password" class="form-label required"><?= Html::encode($translator->translate('layout.password')) ?></label>
    <?= Html::passwordInput('password', $body['password'] ?? '', [
      'id' => 'password',
      'class' => 'form-control',
      'required' => true,
    ]) ?>
  </div>
  <div class="form-check mb-3">
    <?= Html::checkbox('remember', $body['remember'] ?? '', [
      'id' => 'remember',
      'class' => 'form-check-input',
      'value' => '1',
    ]) ?>
    <label for="remember" class="form-check-label"><?= Html::encode($translator->translate('layout.remember')) ?></label>
  </div>
  <button type="submit" class="btn btn-primary"><?= Html::encode($translator->translate('layout.submit')) ?></button>
</form>
