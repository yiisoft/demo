<?php
   echo "<?php\n";             
?>

declare(strict_types=1); 

namespace <?= $generator->getNamespace_path().DIRECTORY_SEPARATOR.$generator->getCamelcase_capital_name(); ?>;

use <?= $generator->getNamespace_path(). DIRECTORY_SEPARATOR. 'Entity'. DIRECTORY_SEPARATOR. $generator->getCamelcase_capital_name(); ?>;
use <?= $generator->getNamespace_path().DIRECTORY_SEPARATOR.$generator->getCamelcase_capital_name().DIRECTORY_SEPARATOR.$generator->getCamelcase_capital_name(); ?>Service;
use <?= $generator->getNamespace_path().DIRECTORY_SEPARATOR.$generator->getCamelcase_capital_name().DIRECTORY_SEPARATOR.$generator->getCamelcase_capital_name(); ?>Repository;
use <?= $generator->getNamespace_path().DIRECTORY_SEPARATOR.$generator->getRepo_extra_camelcase_name().DIRECTORY_SEPARATOR.$generator->getRepo_extra_camelcase_name(); ?>Repository;
<?php foreach ($relations as $relation) { 
    echo 'use ' . $generator->getNamespace_path() .DIRECTORY_SEPARATOR. $relation->getCamelcase_name().DIRECTORY_SEPARATOR.$relation->getCamelcase_name() .'Repository;'."\n"; 
} ?>
use App\User\UserService;
use Yiisoft\Validator\ValidatorInterface;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;

