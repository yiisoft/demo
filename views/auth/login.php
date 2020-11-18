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

<?php if ($error !== null) { ?>
<div class="alert alert-danger" role="alert">
  <?php echo Html::encode($error) ?>
</div>
<?php } ?>

<form id="loginForm" method="POST" action="<?php echo $urlGenerator->generate('site/login') ?>" enctype="multipart/form-data">
  <input type="hidden" name="_csrf" value="<?php echo $csrf ?>">
  <div class="form-group">
    <label for="subject">Login</label>
      <?php echo Html::textInput('login', $body['login'] ?? '', [
          'class' => 'form-control',
          'required' => true,
      ]) ?>
  </div>
  <div class="form-group">
    <label for="email">Password</label>
      <?php echo Html::passwordInput('password', $body['password'] ?? '', [
          'class' => 'form-control',
          'required' => true,
      ]) ?>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

