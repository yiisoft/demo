<?php
   declare(strict_types=1);
      
   use App\Widget\OffsetPagination;
   use Yiisoft\Html\Html;
   /**    
    * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator
    * @var \Yiisoft\Session\Flash\FlashInterface $flash 
    */
?>
<div id="headerbar">
    <h1 class="headerbar-title"><?= $s->trans('email_templates'); ?></h1>
    <div class="headerbar-item pull-right">
        <a class="btn btn-sm btn-primary" href="<?php $urlGenerator->generate('emailtemplate/add'); ?>">
            <i class="fa fa-plus"></i> <?= $s->trans('new'); ?>
        </a>
    </div>
    <div class="headerbar-item pull-right">
        <?php
            $pagination = OffsetPagination::widget()
            ->paginator($paginator)
            ->urlGenerator(fn ($page) => $urlGenerator->generate('emailtemplate/index', ['page' => $page]));
        ?>
        <?php
            if ($pagination->isRequired()) {
                 echo $pagination;
            }
        ?>
    </div>
</div>
<div id="content" class="table-content">
    
<?php 
        $danger = $flash->get('danger');
        if ($danger != null) {
            $alert =  Alert::widget()
            ->body($danger)
            ->options(['class' => ['alert-danger shadow'],])
            ->render();
            echo $alert;
        }
        $info = $flash->get('info');
        if ($info != null) {
            $alert =  Alert::widget()
            ->body($info)
            ->options(['class' => ['alert-info shadow'],])
            ->render();
            echo $alert;
        }
        $warning = $flash->get('warning');
        if ($warning != null) {
            $alert =  Alert::widget()
            ->body($warning)
            ->options(['class' => ['alert-warning shadow'],])
            ->render();
            echo $alert;
        }
?>        
</div>
<div>
    <table class="table table-hover table-striped">
        <thead>
        <tr>
            <th><?= $s->trans('title'); ?></th>
            <th><?= $s->trans('type'); ?></th>
            <th><?= $s->trans('options'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($email_templates as $email_template) { ?>
            <tr>
                <td><?= Html::encode($email_template->getEmail_template_title()); ?></td>
                <td><?= $stringUtil->stringify($email_template->getEmail_template_type()); ?></td>
                <td>
                    <div class="options btn-group">
                        <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#"><i
                                    class="fa fa-cog"></i> <?= $s->trans('options'); ?></a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="<?= $urlGenerator->generate('emailtemplate/edit',['email_template_id'=>$email_template->getEmail_template_id()]); ?>" style="text-decoration: none ">
                                    <i class="fa fa-edit fa-margin"></i><?= $s->trans('edit'); ?>
                                </a>
                            </li>
                            <li>
                                <form action="<?= $urlGenerator->generate('emailtemplate/delete',['email_template_id'=>$email_template->getEmail_template_id()]); ?>"
                                      method="POST" style="text-decoration: none">
                                    <?php $csrf; ?>
                                    <button type="submit" class="dropdown-button"
                                            onclick="return confirm('<?= $s->trans('delete_record_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i> <?= $s->trans('delete'); ?>
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
