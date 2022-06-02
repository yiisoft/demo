<?php
    declare(strict_types=1);
?>
<div class="row">
    <div class="col-xs-12 col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= $s->trans('email'); ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <label for="settings[email_pdf_attachment]">
                                <?= $s->trans('email_pdf_attachment'); ?>
                            </label>
                            <?php $body['settings[email_pdf_attachment]'] = $s->get_setting('email_pdf_attachment'); ?>
                            <select name="settings[email_pdf_attachment]" id="settings[email_pdf_attachment]"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <option value="0"><?= $s->trans('no'); ?></option>
                                <option value="1" <?php $s->check_select($body['settings[email_pdf_attachment]'], '1'); ?>>
                                    <?= $s->trans('yes'); ?>
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="email_send_method">
                                <?= $s->trans('email_send_method'); ?>
                            </label>
                            <select name="settings[email_send_method]" id="email_send_method"
                                class="form-control" data-minimum-results-for-search="Infinity">
                                <option value=""><?= $s->trans('none'); ?></option>
                                <option value="phpmail" <?php 
                                   $body['settings[email_send_method]'] = $s->get_setting('email_send_method');
                                   $s->check_select($body['settings[email_send_method]'], 'phpmail'); ?>>
                                    <?= $s->trans('email_send_method_phpmail'); ?>
                                </option>
                                <option value="sendmail" <?php $s->check_select($body['settings[email_send_method]'], 'sendmail'); ?>>
                                    <?= $s->trans('email_send_method_sendmail'); ?>
                                </option>
                                <option value="smtp" <?php $s->check_select($body['settings[email_send_method]'], 'smtp'); ?>>
                                    <?= $s->trans('email_send_method_smtp'); ?>
                                </option>
                            </select>
                        </div>

                        <div id="div-smtp-settings">
                            <hr>

                            <div class="form-group">
                                <label for="settings[smtp_server_address]">
                                    <?= $s->trans('smtp_server_address'); ?>
                                </label>
                                <?php $body['settings[smtp_server_address]'] = $s->get_setting('smtp_server_address', '', true); ?>
                                <input type="text" name="settings[smtp_server_address]" id="settings[smtp_server_address]"
                                    class="form-control"
                                    value="<?= $body['settings[smtp_server_address]']; ?>">
                            </div>

                            <div class="form-group">
                                <label for="settings[smtp_mail_from]">
                                    <?= $s->trans('smtp_mail_from'); ?>
                                </label>
                                <?php $body['settings[smtp_mail_from]'] = $s->get_setting('smtp_mail_from', '', true); ?>
                                <input type="email" name="settings[smtp_mail_from]" id="settings[smtp_mail_from]"
                                    class="form-control"
                                    value="<?= $body['settings[smtp_mail_from]']; ?>">
                            </div>

                            <div class="form-group">
                                <label for="settings[smtp_authentication]">
                                    <?= $s->trans('smtp_requires_authentication'); ?>
                                </label>
                                <?php $body['settings[smtp_authentication]'] = $s->get_setting('smtp_authentication'); ?>
                                <select name="settings[smtp_authentication]" id="settings[smtp_authentication]"
                                    class="form-control">
                                    <option value="0">
                                        <?= $s->trans('no'); ?>
                                    </option>
                                    <option value="1" <?php $s->check_select($body['settings[smtp_authentication]'], '1'); ?>>
                                        <?= $s->trans('yes'); ?>
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="settings[smtp_username]">
                                    <?= $s->trans('smtp_username'); ?>
                                </label>
                                <?php $body['settings[smtp_username]'] = $s->get_setting('smtp_username', '', true); ?>
                                <input type="text" name="settings[smtp_username]" id="settings[smtp_username]"
                                    class="form-control"
                                    value="<?= $body['settings[smtp_username]']; ?>">
                            </div>

                            <div class="form-group">
                                <label for="smtp_password">
                                    <?= $s->trans('smtp_password'); ?>
                                </label>
                                <!-- see MailerHelper and Yiisoft/security/Crypt decrypt -->
                                <?php $body['settings[smtp_password]'] = $s->get_setting('settings[smtp_password]'); ?>
                                <?php
                                   if (null!==($s->get_setting('settings[smtp_password]')) && !empty($s->get_setting('settings[smtp_password]'))){
                                      $show_crypt = $crypt->deCryptByKey($body['settings[smtp_password]'],$decrypt_key,'');
                                   }
                                ?>
                                <input type="password" id="smtp_password" class="form-control"
                                    name="settings[smtp_password]"                                    
                                    value="<?= $show_crypt ?? ''; ?>">
                                <input type="hidden" name="settings[smtp_password_field_is_password]" value="1">
                            </div>

                            <div class="form-group">
                                <div>
                                    <label for="settings[smtp_port]">
                                        <?= $s->trans('smtp_port'); ?>
                                    </label>
                                    <?php $body['settings[smtp_port]'] = $s->get_setting('settings[smtp_port]'); ?>
                                    <input type="number" name="settings[smtp_port]" id="settings[smtp_port]"
                                        class="form-control"
                                        value="<?= $body['settings[smtp_port]']; ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="settings[smtp_security]">
                                    <?= $s->trans('smtp_security'); ?>
                                </label>
                                <?php $body['settings[smtp_security]'] = $s->get_setting('settings[smtp_security]'); ?>
                                <select name="settings[smtp_security]" id="settings[smtp_security]"
                                    class="form-control">
                                    <option value=""><?= $s->trans('none'); ?></option>
                                    <option value="ssl" <?php $s->check_select($body['settings[smtp_security]'], 'ssl'); ?>>
                                        <?= $s->trans('smtp_ssl'); ?>
                                    </option>
                                    <option value="tls" <?php $s->check_select($body['settings[smtp_security]'], 'tls'); ?>>
                                        <?= $s->trans('smtp_tls'); ?>
                                    </option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="settings[smtp_verify_certs]">
                                    <?= $s->trans('smtp_verify_certs'); ?>
                                </label>
                                <?php $body['settings[smtp_verify_certs]'] = $s->get_setting('settings[smtp_verify_certs]'); ?>
                                <select name="settings[smtp_verify_certs]" id="settings[smtp_verify_certs]"
                                    class="form-control">
                                    <option value="1"><?= $s->trans('yes'); ?></option>
                                    <option value="0" <?php $s->check_select($body['settings[smtp_verify_certs]'], '0'); ?>>
                                        <?= $s->trans('no'); ?>
                                    </option>
                                </select>
                            </div>

                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