final class <?= $generator->getCamelcase_capital_name(); ?>Controller
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private <?= $generator->getCamelcase_capital_name(); ?>Service $<?= $generator->getSmall_singular_name(); ?>Service;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        <?= $generator->getCamelcase_capital_name(); ?>Service $<?= $generator->getSmall_singular_name(); ?>Service
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('<?= $generator->getRoute_prefix().'/'.$generator->getRoute_suffix(); ?>')
                                           ->withLayout(<?= $generator->getController_layout_dir().".'".$generator->getController_layout_dir_dot_path()."'"; ?>);
        $this->webService = $webService;
        $this->userService = $userService;
        $this-><?= $generator->getSmall_singular_name(); ?>Service = $<?= $generator->getSmall_singular_name(); ?>Service;
    }
    
    public function index(SessionInterface $session, <?= $generator->getCamelcase_capital_name(); ?>Repository $<?= $generator->getSmall_singular_name(); ?>Repository, <?= $generator->getRepo_extra_camelcase_name(); ?>Repository $<?= strtolower($generator->getRepo_extra_camelcase_name()); ?>Repository, Request $request, <?= $generator->getCamelcase_capital_name(); ?>Service $service): Response
    {
      <?php if ($generator->isKeyset_paginator_include()) { 
              echo "\n";
              echo '        $paginator = $service->getFeedPaginator();'."\n";
              echo '        if ($request->getAttribute('."'".$generator->getPaginator_next_page_attribute()."') !== null) {"."\n";
              echo '         $paginator = $paginator->withNextPageToken((string)$request->getAttribute('."'".$generator->getPaginator_next_page_attribute()."'));";
        } ?> 
      <?php
              echo '   $canEdit = $this->rbac($session);'."\n";
              echo '         $flash = $this->flash($session, '."'dummy'"." , 'Flash message!.');"."\n";
              echo '         $parameters = ['."\n";
      ?>      
      <?php if ($generator->getRepo_extra_camelcase_name()) {  
           echo "    's'=>". '$'.lcfirst($generator->getRepo_extra_camelcase_name()).'Repository,'."\n";
           echo "          'canEdit' => ".'$canEdit,'."\n";
           echo "          '".$generator->getSmall_plural_name()."'".' => $'.'this->'.$generator->getSmall_plural_name().'($'.$generator->getSmall_singular_name().'Repository),'."\n"; 
           echo "          'flash'=> ".'$flash'."\n";
           echo "         ];"."\n";
           echo "\n";
        }
      ?>
        if ($this->isAjaxRequest($request)) {
            return $this->viewRenderer->renderPartial('_<?= $generator->getSmall_plural_name(); ?>', ['data' => $paginator]);
        }
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        <?php if ($generator->getRepo_extra_camelcase_name()) {  
                            echo $generator->getRepo_extra_camelcase_name().'Repository '. '$'.lcfirst($generator->getRepo_extra_camelcase_name()).'Repository,';
                        }    
                        ?>
                        <?php
                        $rel = '';
                        echo "\n";
                        foreach ($relations as $relation) {
                            $rel .= '                        '.$relation->getCamelcase_name().'Repository $'.$relation->getLowercase_name().'Repository,'."\n";
                        }
                        echo rtrim($rel,",\n")."\n";        
                        ?>
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['<?= $generator->getSmall_singular_name(); ?>/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            <?php if ($generator->getRepo_extra_camelcase_name()) {  
                echo "'s'=>". '$'.lcfirst($generator->getRepo_extra_camelcase_name()).'Repository,'."\n";
            }
            ?>
            'head'=>$head,
            <?php echo "\n";
            foreach ($relations as $relation) {
                echo "            '".$relation->getLowercase_name()."s'=>".'$'.$relation->getLowercase_name().'Repository->findAllPreloaded(),'."\n";
            }
            ?>
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new <?= $generator->getCamelcase_capital_name(); ?>Form();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this-><?= $generator->getSmall_singular_name(); ?>Service->save<?= $generator->getCamelcase_capital_name(); ?>(new <?= $generator->getCamelcase_capital_name(); ?>(),$form);
                return $this->webService->getRedirectResponse('<?= $generator->getSmall_singular_name(); ?>/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        <?php if ($generator->getCamelcase_capital_name()) {  
                            echo $generator->getCamelcase_capital_name().'Repository '. '$'.$generator->getSmall_singular_name().'Repository,';
                        }
                        ?> 
                        <?php if ($generator->getRepo_extra_camelcase_name()) {  
                            echo $generator->getRepo_extra_camelcase_name().'Repository '. '$'.strtolower($generator->getRepo_extra_camelcase_name()).'Repository,';
                        }
                        ?>
                        <?php
                        $rel = '';
                        echo "\n";
                        foreach ($relations as $relation) {
                            $rel .= '                        '.$relation->getCamelcase_name().'Repository $'.$relation->getLowercase_name().'Repository,'."\n";
                        }
                        echo rtrim($rel,",\n")."\n";
                        ?>
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['<?= $generator->getSmall_singular_name(); ?>/edit', ['id' => $this-><?= $generator->getSmall_singular_name();?>($request, $<?= $generator->getSmall_singular_name(); ?>Repository)->getId()]],
            'errors' => [],
            'body' => $this->body($this-><?= $generator->getSmall_singular_name();?>($request, $<?= $generator->getSmall_singular_name();?>Repository)),
            'head'=>$head,
            <?php if ($generator->getRepo_extra_camelcase_name()) {  
                 echo "'s'=>". '$'.lcfirst($generator->getRepo_extra_camelcase_name()).'Repository,'."\n";
            }
            ?>
            <?php
                $rel = '';
                foreach ($relations as $relation) {
                  $rel .= "            '".$relation->getLowercase_name()."s'=>".'$'.$relation->getLowercase_name().'Repository->findAllPreloaded(),'."\n";
                }
                echo rtrim($rel,",\n")."\n";
            ?>
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new <?= $generator->getCamelcase_capital_name(); ?>Form();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this-><?= $generator->getSmall_singular_name();?>Service->save<?= $generator->getCamelcase_capital_name(); ?>($this-><?= $generator->getSmall_singular_name();?>($request,$<?= $generator->getSmall_singular_name();?>Repository), $form);
                return $this->webService->getRedirectResponse('<?= $generator->getSmall_singular_name(); ?>/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session,Request $request,<?= $generator->getCamelcase_capital_name(); ?>Repository $<?= $generator->getSmall_singular_name();?>Repository 
    ): Response {
        $this->rbac($session);
        $this->flash($session, 'danger','This record has been deleted');
        $this-><?= $generator->getSmall_singular_name();?>Service->delete<?= $generator->getCamelcase_capital_name(); ?>($this-><?= $generator->getSmall_singular_name();?>($request,$<?= $generator->getSmall_singular_name();?>Repository));               
        return $this->webService->getRedirectResponse('<?= $generator->getSmall_singular_name();?>/index');        
    }
    
    public function view(SessionInterface $session,Request $request,<?= $generator->getCamelcase_capital_name(); ?>Repository $<?= $generator->getSmall_singular_name();?>Repository,
        <?php if ($generator->getRepo_extra_camelcase_name()) {  
            echo $generator->getRepo_extra_camelcase_name().'Repository '. '$'.strtolower($generator->getRepo_extra_camelcase_name()).'Repository,'."\n";
        }
        ?>
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['<?= $generator->getSmall_singular_name(); ?>/edit', ['id' => $this-><?= $generator->getSmall_singular_name();?>($request, $<?= $generator->getSmall_singular_name();?>Repository)->getId()]],
            'errors' => [],
            'body' => $this->body($this-><?= $generator->getSmall_singular_name();?>($request, $<?= $generator->getSmall_singular_name();?>Repository)),
            's'=>$settingRepository,             
            '<?= $generator->getSmall_singular_name();?>'=>$<?= $generator->getSmall_singular_name();?>Repository->repo<?= $generator->getCamelcase_capital_name();?>query($this-><?= $generator->getSmall_singular_name();?>($request, $<?= $generator->getSmall_singular_name();?>Repository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
    
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('edit<?= $generator->getCamelcase_capital_name(); ?>');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('<?= $generator->getSmall_singular_name(); ?>/index');
        }
        return $canEdit;
    }
    
    private function <?= $generator->getSmall_singular_name();?>(Request $request,<?= $generator->getCamelcase_capital_name();?>Repository $<?= $generator->getSmall_singular_name();?>Repository) 
    {
        $id = $request->getAttribute('id');       
        $<?= $generator->getSmall_singular_name();?> = $<?= $generator->getSmall_singular_name();?>Repository->repo<?= $generator->getCamelcase_capital_name();?>query($id);
        if ($<?= $generator->getSmall_singular_name();?> === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $<?= $generator->getSmall_singular_name();?>;
    }
    
    private function <?= $generator->getSmall_plural_name();?>(<?= $generator->getCamelcase_capital_name();?>Repository $<?= $generator->getSmall_singular_name();?>Repository) 
    {
        $<?= $generator->getSmall_plural_name();?> = $<?= $generator->getSmall_singular_name();?>Repository->findAllPreloaded();        
        if ($<?= $generator->getSmall_plural_name();?> === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $<?= $generator->getSmall_plural_name();?>;
    }
    
    private function body($<?= $generator->getSmall_singular_name();?>) {
        $body = [
                <?php
                  echo "\n";
                  $bo = '';
                    foreach ($orm_schema->getColumns() as $column) {
                    $bo .= "          '".$column->getName()."'=>$".$generator->getSmall_singular_name()."->get".ucfirst($column->getName())."(),\n";
                  }
                  echo rtrim($bo,",\n")."\n";        
                ?>
                ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}

<?php
   echo "?>";             
?>
