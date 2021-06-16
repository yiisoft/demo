<div class="panel panel-default">
    <div class="panel-heading"><?= $s->trans('email_template_tags'); ?></div>
    <div class="panel-body">

        <p class="small"><?= $s->trans('email_template_tags_instructions'); ?></p>

        <div class="form-group">
            <label for="tags_client"><?= $s->trans('client'); ?></label>
            <select id="tags_client" class="tag-select form-control">
                <option value="{{{client_name}}}">
                    <?= $s->trans('client_name'); ?>
                </option>
                <option value="{{{client_surname}}}">
                    <?= $s->trans('client_surname'); ?>
                </option>
                <optgroup label="<?= $s->trans('address'); ?>">
                    <option value="{{{client_address_1}}}">
                        <?= $s->trans('street_address'); ?>
                    </option>
                    <option value="{{{client_address_2}}}">
                        <?= $s->trans('street_address_2'); ?>
                    </option>
                    <option value="{{{client_city}}}">
                        <?= $s->trans('city'); ?>
                    </option>
                    <option value="{{{client_state}}}">
                        <?= $s->trans('state'); ?>
                    </option>
                    <option value="{{{client_zip}}}">
                        <?= $s->trans('zip'); ?>
                    </option>
                    <option value="{{{client_country}}}">
                        <?= $s->trans('country'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?= $s->trans('contact_information'); ?>">
                    <option value="{{{client_phone}}}">
                        <?= $s->trans('phone'); ?>
                    </option>
                    <option value="{{{client_fax}}}">
                        <?= $s->trans('fax'); ?>
                    </option>
                    <option value="{{{client_mobile}}}">
                        <?= $s->trans('mobile'); ?>
                    </option>
                    <option value="{{{client_email}}}">
                        <?= $s->trans('email'); ?>
                    </option>
                    <option value="{{{client_web}}}">
                        <?= $s->trans('web_address'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?= $s->trans('tax_information'); ?>">
                    <option value="{{{client_vat_id}}}">
                        <?= $s->trans('vat_id'); ?>
                    </option>
                    <option value="{{{client_tax_code}}}">
                        <?= $s->trans('tax_code'); ?>
                    </option>
                    <option value="{{{client_avs}}}">
                        <?= $s->trans('sumex_ssn'); ?>
                    </option>
                    <option value="{{{client_insurednumber}}}">
                        <?= $s->trans('sumex_insurednumber'); ?>
                    </option>
                    <option value="{{{client_weka}}}">
                        <?= $s->trans('sumex_veka'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?= $s->trans('custom_fields'); ?>">
                    <?php //foreach (//$custom_fields['ip_client_custom'] as $custom) { ?>
                        <option value="{{{<?php //echo 'ip_cf_' . $custom->custom_field_id; ?>}}}">
                            <?php //echo $custom->custom_field_label . ' (ID ' . $custom->custom_field_id . ')'; ?>
                        </option>
                    <?php //} ?>
                </optgroup>
            </select>
        </div>

        <div class="form-group">
            <label for="tags_user"><?= $s->trans('user'); ?></label>
            <select id="tags_user" class="tag-select form-control">
                <option value="{{{user_name}}}">
                    <?= $s->trans('name'); ?>
                </option>
                <option value="{{{user_company}}}">
                    <?= $s->trans('company'); ?>
                </option>
                <optgroup label="<?= $s->trans('address'); ?>">
                    <option value="{{{user_address_1}}}">
                        <?= $s->trans('street_address'); ?>
                    </option>
                    <option value="{{{user_address_2}}}">
                        <?= $s->trans('street_address_2'); ?>
                    </option>
                    <option value="{{{user_city}}}">
                        <?= $s->trans('city'); ?>
                    </option>
                    <option value="{{{user_state}}}">
                        <?= $s->trans('state'); ?>
                    </option>
                    <option value="{{{user_zip}}}">
                        <?= $s->trans('zip'); ?>
                    </option>
                    <option value="{{{user_country}}}">
                        <?= $s->trans('country'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?= $s->trans('contact_information'); ?>">
                    <option value="{{{user_phone}}}">
                        <?= $s->trans('phone'); ?>
                    </option>
                    <option value="{{{user_fax}}}">
                        <?= $s->trans('fax'); ?>
                    </option>
                    <option value="{{{user_mobile}}}">
                        <?= $s->trans('mobile'); ?>
                    </option>
                    <option value="{{{user_email}}}">
                        <?= $s->trans('email'); ?>
                    </option>
                    <option value="{{{user_web}}}">
                        <?= $s->trans('web_address'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?= $s->trans('sumex_information'); ?>">
                    <option value="{{{user_subscribernumber}}}">
                        <?= $s->trans('user_subscriber_number'); ?>
                    </option>
                    <option value="{{{user_iban}}}">
                        <?= $s->trans('user_iban'); ?>
                    </option>
                    <option value="{{{user_gln}}}">
                        <?= $s->trans('gln'); ?>
                    </option>
                    <option value="{{{user_rcc}}}">
                        <?= $s->trans('sumex_rcc'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?///= $s->trans('custom_fields'); ?>">
                    <?php ///foreach ($custom_fields['ip_user_custom'] as $custom) { ?>
                        <option value="{{{<?php/// echo 'ip_cf_' . $custom->custom_field_id; ?>}}}">
                            <?php/// echo $custom->custom_field_label . ' (ID ' . $custom->custom_field_id . ')'; ?>
                        </option>
                    <?php/// } ?>
                </optgroup>
            </select>
        </div>

        <div class="form-group">
            <label for="tags_invoice"><?= $s->trans('invoices'); ?></label>
            <select id="tags_invoice" class="tag-select form-control">
                <option value="{{{invoice_number}}}">
                    <?= $s->trans('id'); ?>
                </option>
                <option value="{{{invoice_status}}}">
                    <?= $s->trans('status'); ?>
                </option>
                <optgroup label="<?= $s->trans('invoice_dates'); ?>">
                    <option value="{{{invoice_date_due}}}">
                        <?= $s->trans('due_date'); ?>
                    </option>
                    <option value="{{{invoice_date_created}}}">
                        <?= $s->trans('invoice_date'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?= $s->trans('invoice_amounts'); ?>">
                    <option value="{{{invoice_item_subtotal}}}">
                        <?= $s->trans('subtotal'); ?>
                    </option>
                    <option value="{{{invoice_item_tax_total}}}">
                        <?= $s->trans('invoice_tax'); ?>
                    </option>
                    <option value="{{{invoice_total}}}">
                        <?= $s->trans('total'); ?>
                    </option>
                    <option value="{{{invoice_paid}}}">
                        <?= $s->trans('total_paid'); ?>
                    </option>
                    <option value="{{{invoice_balance}}}">
                        <?= $s->trans('balance'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?= $s->trans('extra_information'); ?>">
                    <option value="{{{invoice_terms}}}">
                        <?= $s->trans('invoice_terms'); ?>
                    </option>
                <option value="{{{invoice_guest_url}}}">
                    <?= $s->trans('guest_url'); ?>
                </option>
<!--                 <option value="{{{payment_method}}}"> -->
<!--                     <?= $s->trans('payment_method'); ?> -->
<!--                 </option> -->
                </optgroup>

                <optgroup label="<?///= $s->trans('custom_fields'); ?>">
                    <?php ///foreach ($custom_fields['ip_invoice_custom'] as $custom) { ?>
                        <option value="{{{<?php/// echo 'ip_cf_' . $custom->custom_field_id; ?>}}}">
                            <?php ///echo $custom->custom_field_label . ' (ID ' . $custom->custom_field_id . ')'; ?>
                        </option>
                    <?php ///} ?>
                </optgroup>
            </select>
        </div>

        <div class="form-group">
            <label for="tags_quote"><?= $s->trans('quotes'); ?></label>
            <select id="tags_quote" class="tag-select form-control">
                <option value="{{{quote_number}}}">
                    <?= $s->trans('id'); ?>
                </option>
                <optgroup label="<?= $s->trans('quote_dates'); ?>">
                    <option value="{{{quote_date_created}}}">
                        <?= $s->trans('quote_date'); ?>
                    </option>
                    <option value="{{{quote_date_expires}}}">
                        <?= $s->trans('expires'); ?>
                    </option>
                </optgroup>
                <optgroup label="<?= $s->trans('quote_amounts'); ?>">
                    <option value="{{{quote_item_subtotal}}}">
                        <?= $s->trans('subtot al'); ?>
                    </option>
                    <option value="{{{quote_tax_total}}}">
                        <?= $s->trans('quote_tax'); ?>
                    </option>
                    <option value="{{{quote_item_discount}}}">
                        <?= $s->trans('discount'); ?>
                    </option>
                    <option value="{{{quote_total}}}">
                        <?= $s->trans('total'); ?>
                    </option>
                </optgroup>

                <optgroup label="<?= $s->trans('extra_information'); ?>">
                    <option value="{{{quote_guest_url}}}">
                        <?= $s->trans('guest_url'); ?>
                    </option>
                </optgroup>

                <optgroup label="<?= $s->trans('custom_fields'); ?>">
                    <?php ///foreach ($custom_fields['ip_quote_custom'] as $custom) { ?>
                        <option value="{{{<?php ///echo 'ip_cf_' . $custom->custom_field_id; ?>}}}">
                            <?php ///echo $custom->custom_field_label . ' (ID ' . $custom->custom_field_id . ')'; ?>
                        </option>
                    <?php ///} ?>
                </optgroup>
            </select>
        </div>

        <div class="form-group">
            <label for="tags_sumex"><?= $s->trans('invoice_sumex'); ?></label>
            <select id="tags_sumex" class="tag-select form-control">
                <option value="{{{sumex_reason}}}">
                    <?= $s->trans('reason'); ?>
                </option>
                <option value="{{{sumex_diagnosis}}}">
                    <?= $s->trans('invoice_sumex_diagnosis'); ?>
                </option>
                <option value="{{{sumex_observations}}}">
                    <?= $s->trans('sumex_observations'); ?>
                </option>
                <option value="{{{sumex_treatmentstart}}}">
                    <?= $s->trans('treatment_start'); ?>
                </option>
                <option value="{{{sumex_treatmentend}}}">
                    <?= $s->trans('treatment_end'); ?>
                </option>
                <option value="{{{sumex_casedate}}}">
                    <?= $s->trans('case_date'); ?>
                </option>
                <option value="{{{sumex_casenumber}}}">
                    <?= $s->trans('case_number'); ?>
                </option>
            </select>
        </div>

    </div>
</div>
