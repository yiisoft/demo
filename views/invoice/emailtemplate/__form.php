<?php

declare(strict_types=1);

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var Yiisoft\Yii\View\ViewRenderer $viewRenderer
 * @var array $body
 * @var string $csrf
 * @var string $action
 * @var array $errors
 * @var string $title
 */

$this->addJsFiles($assetManager->getJsFiles());

if (!empty($errors)) {
    foreach ($errors as $field => $error) {
        echo Alert::widget()->options(['class' => 'alert-danger'])->body(Html::encode($field . ':' . $error));
    }
}
?>

<h1><?= Html::encode($title) ?></h1>

<form id="emailtemplateForm" method="POST" action="<?= $urlGenerator->generate(...$action) ?>" enctype="multipart/form-data" >
<input type="hidden" name="_csrf" value="<?= $csrf ?>">
  <div class="row">
    <div class="mb-3 form-group">
        <input type="text" class="form-control" name="email_template_title" id="email_template_title" placeholder="<?= $s->trans('title'); ?>" value="<?= Html::encode($body['email_template_title'] ?? '') ?>" required>
    </div>
    <div class="mb-3 form-group">
        <div class="radio">
            <label>
                <input type="radio" name="email_template_type" id="email_template_type_invoice"
                       value="<?= Html::encode($body['email_template_title'] ?? 'invoice') ?>" checked>
                <?= $s->trans('invoice'); ?>
            </label>
        </div>
        <div class="radio">
            <label>
                <input type="radio" name="email_template_type" id="email_template_type_quote"
                       value="<?= Html::encode($body['email_template_title'] ?? 'quote') ?>">
                <?= $s->trans('quote'); ?>
            </label>
        </div>
    </div>  
    <div class="mb-3 form-group">
                    <input type="text" name="email_template_from_name" id="email_template_from_name"
                           class="form-control taggable" placeholder="<?= $s->trans('from_name'); ?>"
                           value="<?= Html::encode($body['email_template_from_name'] ?? '') ?>" required>
    </div>
    <div class="mb-3 form-group">
                    <input type="text" name="email_template_from_email" id="email_template_from_email"
                           class="form-control taggable" placeholder="<?= $s->trans('from_email'); ?>" required
                           value="<?= Html::encode($body['email_template_from_email'] ?? '') ?>">
    </div>
    <div class="mb-3 form-group">
                    <input type="text" name="email_template_cc" id="email_template_cc" class="form-control taggable" placeholder="<?= $s->trans('cc'); ?>"
                           value="<?= Html::encode($body['email_template_cc'] ?? '') ?>">
    </div>

    <div class="mb-3 form-group">
                    <input type="text" name="email_template_bcc" id="email_template_bcc" class="form-control taggable" placeholder="<?= $s->trans('bcc'); ?>"
                           value="<?= Html::encode($body['email_template_bcc'] ?? '') ?>">
    </div>

    <div class="mb-3 form-group">
                    <input type="text" name="email_template_subject" id="email_template_subject"
                           class="form-control taggable" placeholder="<?= $s->trans('subject'); ?>"
                           value="<?= Html::encode($body['email_template_subject'] ?? '') ?>">
    </div>

    <div class="mb-3 form-group">
                    <select name="email_template_pdf_template" id="email_template_pdf_template"
                            class="form-control simple-select">
                        <option value=""><?= $s->trans('pdf_template'); ?></option>

                        <optgroup label="<?= $s->trans('invoices'); ?>">
                            <?php foreach ($invoice_templates as $template): ?>
                                <option class="hidden-invoice" value="<?= $template; ?>"
                                    <?php $s->check_select($selected_pdf_template, $template); ?>>
                                    <?= $template; ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>

                        <optgroup label="<?= $s->trans('quotes'); ?>">
                            <?php foreach ($quote_templates as $template): ?>
                                <option class="hidden-quote" value="<?= $template; ?>"
                                    <?php $s->check_select($selected_pdf_template, $template); ?>>
                                    <?= $template; ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
    </div>
    <div class="mb-3 form-group">
                            <br>
                            <div class="html-tags btn-group btn-group-sm">
                                <span class="html-tag btn btn-default" data-tag-type="text-paragraph">
                                    <i class="fa fa-fw fa-paragraph"></i>
                                </span>
                                <span class="html-tag btn btn-default" data-tag-type="text-linebreak">
                                    &lt;br&gt;
                                </span>
                                <span class="html-tag btn btn-default" data-tag-type="text-bold">
                                    <i class="fa fa-fw fa-bold">b</i>
                                </span>
                                <span class="html-tag btn btn-default" data-tag-type="text-italic">
                                    <i class="fa fa-fw fa-italic"></i>
                                </span>
                            </div>
                            <div class="html-tags btn-group btn-group-sm">
                                <span class="html-tag btn btn-default" data-tag-type="text-h1">H1</span>
                                <span class="html-tag btn btn-default" data-tag-type="text-h2">H2</span>
                                <span class="html-tag btn btn-default" data-tag-type="text-h3">H3</span>
                                <span class="html-tag btn btn-default" data-tag-type="text-h4">H4</span>
                            </div>
                            <div class="html-tags btn-group btn-group-sm">
                                <span class="html-tag btn btn-default" data-tag-type="text-code">
                                    <i class="fa fa-fw fa-code"></i>
                                </span>
                                <span class="html-tag btn btn-default" data-tag-type="text-hr">
                                    &lt;hr/&gt;
                                </span>
                                <span class="html-tag btn btn-default" data-tag-type="text-css">
                                    CSS
                                </span>
                            </div>

                            <textarea name="email_template_body" id="email_template_body" rows="8"
                                      class="email-template-body form-control taggable"><?= Html::encode($body['email_template_body'] ?? '') ?>
                            </textarea>

                            <br>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <?= $s->trans('preview'); ?>
                                    <span id="email-template-preview-reload" class="pull-right cursor-pointer">
                                        <i class="fa fa-refresh"></i>
                                    </span>
                                </div>
                                <div class="panel-body">
                                    <iframe id="email-template-preview"></iframe>
                                </div>
                            </div>

    </div>
    <div class="mb-3 form-group">
       <?php
            $response = $tag->renderPartial('invoice/emailtemplate/template-tags',['s'=>$s]);
            echo (string)$response->getBody();
       ?>  
    </div>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>

