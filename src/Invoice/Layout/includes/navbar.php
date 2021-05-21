<?php
  use yii\helpers\Url;
  use yii\bootstrap4\NavBar;
  use frontend\modules\invoice\application\components\Utilities;
?>
<?php NavBar::begin(['options' => ['class' => 'navbar navbar-inverse navbar-expand-sm', 'role'=>'navigation','id'=>'ip-navbar-collapse']]);?>
       <div class="navbar-header">
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#ip-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <?= Utilities::trans('menu') ?> &nbsp; <i class="fa fa-bars"></i>
            </button>
        </div>   
        <div class="collapse navbar-collapse" id="ip-navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="#">
                        <span class="hidden-md"><?= Utilities::trans('dashboard');?></span>
                        <span class="visible-md-inline-block"><?= Utilities::trans('dashboard');?></span>
                    </a> 
                </li>
                <li hidden class="dropdown">
                    <a hidden href="#" data-toggle="dropdown">
                        <i hidden class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php echo Utilities::trans('clients'); ?></span>
                        <i hidden class="visible-md-inline fa fa-users"></i>
                    </a>
                    <ul hidden class="dropdown-menu">
                        <li hidden><a hidden class="dropdown-item" href="#"><?= Utilities::trans('add_client'); ?></a></li>
                        <li hidden><a class="dropdown-item" href="#"><?= Utilities::trans('view_clients'); ?></a></li>
                    </ul>
                </li>

                <li hidden class="dropdown">
                    <a hidden href="#" data-toggle="dropdown">
                        <i hidden class="fa fa-caret-down"></i> &nbsp;
                        <span hidden class="hidden-md"><?php echo Utilities::trans('quotes'); ?></span>
                        <i hidden class="visible-md-inline fa fa-file"></i>
                    </a>
                    <ul hidden class="dropdown-menu">
                        <li hidden><a class="dropdown-item" href="#"><?php echo Utilities::trans('create_quote'); ?></a></li>
                        <li hidden><a class="dropdown-item" href="#"><?php echo Utilities::trans('view_quotes'); ?></a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php echo Utilities::trans('invoices'); ?></span>
                        <i class="visible-md-inline fa fa-file-text"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li hidden><a href="#"><?php echo Utilities::trans('create_invoice'); ?></a></li>
                        <li><a href="<?php echo Url::toRoute(['salesinvoice/index']); ?>"><?php echo Utilities::trans('view_invoices'); ?></a></li>
                        <li hidden><a href="#"><?php echo Utilities::trans('view_recurring_invoices'); ?></a></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php echo Utilities::trans('payments'); ?></span>
                        <i class="visible-md-inline fa fa-credit-card"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li hidden><a href="#"><?php echo Utilities::trans('enter_payment'); ?></a></li>
                        <li hidden><a href="#"><?php echo Utilities::trans('view_payments'); ?></a></li>
                        <li><a href="<?php echo Url::toRoute(['ip/merchantresponselist']); ?>"><?php echo Utilities::trans('view_payment_logs'); ?></a></li>
                    </ul>
                </li>

                <li hidden class="dropdown">
                    <a hidden href="#" data-toggle="dropdown">
                        <i hidden class="fa fa-caret-down"></i> &nbsp;
                        <span hidden class="hidden-md"><?php echo Utilities::trans('products'); ?></span>
                        <i hidden class="visible-md-inline fa fa-database"></i>
                    </a>
                    <ul hidden class="dropdown-menu">
                        <li hidden><a href="<?php echo Url::toRoute(['/product/create']); ?>"><?php echo Utilities::trans('create_product'); ?></a></li>
                        <li hidden><a href="<?php echo Url::toRoute(['/product/index']); ?>"><?php echo Utilities::trans('view_products'); ?></a></li>
                        <li hidden><a href="<?php echo Url::toRoute(['/productsubcategory/index']); ?>"><?php echo Utilities::trans('view_product_families'); ?></a></li>
                        <li hidden><a href="<?php echo Url::toRoute(['/product/index']); ?>"><?php echo Utilities::trans('view_product_units'); ?></a></li>
                    </ul>
                </li>

                <li hidden class="dropdown <?php echo $mdl_settings->get_setting('projects_enabled') == 1 ?: 'hidden'; ?>">
                    <a hidden href="#" data-toggle="dropdown">
                        <i hidden class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?= Utilities::trans('tasks'); ?></span>
                        <i hidden class="visible-md-inline fa fa-check-square-o"></i>
                    </a>
                    <ul hidden class="dropdown-menu">
                        <li hidden><a hidden href="#"><?php echo Utilities::trans('create_task'); ?></a></li>
                        <li hidden><a hidden href="#"><?php echo Utilities::trans('view_tasks'); ?></a></li>
						<li role="separator" class="divider"></li>
                        <li hidden><a hidden href="#"><?php echo Utilities::trans('create_project'); ?></a></li>
                        <li hidden><a hidden href="#"><?php echo Utilities::trans('view_projects'); ?></a></li>
                    </ul>
                </li>

                <li hidden class="dropdown">
                    <a hidden href="#" data-toggle="dropdown">
                        <i hidden class="fa fa-caret-down"></i> &nbsp;
                        <span hidden class="hidden-md"><?php echo Utilities::trans('reports'); ?></span>
                        <i hidden class="visible-md-inline fa fa-bar-chart"></i>
                    </a>
                    <ul hidden class="dropdown-menu">
                        <li hidden><a href="#"><?php echo Utilities::trans('invoice_aging'); ?></a></li>
                        <li hidden><a href="#"><?php echo Utilities::trans('payment_history'); ?></a></li>
                        <li hidden><a href="#"><?php echo Utilities::trans('sales_by_client'); ?></a></li>
                        <li hidden><a href="#"><?php echo Utilities::trans('sales_by_date'); ?></a></li>
                    </ul>
                </li>
            </ul>

            <?php if (isset($filter_display) and $filter_display == true) { ?>
                <?php $this->layout->load_view('filter/jquery_filter'); ?>
                <form class="navbar-form navbar-left" role="search" onsubmit="return false;">
                    <div class="form-group">
                        <input id="filter" type="text" class="search-query form-control input-sm"
                               placeholder="<?php echo $filter_placeholder; ?>">
                    </div>
                </form>
            <?php } ?>

            <ul class="nav navbar-nav navbar-right">
                <li hidden>
                    <a hidden href="https://wiki.invoiceplane.com/" target="_blank"
                       class="tip icon" title="<?php echo Utilities::trans('documentation'); ?>"
                       data-placement="bottom">
                        <i class="fa fa-question-circle"></i>
                        <span class="visible-xs">&nbsp;<?php echo Utilities::trans('documentation'); ?></span>
                    </a>
                </li>

                <li class="dropdown">
                    <a href="#" class="tip icon" data-toggle="dropdown"
                       title="<?= Utilities::trans('settings'); ?>"
                       data-placement="bottom">
                        <i class="fa fa-cogs"></i>
                        <span class="visible-xs">&nbsp;<?php echo Utilities::trans('settings'); ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li hidden><a href="#"><?php echo Utilities::trans('custom_fields'); ?></a></li>
                        <li><a href="<?php echo Url::toRoute(['ip/formlist']); ?>"><?php echo Utilities::trans('email_templates'); ?></a></li>
                        <li hidden><a href="#"><?php echo Utilities::trans('invoice_groups'); ?></a></li>
                        <li hidden><a href="#"><?php echo Utilities::trans('invoice_archive'); ?></a></li>
                        <!-- // temporarily disabled
                        <li><?php echo Utilities::trans('item_lookups'); ?></li>
                        -->
                        <li><a href="<?php echo Url::toRoute(['ip/paymentmethodlist']); ?>"><?php echo Utilities::trans('payment_methods'); ?></a></li>
                        <li hidden><a href="#"><?php echo Utilities::trans('tax_rates'); ?></a></li>
                        <li hidden><a href="#"><?php echo Utilities::trans('user_accounts'); ?></a></li>
                        <li class="divider hidden-xs hidden-sm"></li>
                        <li><a href="<?php echo Url::toRoute(['ip/settings']); ?>"><?php echo Utilities::trans('system_settings'); ?></a></li>                        
                    </ul>
                </li>
                <li>
                    <a href="<?php echo Url::toRoute(['/site/index']); ?>"
                       class="tip icon logout" data-placement="bottom"
                       title="<?= Utilities::trans('menu'); ?>">
                        <i class="fa fa-home"></i>
                        <span class="visible-xs">&nbsp;<?= Utilities::trans('menu'); ?></span>
                    </a>
                </li>
            </ul>
        </div>
<?php NavBar::end(); ?>