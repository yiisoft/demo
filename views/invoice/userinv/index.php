<?php
declare(strict_types=1); 
/**
 * @var \App\Invoice\Entity\UserInv $userinv
 * @var \Yiisoft\Router\UrlGeneratorInterface $urlGenerator 
 * @var \Yiisoft\Data\Paginator\OffsetPaginator $paginator
 * @var \Yiisoft\Session\Flash\FlashInterface $flash
 * @var \App\Invoice\Setting\SetttingRepository $s 
 */
use App\Widget\OffsetPagination;
use Yiisoft\Html\Html;
use Yiisoft\Yii\Bootstrap5\Alert;
use Yiisoft\Yii\DataView\Columns\DataColumn;
use Yiisoft\Yii\DataView\GridView;
?>
<div id="headerbar">
    <h1 class="headerbar-title"><?= ' '. $s->trans('users'); ?></h1>    
    <div class="submenu-row">
            <div class="btn-group index-options">
                <a href="<?= $urlGenerator->generate('userinv/index',['page'=>1, 'active'=>2]); ?>"
                   class="btn <?php echo $active == 2 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('all'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('userinv/index',['page'=>1, 'active'=>1]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $active == 1 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('active'); ?>
                </a>
                <a href="<?= $urlGenerator->generate('userinv/index',['page'=>1, 'active'=>0]); ?>" style="text-decoration:none"
                   class="btn  <?php echo $active == 0 ? 'btn-primary' : 'btn-default' ?>">
                    <?= $s->trans('inactive'); ?>
                </a>
                <?= 
                Html::a(
                        Html::tag('i', '', [
                            'class' => 'fa fa-plus'
                        ]), 
                        $urlGenerator->generate('userinv/add'), ['class' => 'btn btn-sm btn-primary']
                        )->render();
                ?>
            </div>
    </div>
    <div class="headerbar-item pull-left">
        <div>
            <?php
                $pagination = OffsetPagination::widget()->paginator($paginator)
                                                        ->urlGenerator(fn ($page) => $urlGenerator->generate('userinv/index', ['page' => $page, 'active'=>$active]));
            ?>
            <?php
                if ($pagination->isRequired()) {
                   echo $pagination;
                }
            ?>
        </div>              
    </div>
</div>
<div>
<?php
    $danger = $flash->get('danger');
    if ($danger != null) {
        $alert =  Alert::widget()
            ->body($danger)
            ->options([
                'class' => ['alert-danger shadow'],
            ])
        ->render();
        echo $alert;
    }
    $info = $flash->get('info');
    if ($info != null) {
        $alert =  Alert::widget()
            ->body($info)
            ->options([
                'class' => ['alert-info shadow'],
            ])
        ->render();
        echo $alert;
    }
    $warning = $flash->get('warning');
    if ($warning != null) {
        $alert =  Alert::widget()
            ->body($warning)
            ->options([
                'class' => ['alert-warning shadow'],
            ])
        ->render();
        echo $alert;
    }
?>
</div>
<div id="content" class="table-content">  
<div class="card shadow">
        <h5 class="card-header bg-primary text-white">
            <i class="bi bi-people"></i><?= $s->trans('users'); ?>
        </h5>
        <?= GridView::widget()
        ->columns([
            [               
                'attribute()' => ['user_id'], 
                'value()' =>[static fn ($model): string => $model->getUser_id()],
            ],
            [               
                'attribute()' => ['id'], 
                'value()' => [static fn ($model): string => $model->getId()],
            ],            
            [               
                'attribute()' => ['active'], 
                'value()' => [static function ($model) use($s): string {
                        return $model->getActive() ? Html::tag('span',$s->trans('yes'),['class'=>'label active'])->render() 
                                                   : Html::tag('span',$s->trans('no'),['class'=>'label inactive'])->render();
                }],
            ],
            [
                
                'header()'=>[$s->trans('user_all_clients')],                
                'attribute()' => ['all_clients'], 
                'value()' => [static function ($model) use($s): string {
                        return $model->getAll_clients() ? Html::tag('span',$s->trans('yes'),['class'=>'label active'])->render()
                                                        : Html::tag('span',$s->trans('no'),['class'=>'label inactive'])->render();
                }],
            ],
            [
                'class' => DataColumn::class,  
                'attribute()' => ['name'], 'value()' => [static function ($model): string {
                        return $model->getName();
                }],
            ],
            [
                'header()'=>[$s->trans('user_type')],
                'attribute()' => ['type'], 
                'value()' => [static function ($model) use ($s): string {
                        $user_types = [
                            0 => $s->trans('administrator'),
                            1 => $s->trans('guest_read_only'),
                        ];  
                        return $user_types[$model->getType()];
                }],
            ],
            [
                'attribute()' => ['email'], 'value()' => [static function ($model): string {
                        return $model->getEmail();
                }],
            ],
            [
                'header()'=>[$s->trans('assigned_clients')],
                'attribute()' => ['type'], 
                'value()' => [static function ($model) use ($urlGenerator): string {
                        // The administrator has access to all clients so assigning clients is only applicable to guest user accounts
                        // Display the button only if the user has a guest account setup not to be confused with Yii's isGuest.
                        // Admin => 0, Guest => 1   not to be confused with admin User Table id which is 1 and UserInv Table user_id is 1.
                        return $model->getType() !== 0 ? Html::a(
                                    Html::tag('i','',['class'=>'fa fa-list fa-margin']),
                                    // UserInv is an extension of table user
                                    // The user_id will be retrieved in the controller not here 
                                    // Just pass the primary key of UserInv here below
                                        $urlGenerator->generate('userinv/client',['id'=>$model->getId()]),
                                        ['class'=>'btn btn-default']            
                                    )->render() : '';
                }],
            ],
            [
                'header()'=>[$s->trans('edit')],
                'attribute()' => ['type'], 
                'value()' => [static function ($model) use ($urlGenerator): string {
                        return $model->getType() == 1 ? Html::a(
                                                            Html::tag('i','',['class'=>'fa fa-edit fa-margin']),
                                                        $urlGenerator->generate('userinv/edit',['id'=>$model->getId()]),[]                                         
                                                        )->render() : '';
                               
                        
                }],
            ],
                       
            [
                'header()'=>[$s->trans('delete')],
                'attribute()' => ['type'], 
                'value()' => [static function ($model) use ($s, $urlGenerator): string {
                        return $model->getType() == 1 ? Html::a( Html::tag('button',
                                                            Html::tag('i','',['class'=>'fa fa-trash fa-margin']),
                                                            [
                                                                'type'=>'submit', 
                                                                'class'=>'dropdown-button',
                                                                'onclick'=>"return confirm("."'".$s->trans('delete_record_warning')."');"
                                                            ]
                                                            ),
                                                        $urlGenerator->generate('userinv/delete',['id'=>$model->getId()]),[]                                         
                                                        )->render() : '';
                               
                        
                }],
            ],            
                        
        ])
        ->currentPage($page)
        ->headOptions(['class'=>'card-header bg-info text-black'])
        ->pageArgument(true)
        ->paginator($paginator)
        ->requestArguments(['sort' => $sortOrder, 'page' => $page])                
        ->rowOptions(['class' => 'align-middle'])
        ->summaryOptions(['class' => 'mt-3 me-3 summary text-end'])
        ->tableOptions(['class' => 'table table-striped text-center h-75','id'=>'table-user-inv'])
        ->showFooter()
        ->showHeader(true)
        ?>
</div>
</div>
