<?php
    declare(strict_types=1);
?>
<div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <?= $s->trans('quote'); ?>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[default_quote_group]">
                                <?= $s->trans('default_quote_group'); ?>
                            </label>
                            <?php $body['settings[default_quote_group]'] = $s->get_setting('default_quote_group');?>
                            <select name="settings[default_quote_group]" id="settings[default_quote_group]"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <option value=""><?= $s->trans('none'); ?></option>
                                <?php foreach ($invoice_groups as $invoice_group) { ?>
                                    <option value="<?= $invoice_group->getId(); ?>"
                                        <?php $s->check_select($body['settings[default_quote_group]'], $invoice_group->getId()); ?>>
                                        <?= $invoice_group->getName(); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[default_quote_notes]">
                                <?= $s->trans('default_quote_notes'); ?>
                            </label>
                            <?php $body['settings[default_quote_notes]'] = $s->get_setting('default_quote_notes','',true);?>
                            <textarea name="settings[default_quote_notes]" id="settings[default_quote_notes]" rows="3"
                                class="form-control"><?= $body['settings[default_quote_notes]']; ?></textarea>
                        </div>

                    </div>
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[quotes_expire_after]">
                                <?= $s->trans('quotes_expire_after'); ?>
                            </label>
                            <?php $body['settings[quotes_expire_after]'] = $s->get_setting('quotes_expire_after');?>
                            <input type="number" name="settings[quotes_expire_after]" id="settings[quotes_expire_after]"
                                class="form-control"
                                value="<?= $body['settings[quotes_expire_after]']; ?>">
                        </div>

                        <div class="form-group">
                            <label for="settings[generate_quote_number_for_draft]">
                                <?= $s->trans('generate_quote_number_for_draft'); ?>
                            </label>                            
                            <?php $body['settings[generate_quote_number_for_draft]'] = $s->get_setting('generate_quote_number_for_draft');?>
                            <select name="settings[generate_quote_number_for_draft]" class="form-control"
                                id="settings[generate_quote_number_for_draft]" data-minimum-results-for-search="Infinity">
                                <option value="0">
                                    <?= $s->trans('no'); ?>
                                </option>
                                <option value="1" <?php $s->check_select($body['settings[generate_quote_number_for_draft]'], '1'); ?>>
                                    <?= $s->trans('yes'); ?>
                                </option>
                            </select>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <?= $s->trans('pdf_settings'); ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[mark_quotes_sent_pdf]">
                                <?= $s->trans('mark_quotes_sent_pdf'); ?>
                            </label>
                            <?php $body['settings[mark_quotes_sent_pdf]'] = $s->get_setting('mark_quotes_sent_pdf');?>
                            <select name="settings[mark_quotes_sent_pdf]" id="settings[mark_quotes_sent_pdf]"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <option value="0">
                                    <?= $s->trans('no'); ?>
                                </option>
                                <option value="1" <?php $s->check_select($body['settings[mark_quotes_sent_pdf]'], '1'); ?>>
                                    <?= $s->trans('yes'); ?>
                                </option>
                            </select>
                        </div>

                    </div>
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[quote_pre_password]">
                                <?= $s->trans('quote_pre_password'); ?>
                            </label>
                            <?php $body['settings[quote_pre_password]'] = $s->get_setting('quote_pre_password','',true);?>
                            <input type="text" name="settings[quote_pre_password]" id="settings[quote_pre_password]"
                                class="form-control" value="<?= $body['settings[quote_pre_password]']; ?>">
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <?= $s->trans('quote_templates'); ?>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[pdf_quote_template]">
                                <?= $s->trans('pdf_quote_template'); ?>
                            </label>                            
                            <?php $body['settings[pdf_quote_template]'] = $s->get_setting('pdf_quote_template');?>
                            <select name="settings[pdf_quote_template]" id="settings[pdf_quote_template]"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <option value=""><?= $s->trans('none'); ?></option>
                                <?php foreach ($pdf_quote_templates as $quote_template) { ?>
                                    <option value="<?= $quote_template; ?>"
                                        <?php $s->check_select($body['settings[pdf_quote_template]'], $quote_template); ?>>
                                        <?= $quote_template; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="settings[public_quote_template]">
                                <?= $s->trans('public_quote_template'); ?>
                            </label>                            
                            <?php $body['settings[public_quote_template]'] = $s->get_setting('public_quote_template');?>
                            <select name="settings[public_quote_template]" id="settings[public_quote_template]"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <option value=""><?= $s->trans('none'); ?></option>
                                <?php foreach ($public_quote_templates as $quote_template) { ?>
                                    <option value="<?= $quote_template; ?>"
                                        <?php $s->check_select($body['settings[public_quote_template]'], $quote_template); ?>>
                                        <?= $quote_template; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[email_quote_template]">
                                <?= $s->trans('default_email_template'); ?>
                            </label>                                                        
                            <?php $body['settings[email_quote_template]'] = $s->get_setting('email_quote_template');?>
                            <select name="settings[email_quote_template]" id="settings[email_quote_template]"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <option value=""><?= $s->trans('none'); ?></option>
                                <?php foreach ($email_templates_quote as $email_template) { ?>
                                    <option value="<?= $email_template->getEmail_template_id(); ?>"
                                        <?php $s->check_select($body['settings[email_quote_template]'], $email_template->getEmail_template_id()); ?>>
                                        <?= $email_template->getEmail_template_title(); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[pdf_quote_footer]">
                                <?= $s->trans('pdf_quote_footer'); ?>
                            </label>                                                                                    
                            <?php $body['settings[pdf_quote_footer]'] = $s->get_setting('pdf_quote_footer', '', true);?>
                            <textarea name="settings[pdf_quote_footer]" id="settings[pdf_quote_footer]"
                                class="form-control no-margin"><?= $body['settings[pdf_quote_footer]']; ?></textarea>
                            <p class="help-block"><?= $s->trans('pdf_quote_footer_hint'); ?></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
