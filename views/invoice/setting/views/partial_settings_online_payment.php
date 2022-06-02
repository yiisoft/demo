<?php
    declare(strict_types=1);
?>
<div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <?= $s->trans('online_payments'); ?>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="checkbox">
                        <?php $body['settings[enable_online_payments]'] = $s->get_setting('enable_online_payments');?>
                        <label>
                            <input type="hidden" name="settings[enable_online_payments]" value="0">
                            <input type="checkbox" name="settings[enable_online_payments]" value="1"
                                <?php $s->check_select($body['settings[enable_online_payments]'], 1, '==', true) ?>>
                            <?= $s->trans('enable_online_payments'); ?>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="online-payment-select">
                        <?= $s->trans('add_payment_provider'); ?>
                    </label>
                    <select id="online-payment-select" class="form-control">
                        <option value=""><?= $s->trans('none'); ?></option>
                        <?php foreach ($gateway_drivers as $driver => $fields) {
                            $d = strtolower($driver);
                            ?>
                            <option value="<?= $d; ?>">
                                <?= ucwords(str_replace('_', ' ', $driver)); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

            </div>
        </div>

        <?php
        foreach ($gateway_drivers as $driver => $fields) :
            $d = strtolower($driver);
            ?>
            <div id="gateway-settings-<?= $d; ?>"
                class="gateway-settings panel panel-default <?= $s->get_setting('gateway_' . $d . '_enabled') ? 'active-gateway' : 'hidden'; ?>">

                <div class="panel-heading">
                    <?= ucwords(str_replace('_', ' ', $driver)); ?>
                    <div class="pull-right">
                        <div class="checkbox no-margin">
                            <label>
                                <?php $body['settings[gateway_' . $d . '_enabled]'] = $s->get_setting('gateway_' . $d . '_enabled');?>
                                <input type="hidden" name="settings[gateway_<?= $d; ?>_enabled]" value="0">
                                <input type="checkbox" name="settings[gateway_<?= $d; ?>_enabled]" value="1"
                                    id="settings[gateway_<?= $d; ?>_enabled]"
                                    <?php $s->check_select($body['settings[gateway_' . $d . '_enabled]'], 1, '==', true) ?>>
                                <?= $s->trans('enabled'); ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="panel-body small">

                    <?php foreach ($fields as $key => $setting) { ?>
                        <?php $body['settings[gateway_' . $d . '_'.$key.']'] = $s->get_setting('gateway_' . $d . '_' . $key);?>
                        <?php if ($setting['type'] == 'checkbox') : ?>

                            <div class="checkbox">
                                <label>                                    
                                    <input type="hidden" name="settings[gateway_<?= $d; ?>_<?= $key ?>]"
                                        value="0">
                                    <input type="checkbox" name="settings[gateway_<?= $d; ?>_<?= $key ?>]"
                                        value="1"
                                        <?php $s->check_select($body['settings[gateway_' . $d . '_'.$key.']'], 1, '==', true) ?>>
                                    <?= $s->trans('online_payment_' . $key, '', $setting['label']); ?>
                                </label>
                            </div>

                        <?php else : ?>

                            <div class="form-group">
                                <label for="settings[gateway_<?= $d; ?>_<?= $key ?>]">
                                    <?= $s->trans('online_payment_' . $key, '', $setting['label']); ?>
                                </label>
                                <input type="<?= $setting['type']; ?>" class="input-sm form-control"
                                    name="settings[gateway_<?= $d; ?>_<?= $key ?>]"
                                    id="settings[gateway_<?= $d; ?>_<?= $key ?>]"
                                    <?php
                                        $show_crypt ='';
                                        if ($setting['type'] == 'password') : ?>
                                        <?php
                                            $driver_key = $s->get_setting('gateway_' . $d . '_' . $key);
                                            if (null!==($driver_key) && !empty($driver_key)){
                                               $show_crypt = $crypt->deCryptByKey($driver_key,$decrypt_key,'');
                                            }
                                        ?>
                                        value="<?= $show_crypt; ?>"
                                    <?php else : ?>
                                        value="<?= $body['settings[gateway_' . $d . '_'.$key.']']; ?>"
                                    <?php endif; ?>
                                >
                                <?php if ($setting['type'] == 'password') : ?>
                                    <input type="hidden" value="1"
                                        name="settings[gateway_<?= $d . '_' . $key ?>_field_is_password]">
                                <?php endif; ?>
                            </div>

                        <?php endif; ?>
                    <?php } ?>

                    <hr>

                    <div class="form-group">
                        <label for="settings[gateway_<?= $d; ?>_currency]">
                            <?= $s->trans('currency'); ?>
                        </label>
                        <?php $body['settings[gateway_' . $d . '_currency]'] = $s->get_setting('gateway_' . $d . '_currency');?>
                        <select name="settings[gateway_<?= $d; ?>_currency]"
                            id="settings[gateway_<?= $d; ?>_currency]"
                            class="input-sm form-control">
                            <?php foreach ($gateway_currency_codes as $val => $key) { ?>
                                <option value="<?= $val; ?>"
                                    <?php $s->check_select($body['settings[gateway_' . $d . '_currency]'], $val); ?>>
                                    <?= $val; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[gateway_<?= $d; ?>_payment_method]">
                            <?= $s->trans('online_payment_method'); ?>
                        </label>
                        <?php $body['settings[gateway_' . $d . '_payment_method]'] = $s->get_setting('gateway_' . $d . '_payment_method');?>
                        <select name="settings[gateway_<?= $d; ?>_payment_method]"
                            id="settings[gateway_<?= $d; ?>_payment_method]"
                            class="input-sm form-control">
                            <option value=""><?= $s->trans('none'); ?></option>
                            <?php foreach ($payment_methods as $payment_method) { ?>
                                <option value="<?= $payment_method->getId(); ?>"
                                    <?php $s->check_select($body['settings[gateway_' . $d . '_payment_method]'], $payment_method->getId()) ?>>
                                    <?= $payment_method->getName(); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                </div>

            </div>
        <?php endforeach; ?>

    </div>
</div>
