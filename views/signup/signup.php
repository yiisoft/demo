<?php

declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var \Yiisoft\View\WebView $this
 * @var \Yiisoft\Translator\TranslatorInterface $translator
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var string $csrf
 */

$this->setTitle($translator->translate('Signup'));
$error = $error ?? null;
?>

<?php if ($error !== null) : ?>
  <div class="alert alert-danger" role="alert">
    <?= Html::encode($error) ?>
  </div>
<?php endif ?>

<form id="signupForm" method="POST" action="<?= $urlGenerator->generate('auth/signup') ?>" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?= $csrf ?>">
  <div class="mb-3">
    <label for="login" class="form-label required"><?= $translator->translate('layout.login') ?></label>
    <?= Html::textInput('login', $body['login'] ?? '', [
      'id' => 'login',
      'class' => 'form-control',
      'required' => true,
    ]) ?>
  </div>
  <div class="mb-3">
    <label for="password" class="form-label required"><?= $translator->translate('layout.password') ?></label>
    <?= Html::passwordInput('password', $body['password'] ?? '', [
      'id' => 'password',
      'class' => 'form-control',
      'required' => true,
    ]) ?>
  </div>
  <button type="submit" class="btn btn-primary"><?= $translator->translate('layout.submit') ?></button>
</form>
