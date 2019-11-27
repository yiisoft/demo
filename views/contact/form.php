<?php

/**
 * @var $this \Yiisoft\View\View
 * @var $urlGenerator \Yiisoft\Router\UrlGeneratorInterface
 */

?>
<?php if (isset($sent)): ?>
<?php if ($sent): ?>
<div class="alert alert-success" role="alert">
  Thanks to contact us, we'll get in touch with you as soon as possible.
</div>
<?php else: ?>
<div class="alert alert-danger" role="alert">
  <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>
<?php endif; ?>

<form id="contactForm" method="POST" action="<?= $urlGenerator->generate('site/contact') ?>" enctype="multipart/form-data">
  <div class="form-group">
    <label for="subject">Subject</label>
    <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" value="<?= $body['subject'] ?? '' ?>" required>
  </div>
  <div class="form-group">
    <label for="email">Email</label>
    <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" value="<?= $body['email'] ?? '' ?>" required>
  </div>
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?= $body['name'] ?? '' ?>" required>
  </div>
  <div class="form-group">
    <label for="content">Content</label>
    <textarea class="form-control" name="content" id="content" placeholder="Content" required><?= $body['content'] ?? '' ?></textarea>
  </div>
  <div class="form-group">
    <label>File</label>
    <input type="file" class="form-control-file" name="file">
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

