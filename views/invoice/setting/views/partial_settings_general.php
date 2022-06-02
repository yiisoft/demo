<?php
    declare(strict_types=1);
?>
<div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <?= $s->trans('general'); ?>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[default_language]">
                                <?= $s->trans('language'); ?>
                            </label>
                            <?php $body['settings[default_language]'] = $s->get_setting('default_language'); ?>
                            <select name="settings[default_language]" id="settings[default_language]"
                                class="form-control">
                                <?php foreach ($languages as $language) {
                                    ?>
                                    <option value="<?= $language; ?>" <?php $s->check_select($body['settings[default_language]'], $language) ?>>
                                        <?= ucfirst($language); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[first_day_of_week]">
                                <?= $s->trans('first_day_of_week'); ?>
                            </label>
                            <?php $body['settings[first_day_of_week]'] = $s->get_setting('first_day_of_week'); ?>
                            <select name="settings[first_day_of_week]" id="settings[first_day_of_week]"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <?php foreach ($first_days_of_weeks as $first_day_of_week_id => $first_day_of_week_name) { ?>
                                    <option value="<?= $first_day_of_week_id; ?>"
                                        <?php
                                            $s->check_select($body['settings[first_day_of_week]'], $first_day_of_week_id); 
                                        ?>>
                                        <?= $first_day_of_week_name; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[date_format]">
                                <?= $s->trans('date_format'); ?>
                            </label>
                            <?php   $body['settings[date_format]'] = $s->get_setting('date_format'); ?>
                            <select name="settings[date_format]" id="settings[date_format]"
                                class="form-control">
                                <?php foreach ($date_formats as $date_format) { ?>
                                    <option value="<?= $date_format['setting']; ?>"
                                        <?php  $s->check_select($body['settings[date_format]'], $date_format['setting']); ?>>
                                        <?= $current_date->format($date_format['setting']); ?>
                                        (<?= $date_format['setting'] ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[default_country]">
                                <?= $s->trans('default_country'); ?>
                            </label>
                            <?php   $body['settings[default_country]'] = $s->get_setting('default_country'); ?>
                            <select name="settings[default_country]" id="settings[default_country]"
                                class="form-control">
                                <option value=""><?= $s->trans('none'); ?></option>
                                <?php foreach ($countries as $cldr => $country) { ?>
                                    <option value="<?= $cldr; ?>" 
                                        <?php
                                            $s->check_select($body['settings[default_country]'], $cldr); ?>>
                                        <?= $country ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="default_list_limit">
                                <?= $s->trans('default_list_limit'); ?>
                            </label>
                            <?php $body['settings[default_list_limit]'] = $s->get_setting('default_list_limit', 15, true); ?>
                            <input type="number" name="settings[default_list_limit]" id="default_list_limit"
                                class="form-control" minlength="1" min="1" required
                                value="<?= $body['settings[default_list_limit]']; ?>">
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <div class="panel panel-default">
            <div class="panel-heading">
                <?= $s->trans('amount_settings'); ?>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[currency_symbol]">
                                <?= $s->trans('currency_symbol'); ?>
                            </label>
                            <?php 
                                $body['settings[currency_symbol]'] = $s->get_setting('currency_symbol', '', true);
                            ?>
                            <input type="text" name="settings[currency_symbol]" id="settings[currency_symbol]"
                                class="form-control"
                                value="<?= $body['settings[currency_symbol]']; ?>">
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[currency_symbol_placement]">
                                <?= $s->trans('currency_symbol_placement'); ?>
                            </label>
                            <?php   $body['settings[currency_symbol_placement]'] = $s->get_setting('currency_symbol_placement'); ?>
                            <select name="settings[currency_symbol_placement]" id="settings[currency_symbol_placement]"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <option value="before" 
                                    <?php   
                                        $s->check_select($body['settings[currency_symbol_placement]'], 'before'); 
                                    ?>>
                                    <?= $s->trans('before_amount'); ?>
                                </option>
                                <option value="after" <?php $s->check_select($body['settings[currency_symbol_placement]'], 'after'); ?>>
                                    <?= $s->trans('after_amount'); ?>
                                </option>
                                <option value="afterspace" <?php $s->check_select($body['settings[currency_symbol_placement]'], 'afterspace'); ?>>
                                    <?= $s->trans('after_amount_space'); ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[currency_code]">
                                <?= $s->trans('currency_code'); ?>
                            </label>
                            <?php $body['settings[currency_code]'] = $s->get_setting('currency_code', '', true); ?>
                            <select name="settings[currency_code]"
                                id="settings[currency_code]"
                                class="input-sm form-control">
                                <?php foreach ($gateway_currency_codes as $val => $key) { ?>
                                    <option value="<?= $val; ?>"
                                        <?php
                                            $s->check_select($body['settings[currency_code]'], $val); 
                                        ?>>
                                        <?= $val; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="tax_rate_decimal_places">
                                <?= $s->trans('tax_rate_decimal_places'); ?>
                            </label>
                            <?php   $body['settings[tax_rate_decimal_places]'] = $s->get_setting('tax_rate_decimal_places'); ?>
                            <select name="settings[tax_rate_decimal_places]" class="form-control"
                                id="tax_rate_decimal_places" data-minimum-results-for-search="Infinity">
                                <option value="2" 
                                    <?php 
                                        $s->check_select($body['settings[tax_rate_decimal_places]'], '2'); 
                                    ?>>
                                    2
                                </option>
                                <option value="3" 
                                    <?php
                                        $s->check_select($body['settings[tax_rate_decimal_places]'], '3');                                         
                                    ?>>
                                    3
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[number_format]">
                                <?= $s->trans('number_format'); ?>
                            </label>
                            <?php   $body['settings[number_format]'] = $s->get_setting('number_format'); ?>
                            <select name="settings[number_format]" id="settings[number_format]"
                                class="form-control"
                                data-minimum-results-for-search="Infinity">
                                <?php foreach ($number_formats as $key => $value) { ?>
                                    <option value="<?php print($key); ?>"
                                        <?php
                                            $s->check_select($body['settings[number_format]'], $value['label']); 
                                        ?>>
                                        <?= $s->trans($value['label']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <div class="panel panel-default">
            <div class="panel-heading">
                <?= $s->trans('dashboard'); ?>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[quote_overview_period]">
                                <?= $s->trans('quote_overview_period'); ?>
                            </label>
                            <?php $body['settings[quote_overview_period]'] = $s->get_setting('quote_overview_period'); ?>
                            <select name="settings[quote_overview_period]" id="settings[quote_overview_period]"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <option value="this-month" <?php $s->check_select($body['settings[quote_overview_period]'], 'this-month'); ?>>
                                    <?= $s->trans('this_month'); ?>
                                </option>
                                <option value="last-month" <?php $s->check_select($body['settings[quote_overview_period]'], 'last-month'); ?>>
                                    <?= $s->trans('last_month'); ?>
                                </option>
                                <option value="this-quarter" <?php $s->check_select($body['settings[quote_overview_period]'], 'this-quarter'); ?>>
                                    <?= $s->trans('this_quarter'); ?>
                                </option>
                                <option value="last-quarter" <?php $s->check_select($body['settings[quote_overview_period]'], 'last-quarter'); ?>>
                                    <?= $s->trans('last_quarter'); ?>
                                </option>
                                <option value="this-year" <?php $s->check_select($body['settings[quote_overview_period]'], 'this-year'); ?>>
                                    <?= $s->trans('this_year'); ?>
                                </option>
                                <option value="last-year" <?php $s->check_select($body['settings[quote_overview_period]'], 'last-year'); ?>>
                                    <?= $s->trans('last_year'); ?>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[invoice_overview_period]">
                                <?= $s->trans('invoice_overview_period'); ?>
                            </label>
                            <?php $body['settings[invoice_overview_period]'] = $s->get_setting('invoice_overview_period'); ?>
                            <select name="settings[invoice_overview_period]" id="settings[invoice_overview_period]"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <option value="this-month" <?php $s->check_select($body['settings[invoice_overview_period]'], 'this-month'); ?>>
                                    <?= $s->trans('this_month'); ?>
                                </option>
                                <option value="last-month" <?php $s->check_select($body['settings[invoice_overview_period]'], 'last-month'); ?>>
                                    <?= $s->trans('last_month'); ?>
                                </option>
                                <option value="this-quarter" <?php $s->check_select($body['settings[invoice_overview_period]'], 'this-quarter'); ?>>
                                    <?= $s->trans('this_quarter'); ?>
                                </option>
                                <option value="last-quarter" <?php $s->check_select($body['settings[invoice_overview_period]'], 'last-quarter'); ?>>
                                    <?= $s->trans('last_quarter'); ?>
                                </option>
                                <option value="this-year" <?php $s->check_select($body['settings[invoice_overview_period]'], 'this-year'); ?>>
                                    <?= $s->trans('this_year'); ?>
                                </option>
                                <option value="last-year" <?php $s->check_select($body['settings[invoice_overview_period]'], 'last-year'); ?>>
                                    <?= $s->trans('last_year'); ?>
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="disable_quickactions">
                                <?= $s->trans('disable_quickactions'); ?>
                            </label>
                            <?php   $body['settings[disable_quickactions]'] = $s->get_setting('disable_quickactions'); ?>
                            <select name="settings[disable_quickactions]" class="form-control"
                                id="disable_quickactions" data-minimum-results-for-search="Infinity">
                                <option value="0">
                                    <?= $s->trans('no'); ?>
                                </option>
                                <option value="1" 
                                <?php
                                    $s->check_select($body['settings[disable_quickactions]'], '1'); 
                                ?>>
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
                <?= $s->trans('interface'); ?>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="disable_sidebar">
                                <?= $s->trans('disable_sidebar'); ?>
                            </label>
                            <?php   $body['settings[disable_sidebar]'] = $s->get_setting('disable_sidebar'); ?>
                            <select name="settings[disable_sidebar]" class="form-control"
                                id="disable_sidebar" data-minimum-results-for-search="Infinity">
                                <option value="0">
                                    <?= $s->trans('no'); ?>
                                </option>
                                <option value="1" 
                                    <?php
                                        $s->check_select($body['settings[disable_sidebar]'], '1'); 
                                    ?>>
                                    <?= $s->trans('yes'); ?>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <label for="settings[custom_title]">
                                <?= $s->trans('custom_title'); ?>
                            </label>
                            <?php $body['settings[custom_title]'] = $s->get_setting('custom_title'); ?>
                            <input type="text" name="settings[custom_title]" id="settings[custom_title]"
                                class="form-control"
                                value="<?= $body['settings[custom_title]']; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="monospace_amounts">
                                <?= $s->trans('monospaced_font_for_amounts'); ?>
                            </label>
                            <?php   $body['settings[monospace_amounts]'] = $s->get_setting('monospace_amounts'); ?>
                            <select name="settings[monospace_amounts]" class="form-control"
                                id="monospace_amounts" data-minimum-results-for-search="Infinity">
                                <option value="0"><?= $s->trans('no'); ?></option>
                                <option value="1" <?php $s->check_select($body['settings[monospace_amounts]'], '1'); ?>>
                                    <?= $s->trans('yes'); ?>
                                </option>
                            </select>
                            <p class="help-block">
                                <?= $s->trans('example'); ?>:
                                <span style="font-family: Monaco, Lucida Console, monospace"><?= $s->format_currency(123456.78); ?></span>
                            </p>
                        </div>

                        <div class="form-group">
                            <label for="login_logo">
                                <?= $s->trans('login_logo'); ?>
                            </label>
                            <?php if ($s->get_setting('login_logo')) { ?>
                                <br/>
                                <img class="personal_logo"
                                    src="<?php //echo base_url(); ?>uploads/<?= $s->get_setting('login_logo'); ?>"><br>
                                <?php //echo anchor('settings/remove_logo/login', $s->trans('remove_logo')); ?><br/>
                            <?php } ?>
                            <input type="file" name="login_logo" id="login_logo" class="form-control"/>
                        </div>

                    </div>
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[reports_in_new_tab]">
                                <?= $s->trans('reports_in_new_tab'); ?>
                            </label>
                            <?php  $body['settings[reports_in_new_tab]'] = $s->get_setting('reports_in_new_tab'); ?>
                            <select name="settings[reports_in_new_tab]" id="settings[reports_in_new_tab]"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <option value="0"><?= $s->trans('no'); ?></option>
                                <option value="1" <?php $s->check_select($body['settings[reports_in_new_tab]'], '1'); ?>>
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
                <?= $s->trans('system_settings'); ?>
            </div>
            <div class="panel-body">

                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[bcc_mails_to_admin]">
                                <?= $s->trans('bcc_mails_to_admin'); ?>
                            </label>
                            <?php   $body['settings[bcc_mails_to_admin]'] = $s->get_setting('bcc_mails_to_admin'); ?>
                            <select name="settings[bcc_mails_to_admin]" id="settings[bcc_mails_to_admin]"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <option value="0"><?= $s->trans('no'); ?></option>
                                <option value="1" <?php $s->check_select($body['settings[bcc_mails_to_admin]'], '1'); ?>>
                                    <?= $s->trans('yes'); ?>
                                </option>
                            </select>

                            <p class="help-block"><?= $s->trans('bcc_mails_to_admin_hint'); ?></p>
                        </div>

                    </div>
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="cron_key">
                                <?= $s->trans('cron_key'); ?>
                            </label>
                            <?php   $body['settings[cron_key]'] = $s->get_setting('cron_key'); ?>
                            <div class="input-group">
                                <input type="text" name="settings[cron_key]" id="cron_key" class="form-control" readonly
                                    value="<?= $body['settings[cron_key]']; ?>">
                                <div class="input-group-text">
                                    <button id="btn_generate_cron_key" type="button" class="btn_generate_cron_key btn btn-primary btn-block">
                                        <i class="fa fa-recycle fa-margin"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
