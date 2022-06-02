<?php

declare(strict_types=1); 

use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;

/**
 * @var \Yiisoft\View\View $this
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
 * @var array $body
 * @var string $csrf
 */
?>
<form method="post">

    <input type="hidden" name="_csrf" value="<?= $csrf; ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?= $s->trans('custom_values'); ?></h1>

        <div class="headerbar-item pull-right">
            <div class="btn-group btn-group-sm">
                <a class="btn btn-default" href="<?= $urlGenerator->generate('customfield/index'); ?>">
                    <i class="fa fa-arrow-left"></i> <?= $s->trans('back'); ?>
                </a>
                <a class="btn btn-primary" href="<?= $urlGenerator->generate('customvalue/new',['id'=> $custom_field_id]) ?>">
                    <i class="fa fa-plus"></i> <?= $s->trans('new'); ?>
                </a>
            </div>
        </div>
    </div>

    <div id="content">

        <?php 
                    if (!empty($errors)) {
                        foreach ($errors as $field => $error) {
                            echo Alert::widget()->options(['class' => 'alert-danger'])->body(Html::encode($field . ':' . $error));
                        }
                    } 
        ?>

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">

                <div class="form-group">
                    <label for="label"><?= $s->trans('field'); ?>: </label>
                    <input type="text" name="label" id="label" class="form-control"
                           value="<?= Html::encode($custom_field->getLabel()); ?>" disabled="disabled">
                </div>

                <div class="form-group">
                    <label for="types"><?= $s->trans('type'); ?>: </label>
                    <select name="types" id="types" class="form-control"
                            disabled="disabled">
                        <?php foreach ($custom_values_types as $type): ?>
                            <?= $alpha = str_replace('-', '_', strtolower($type)); ?>
                            <option value="<?= $type; ?>" <?= $s->check_select($custom_field->getType(), $type); ?>>
                                <?= $s->trans($alpha); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th><?= $s->trans('id'); ?></th>
                            <th><?= $s->trans('label'); ?></th>
                            <th><?= $s->trans('options'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($custom_values as $custom_value) { ?>
                            <tr>
                                <td><?= $custom_value->getId(); ?></td>
                                <td><?= Html::encode($custom_value->getValue()); ?></td>
                                <td>
                                    <div class="options btn-group">
                                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                           href="#">
                                            <i class="fa fa-cog"></i> <?= $s->trans('options'); ?>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="<?= $urlGenerator->generate('customvalue/edit',['id'=>$custom_value->getId()]); ?>" style="text-decoration:none">
                                                    <i class="fa fa-edit fa-margin"></i> <?= $s->trans('edit'); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= $urlGenerator->generate('customvalue/delete',['id' =>$custom_value->getId()]); ?>" style="text-decoration:none" onclick="return confirm('<?= $s->trans('delete_record_warning'); ?>');">
                                                    <i class="fa fa-trash fa-margin"></i><?= $s->trans('delete'); ?>                                    
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    </div>

</form>
