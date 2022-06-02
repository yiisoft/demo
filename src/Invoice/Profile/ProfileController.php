<?php

declare(strict_types=1); 

namespace App\Invoice\Profile;


use App\Invoice\Company\CompanyRepository;
use App\Invoice\Entity\Profile;
use App\Invoice\Profile\ProfileService;
use App\Invoice\Profile\ProfileRepository;
use App\Invoice\Setting\SettingRepository;
use App\User\UserService;
use App\Service\WebControllerService;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use \Exception;

final class ProfileController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService;
    private ProfileService $profileService;
    private TranslatorInterface $translator;
        
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        UserService $userService,
        ProfileService $profileService,
        TranslatorInterface $translator
    )    
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/profile')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->userService = $userService;
        $this->profileService = $profileService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, ProfileRepository $profileRepository, SettingRepository $settingRepository): Response
    {      
         $canEdit = $this->rbac($session);
         $flash = $this->flash($session, 'info' , 'Create a profile with a new email address, or mobile number, make it active, '.
                 'and select the company details you wish to link it to. This information will automatically appear on the documentation eg. quotes and invoices.');
         $parameters = [
          's'=>$settingRepository,
          'canEdit' => $canEdit,
          'profiles' => $this->profiles($profileRepository),
          'flash'=> $flash
         ];
        
        return $this->viewRenderer->render('index', $parameters);
    }
    
    private function isAjaxRequest(Request $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function add(ViewRenderer $head,SessionInterface $session, Request $request, 
                        ValidatorInterface $validator,
                        SettingRepository $settingRepository,                        
                        CompanyRepository $companyRepository
    )
    {
        $this->rbac($session);
        $parameters = [
            'title' => 'Add',
            'action' => ['profile/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,
            'head'=>$head,            
            'companies'=>$companyRepository->findAllPreloaded(),
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new ProfileForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->profileService->saveProfile(new Profile(),$form);
                return $this->webService->getRedirectResponse('profile/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function edit(ViewRenderer $head, SessionInterface $session, Request $request, CurrentRoute $currentRoute,
                        ValidatorInterface $validator,
                        ProfileRepository $profileRepository, 
                        SettingRepository $settingRepository,                        
                        CompanyRepository $companyRepository
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => 'Edit',
            'action' => ['profile/edit', ['id' => $this->profile($currentRoute, $profileRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->profile($currentRoute, $profileRepository)),
            'head'=>$head,
            's'=>$settingRepository,
            'companies'=>$companyRepository->findAllPreloaded()
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new ProfileForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->profileService->saveProfile($this->profile($currentRoute, $profileRepository), $form);
                return $this->webService->getRedirectResponse('profile/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('_form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, ProfileRepository $profileRepository 
    ): Response {
        $this->rbac($session);
        try {
            if ($this->profileService->deleteProfile($this->profile($currentRoute, $profileRepository))) {               
                $this->flash($session, 'info', 'Deleted.');
                return $this->webService->getRedirectResponse('profile/index');
            } else {
                $this->flash($session, 'info', 'Profile has not been deleted.');
                return $this->webService->getRedirectResponse('profile/index');
            }    
	} catch (Exception $e) {
            unset($e);
            $this->flash($session, 'danger', 'Cannot delete. Profile history exists.');
            return $this->webService->getRedirectResponse('profile/index'); 
        }
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, ProfileRepository $profileRepository,
        SettingRepository $settingRepository,
        ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['profile/view', ['id' => $this->profile($currentRoute, $profileRepository)->getId()]],
            'errors' => [],
            'body' => $this->body($this->profile($currentRoute, $profileRepository)),
            's'=>$settingRepository,             
            'profile'=>$profileRepository->repoProfilequery($this->profile($currentRoute, $profileRepository)->getId()),
        ];
        return $this->viewRenderer->render('_view', $parameters);
    }
        
    private function rbac(SessionInterface $session) 
    {
        $canEdit = $this->userService->hasPermission('editProfile');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('profile/index');
        }
        return $canEdit;
    }
    
    private function profile(CurrentRoute $currentRoute, ProfileRepository $profileRepository) 
    {
        $id = $currentRoute->getArgument('id');       
        $profile = $profileRepository->repoProfilequery($id);
        if ($profile === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $profile;
    }
    
    private function profiles(ProfileRepository $profileRepository) 
    {
        $profiles = $profileRepository->findAllPreloaded();        
        if ($profiles === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $profiles;
    }
    
    private function body($profile) {
        $body = [                
          'id'=>$profile->getId(),
          'company_id'=>$profile->getCompany_id(),
          'current'=>$profile->getCurrent(),
          'mobile'=>$profile->getMobile(),
          'email'=>$profile->getEmail(),
          'description'=>$profile->getDescription(),
          'date_created'=>$profile->getDate_created(),
          'date_modified'=>$profile->getDate_modified()
        ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
}