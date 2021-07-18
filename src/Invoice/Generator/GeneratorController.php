<?php

declare(strict_types=1);

namespace App\Invoice\Generator;

use App\Invoice\Entity\Gentor;
use App\Invoice\Generator\GeneratorRepository;
use App\Invoice\GeneratorRelation\GeneratorRelationRepository;
use App\Invoice\Setting\SettingRepository;
use App\Service\WebControllerService;
use App\User\UserService;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Http\Method;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Yiisoft\Session\SessionInterface as Session;
use Yiisoft\Session\Flash\Flash;
use App\Invoice\Helpers\GenerateCodeFileHelper;
use Spiral\Database\DatabaseManager;
use Yiisoft\View\View;
use Exception;

final class GeneratorController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private GeneratorService $generatorService;    
    private UserService $userService;
    const ENTITY = 'Entity.php';
    const REPO = 'Repository.php';
    const FORM = 'Form.php';
    const SERVICE = 'Service.php';
    const MAPPER = 'Mapper.php';
    const SCOPE = 'Scope.php';
    const CONTROLLER = 'Controller.php';
    const INDEX = 'index.php';
    const _FORM = '_form.php';     
    const _VIEW = '_view.php';
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        GeneratorService $generatorService,
        UserService $userService    
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/generator')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->generatorService = $generatorService;
        $this->userService = $userService;
    }

    public function index(Session $session,GeneratorRepository $generatorRepository, SettingRepository $settingRepository): Response
    {
        $canEdit = $this->rbac($session);
        $generators = $this->generators($generatorRepository);
        $parameters = [
            's'=>$settingRepository,
            'canEdit' => $canEdit,
            'generators' => $generators
        ]; 
        return $this->viewRenderer->render('index', $parameters);
    }

    public function add(Session $session, Request $request, SettingRepository $settingRepository,ValidatorInterface $validator, DatabaseManager $dbal): Response
    {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('add'),
            'action' => ['generator/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'tables'=>$dbal->database('default')->getTables(),
            'selected_table'=>'',
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new GeneratorForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->generatorService->saveGenerator(new Gentor(), $form);
                return $this->webService->getRedirectResponse('generator/index');
            }
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }

    public function edit(Session $session, Request $request, GeneratorRepository $generatorRepository, SettingRepository $settingRepository, ValidatorInterface $validator, DatabaseManager $dbal): Response 
    {
        $this->rbac($session);
        $generator = $this->generator($request, $generatorRepository);
        $parameters = [
            'title' => $settingRepository->trans('edit'),
            'action' => ['generator/edit', ['id' => $generator->getGentor_id()]],
            'errors' => [],
            'body' => $this->body($this->generator($request, $generatorRepository)),
            's'=>$settingRepository,
            'tables'=>$dbal->database('default')->getTables(),
            'selected_table'=>$this->generator($request, $generatorRepository)->getPre_entity_table(),
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new GeneratorForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->generatorService->saveGenerator($generator, $form);
                return $this->webService->getRedirectResponse('generator/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFirstErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }
    
    public function delete(Session $session, Request $request, GeneratorRepository $generatorRepository): Response 
    {
        $this->rbac($session);
        $generator = $this->generator($request, $generatorRepository);
        $this->flash($session,'danger','This record has been deleleted.');
        try {
           $this->generatorService->deleteGenerator($generator);
        }
        catch (Exception $e) {
           $flashMsg = $e->getMessage();
           $this->flash($session,'danger','This record cannot be deleleted due to the following error: ' . $flashMsg);
        }
        return $this->webService->getRedirectResponse('generator/index');   
    }
    
    public function view(Session $session,Request $request,GeneratorRepository $generatorRepository, SettingRepository $settingRepository,ValidatorInterface $validator): Response {
        $this->rbac($session);        
        $generator = $this->generator($request, $generatorRepository);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['generator/view', ['id' => $generator->getGentor_id()]],
            'errors' => [],
            'generator'=>$this->generator($request,$generatorRepository),
            's'=>$settingRepository,     
            'body' => $this->body($this->generator($request, $generatorRepository)),            
            'selected_table'=>$this->generator($request, $generatorRepository)->getPre_entity_table(),            
        ];
        return $this->viewRenderer->render('__view', $parameters);
    }
    
    //$canEdit = $this->rbac();
    private function rbac(Session $session) {
        $canEdit = $this->userService->hasPermission('editGenerator');
        if (!$canEdit){
            $this->flash($session,'warning', 'You do not have the required permission.');
            return $this->webService->getRedirectResponse('generator/index');
        }
        return $canEdit;
    }
    
    //$generator = $this->generator();
    private function generator(Request $request, GeneratorRepository $generatorRepository){
        $id = $request->getAttribute('id');
        $generator = $generatorRepository->repogeneratorquery($id);
        if ($generator === null) {
            return $this->webService->getNotFoundResponse();
        }        
        return $generator; 
    }
    
    //$generators = $this->generators();
    private function generators(GeneratorRepository $generatorRepository){
        $generators = $generatorRepository->findAllPreloaded();
        if ($generators === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $generators;
    }
    
    //$this->flash
    private function flash(Session $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
    
    private function body($generator) {
        $body = [
                'route_prefix' => $generator->getRoute_prefix(),
                'route_suffix' => $generator->getRoute_suffix(),
                'camelcase_capital_name' => $generator->getCamelcase_capital_name(),
                'small_singular_name' => $generator->getSmall_singular_name(),
                'small_plural_name' => $generator->getSmall_plural_name(),
                'namespace_path' => $generator->getNamespace_path(),
                'controller_layout_dir' => $generator->getController_layout_dir(),
                'controller_layout_dir_dot_path' => $generator->getController_layout_dir_dot_path(),
                'repo_extra_camelcase_name' => $generator->getRepo_extra_camelcase_name(),
                'paginator_next_page_attribute' => $generator->getPaginator_next_page_attribute(),
                'pre_entity_table' => $generator->getPre_entity_table(),
                'created_include' => $generator->isCreated_include(),
                'modified_include' => $generator->isModified_include(),
                'updated_include' => $generator->isUpdated_include(),
                'deleted_include' => $generator->isDeleted_include(),
                'constrain_index_field'=> $generator->getConstrain_index_field(),
                'keyset_paginator_include' => $generator->isKeyset_paginator_include(),
                'offset_paginator_include' => $generator->isOffset_paginator_include(),            
                'flash_include' => $generator->isFlash_include(),
                'headerline_include' => $generator->isHeaderline_include(),
        ];
        return $body;
    }
    
    public function entity(Session $session,Request $request, GeneratorRepository $gr, 
                             SettingRepository $settingRepository, GeneratorRelationRepository $grr,
                             ValidatorInterface $validator, DatabaseManager $dbal, View $view
                            ): Response {
        $file = self::ENTITY;
        $path = $this->getAliases();
        $g = $this->generator($request, $gr);
        $id = $g->getGentor_id();
        $relations = $grr->findRelations($id);
        $orm = $dbal->database('default')->table($g->getPre_entity_table())->getSchema();
        $content = $this->getContent($view,$g,$relations,$orm,$file);
        $flash = $this->flash($session,'success',$file.' generated at '.$path.'/'.$file);
        $build_file = $this->build_and_save($path,$content,$file);
        $parameters = [
            'canEdit'=>$this->rbac($session),
            's'=> $settingRepository,
            'title' => 'Generate '.$file,
            'body' => $this->body($g),
            'generator'=> $g,
            'orm_schema'=>$orm,
            'relations'=>$relations,
            'flash'=> $flash,
            'generated'=>$build_file,
        ];
        return $this->viewRenderer->render('__results', $parameters);
    }
    
    public function repo(Session $session,Request $request, GeneratorRepository $gr, 
                             SettingRepository $settingRepository, GeneratorRelationRepository $grr,
                             ValidatorInterface $validator, DatabaseManager $dbal, View $view
                            ): Response {
        $file = self::REPO;
        $path = $this->getAliases();
        $g = $this->generator($request, $gr);
        $id = $g->getGentor_id();
        $relations = $grr->findRelations($id);
        $orm = $dbal->database('default')->table($g->getPre_entity_table())->getSchema();
        $content = $this->getContent($view,$g,$relations,$orm,$file);
        $flash = $this->flash($session,'success',$file.' generated at '.$path.'/'.$file);
        $build_file = $this->build_and_save($path,$content,$file);
        $parameters = [
            'canEdit'=>$this->rbac($session),
            's'=> $settingRepository,
            'title' => 'Generate '.$file,
            'body' => $this->body($g),
            'generator'=> $g,
            'orm_schema'=>$orm,
            'relations'=>$relations,
            'flash'=> $flash,
            'generated'=>$build_file,
        ];
        return $this->viewRenderer->render('__results', $parameters);
    }
    
    public function service(Session $session,Request $request, GeneratorRepository $gr, 
                             SettingRepository $settingRepository, GeneratorRelationRepository $grr,
                             ValidatorInterface $validator, DatabaseManager $dbal, View $view
                            ): Response {
        $file = self::SERVICE;
        $path = $this->getAliases();
        $g = $this->generator($request, $gr);
        $id = $g->getGentor_id();
        $relations = $grr->findRelations($id);
        $orm = $dbal->database('default')->table($g->getPre_entity_table())->getSchema();
        $content = $this->getContent($view,$g,$relations,$orm,$file);
        $flash = $this->flash($session,'success',$file.' generated at '.$path.'/'.$file);
        $build_file = $this->build_and_save($path,$content,$file);
        $parameters = [
            'canEdit'=>$this->rbac($session),
            's'=> $settingRepository,
            'title' => 'Generate '.$file,
            'body' => $this->body($g),
            'generator'=> $g,
            'orm_schema'=>$orm,
            'relations'=>$relations,
            'flash'=> $flash,
            'generated'=>$build_file,
        ];
        return $this->viewRenderer->render('__results', $parameters);
    }
    
    public function form(Session $session,Request $request, GeneratorRepository $gr, 
                             SettingRepository $settingRepository, GeneratorRelationRepository $grr,
                             ValidatorInterface $validator, DatabaseManager $dbal, View $view
                            ): Response {
        $file = self::FORM;
        $path = $this->getAliases();
        $g = $this->generator($request, $gr);
        $id = $g->getGentor_id();
        $relations = $grr->findRelations($id);
        $orm = $dbal->database('default')->table($g->getPre_entity_table())->getSchema();
        $content = $this->getContent($view,$g,$relations,$orm,$file);
        $flash = $this->flash($session,'success',$file.' generated at '.$path.'/'.$file);
        $build_file = $this->build_and_save($path,$content,$file);
        $parameters = [
            'canEdit'=>$this->rbac($session),
            's'=> $settingRepository,
            'title' => 'Generate '.$file,
            'body' => $this->body($g),
            'generator'=> $g,
            'orm_schema'=>$orm,
            'relations'=>$relations,
            'flash'=> $flash,
            'generated'=>$build_file,
        ];
        return $this->viewRenderer->render('__results', $parameters);
    }
        
    public function mapper(Session $session,Request $request, GeneratorRepository $gr, 
                             SettingRepository $settingRepository, GeneratorRelationRepository $grr,
                             ValidatorInterface $validator, DatabaseManager $dbal, View $view
                            ): Response {
        $file = self::MAPPER;
        $path = $this->getAliases();
        $g = $this->generator($request, $gr);
        $id = $g->getGentor_id();
        $relations = $grr->findRelations($id);
        $orm = $dbal->database('default')->table($g->getPre_entity_table())->getSchema();
        $content = $this->getContent($view,$g,$relations,$orm,$file);
        $flash = $this->flash($session,'success',$file.' generated at '.$path.'/'.$file);
        $build_file = $this->build_and_save($path,$content,$file);
        $parameters = [
            'canEdit'=>$this->rbac($session),
            's'=> $settingRepository,
            'title' => 'Generate '.$file,
            'body' => $this->body($g),
            'generator'=> $g,
            'orm_schema'=>$orm,
            'relations'=>$relations,
            'flash'=> $flash,
            'generated'=>$build_file,
        ];
        return $this->viewRenderer->render('__results', $parameters);
    }
    
    public function scope(Session $session,Request $request, GeneratorRepository $gr, 
                             SettingRepository $settingRepository, GeneratorRelationRepository $grr,
                             ValidatorInterface $validator, DatabaseManager $dbal, View $view
                            ): Response {
        $file = self::SCOPE;
        $path = $this->getAliases();
        $g = $this->generator($request, $gr);
        $id = $g->getGentor_id();
        $relations = $grr->findRelations($id);
        $orm = $dbal->database('default')->table($g->getPre_entity_table())->getSchema();
        $content = $this->getContent($view,$g,$relations,$orm,$file);
        $flash = $this->flash($session,'success',$file.' generated at '.$path.'/'.$file);
        $build_file = $this->build_and_save($path,$content,$file);
        $parameters = [
            'canEdit'=>$this->rbac($session),
            's'=> $settingRepository,
            'title' => 'Generate '.$file,
            'body' => $this->body($g),
            'generator'=> $g,
            'orm_schema'=>$orm,
            'relations'=>$relations,
            'flash'=> $flash,
            'generated'=>$build_file,
        ];
        return $this->viewRenderer->render('__results', $parameters);
    }
    
    public function controller(Session $session,Request $request, GeneratorRepository $gr, 
                             SettingRepository $settingRepository, GeneratorRelationRepository $grr,
                             ValidatorInterface $validator, DatabaseManager $dbal, View $view
                            ): Response {
        $file = self::CONTROLLER;
        $path = $this->getAliases();
        $g = $this->generator($request, $gr);
        $id = $g->getGentor_id();
        $relations = $grr->findRelations($id);
        $orm = $dbal->database('default')->table($g->getPre_entity_table())->getSchema();
        $content = $this->getContent($view,$g,$relations,$orm,$file);
        $flash = $this->flash($session,'success',$file.' generated at '.$path.'/'.$file);
        $build_file = $this->build_and_save($path,$content,$file);
        $parameters = [
            'canEdit'=>$this->rbac($session),
            's'=> $settingRepository,
            'title' => 'Generate '.$file,
            'body' => $this->body($g),
            'generator'=> $g,
            'orm_schema'=>$orm,
            'relations'=>$relations,
            'flash'=> $flash,
            'generated'=>$build_file,
        ];
        return $this->viewRenderer->render('__results', $parameters);
    }
        
    public function _index(Session $session,Request $request, GeneratorRepository $gr, 
                             SettingRepository $settingRepository, GeneratorRelationRepository $grr,
                             ValidatorInterface $validator, DatabaseManager $dbal, View $view
                            ): Response {
        $file = self::INDEX;
        $path = $this->getAliases();
        $g = $this->generator($request, $gr);
        $id = $g->getGentor_id();
        $relations = $grr->findRelations($id);
        $orm = $dbal->database('default')->table($g->getPre_entity_table())->getSchema();
        $content = $this->getContent($view,$g,$relations,$orm,$file);
        $flash = $this->flash($session,'success',$file.' generated at '.$path.'/'.$file);
        $build_file = $this->build_and_save($path,$content,$file);
        $parameters = [
            'canEdit'=>$this->rbac($session),
            's'=> $settingRepository,
            'title' => 'Generate '.$file,
            'body' => $this->body($g),
            'generator'=> $g,
            'orm_schema'=>$orm,
            'relations'=>$relations,
            'flash'=> $flash,
            'generated'=>$build_file,
        ];
        return $this->viewRenderer->render('__results', $parameters);
    }
    
    public function _form(Session $session,Request $request, GeneratorRepository $gr, 
                             SettingRepository $settingRepository, GeneratorRelationRepository $grr,
                             ValidatorInterface $validator, DatabaseManager $dbal, View $view
                            ): Response {
        $file = self::_FORM;
        $path = $this->getAliases();
        $g = $this->generator($request, $gr);
        $id = $g->getGentor_id();
        $relations = $grr->findRelations($id);
        $orm = $dbal->database('default')->table($g->getPre_entity_table())->getSchema();
        $content = $this->getContent($view,$g,$relations,$orm,$file);
        $flash = $this->flash($session,'success',$file.' generated at '.$path.'/'.$file);
        $build_file = $this->build_and_save($path,$content,$file);
        $parameters = [
            'canEdit'=>$this->rbac($session),
            's'=> $settingRepository,
            'title' => 'Generate '.$file,
            'body' => $this->body($g),
            'generator'=> $g,
            'orm_schema'=>$orm,
            'relations'=>$relations,
            'flash'=> $flash,
            'generated'=>$build_file,
        ];
        return $this->viewRenderer->render('__results', $parameters);
    }
        
    public function _view(Session $session,Request $request, GeneratorRepository $gr, 
                             SettingRepository $settingRepository, GeneratorRelationRepository $grr,
                             ValidatorInterface $validator, DatabaseManager $dbal, View $view
                            ): Response {
        $file = self::_VIEW;
        $path = $this->getAliases();
        $g = $this->generator($request, $gr);
        $id = $g->getGentor_id();
        $relations = $grr->findRelations($id);
        $orm = $dbal->database('default')->table($g->getPre_entity_table())->getSchema();
        $content = $this->getContent($view,$g,$relations,$orm,$file);
        $flash = $this->flash($session,'success',$file.' generated at '.$path.'/'.$file);
        $build_file = $this->build_and_save($path,$content,$file);
        $parameters = [
            'canEdit'=>$this->rbac($session),
            's'=> $settingRepository,
            'title' => 'Generate '.$file,
            'body' => $this->body($g),
            'generator'=> $g,
            'orm_schema'=>$orm,
            'relations'=>$relations,
            'flash'=> $flash,
            'generated'=>$build_file,
        ];
        return $this->viewRenderer->render('__results', $parameters);
    }
        
    private function getAliases(){
         $view_generator_dir_path = new Aliases([
            '@generators' => dirname(dirname(dirname(__DIR__))).'/views/invoice/generator/templates_protected',
            '@generated' => dirname(dirname(dirname(__DIR__))).'/views/invoice/generator/output_overwrite']);            
         return $view_generator_dir_path->get('@generated');
    }
    
    private function getContent($view,$generator,$relations,$orm_schema,$file){
        return $content = $view->render("//invoice/generator/templates_protected/".$file,['generator' => $generator,
                'relations'=>$relations,
                'orm_schema'=>$orm_schema,
                'body'=>$this->body($generator)]);
    }
    
    private function build_and_save($generated_dir_path,$content, $file){
        $build_file = new GenerateCodeFileHelper("$generated_dir_path/$file", $content); 
        $build_file->save();
        return $build_file;
    }
}
