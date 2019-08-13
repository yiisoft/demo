<?php
use Yiisoft\Html\Html;
$error = $error ?? null;
?>

<?php if ($error !== null): ?>
<div class="alert alert-danger" role="alert">
  <?= Html::encode($error) ?>
</div>
<?php endif ?>

<form id="loginForm" method="POST" action="/login" enctype="multipart/form-data">
  <div class="form-group">
    <label for="subject">Login</label>
      <?= Html::textInput('login', $body['login'] ?? '', [
          'class' => 'form-control',
          'required' => true,
      ]) ?>
  </div>
  <div class="form-group">
    <label for="email">Password</label>
      <?= Html::passwordInput('password', $body['password'] ?? '', [
        'class' => 'form-control',
        'required' => true,
    ]) ?>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

