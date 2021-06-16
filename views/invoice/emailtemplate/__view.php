<?php
declare(strict_types=1);

use Yiisoft\Html\Html;

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var array $body
 * @var string $csrf
 * @var string $action
 * @var string $title
 * @var $s
 */
?>

<h1><?= Html::encode($title) ?></h1>

  <div class="row">
    <div class="mb-3 form-group">
        <label for="email_template_title" class="form-label" style="background:lightblue">Email Template Title</label>
        <?= Html::encode($body['email_template_title'] ?? '') ?>
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="email_template_type" class="form-label" style="background:lightblue">Email Template Type</label>
        <?= Html::encode($body['email_template_type'] ?? '') ?>         
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="email_template_body" class="form-label" style="background:lightblue">Email Template Body</label>
        <?= Html::encode($body['email_template_body'] ?? '') ?>         
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="email_template_subject" class="form-label" style="background:lightblue">Email Template Subject</label>
        <?= Html::encode($body['email_template_subject'] ?? '') ?>         
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="email_template_from_name" class="form-label" style="background:lightblue">Email Template From Name</label>
        <?= Html::encode($body['email_template_from_name'] ?? '') ?>         
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="email_template_from_email" class="form-label" style="background:lightblue">Email Template From Email</label>
        <?= Html::encode($body['email_template_from_email'] ?? '') ?>         
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="email_template_cc" class="form-label" style="background:lightblue">Email Template CC</label>
        <?= Html::encode($body['email_template_cc'] ?? '') ?>         
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="email_template_bcc" class="form-label" style="background:lightblue">Email Template Bcc</label>
        <?= Html::encode($body['email_template_bcc'] ?? '') ?>         
    </div>
    <div class="mb-3 form-group no-margin">
        <label for="email_template_pdf_template" class="form-label" style="background:lightblue">Email Template Pdf Template</label>
        <?= Html::encode($body['email_template_pdf_template'] ?? '') ?>         
    </div>  
  </div> 