<?php
$js = <<< 'SCRIPT'
    $(function () {
        var email_template_type = "<?php echo $body['email_template_type']; ?>";
        var $email_template_type_options = $("[name=email_template_type]");
        $email_template_type_options.click(function () {
            // remove class "show" and deselect any selected elements.
            $(".show").removeClass("show").parent("select").each(function () {
                this.options.selectedIndex = 0;
            });
            // add show class to corresponding class
            $(".hidden-" + $(this).val()).addClass("show");
        });
        if (email_template_type === "") {
            $email_template_type_options.first().click();
        } else {
            $email_template_type_options.each(function () {
                if ($(this).val() === email_template_type) {
                    $(this).click();
                }
            });
        }
    });
    $(document).ready(function() {
    	// find the type of template that has been loaded and enable/disable
        // the invoice and quote selects as required
        var inputValue = $('input[type="radio"]:checked').attr("value");
        if (inputValue === 'quote') {
            $('#tags_invoice').prop('disabled', 'disabled');
            $('#tags_quote').prop('disabled', false);
        } else {
            // inputValue === 'invoice'
            $('#tags_invoice').prop('disabled', false);
            $('#tags_quote').prop('disabled', 'disabled');
        }
        // if the radio input for 'type of template' gets clicked, check the
        // new value and enable/disable the invoice and quote selects as required.
    	$('input[type="radio"]').click(function() {
            var inputValue = $(this).attr("value");
            if (inputValue === 'quote') {
            	$('#tags_invoice').prop('disabled', 'disabled');
            	$('#tags_quote').prop('disabled', false);
            } else {
                // inputValue === 'invoice'
            	$('#tags_invoice').prop('disabled', false);
            	$('#tags_quote').prop('disabled', 'disabled');
            }
        });
    });
    SCRIPT;
      $this->registerJsFile($js,3);
?>