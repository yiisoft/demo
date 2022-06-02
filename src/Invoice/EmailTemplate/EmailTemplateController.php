<?php

declare(strict_types=1);

namespace App\Invoice\EmailTemplate;

use App\Invoice\Entity\EmailTemplate;
use App\Invoice\EmailTemplate\EmailTemplateRepository;
use App\Invoice\CustomField\CustomFieldRepository;
use App\Invoice\EmailTemplate\EmailTemplateForm;
use App\Invoice\Setting\SettingRepository;

use App\Service\WebControllerService;
use App\User\UserService;

use Prophecy\Util\StringUtil;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Yiisoft\Aliases\Aliases;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\Http\Method;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class EmailTemplateController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private EmailTemplateService $emailtemplateService;
    private UserService $userService;
    private TranslatorInterface $translator;

    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        EmailTemplateService $emailtemplateService,
        UserService $userService,
        TranslatorInterface $translator
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/emailtemplate')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->emailtemplateService = $emailtemplateService;
        $this->userService = $userService;
        $this->translator = $translator;
    }
    
    public function index(SessionInterface $session, CurrentRoute $currentRoute, StringUtil $stringUtil,EmailTemplateRepository $emailtemplateRepository, SettingRepository $settingRepository): Response
    {
        $canEdit = $this->rbac($session); 
        $parameters = [              
            'paginator' => (new OffsetPaginator($this->emailtemplates($emailtemplateRepository)))
                            ->withPageSize((int)$settingRepository->setting('default_list_limit'))
                            ->withCurrentPage((int)$currentRoute->getArgument('page', '1')),
            's'=> $settingRepository,
            'canEdit' => $canEdit,
            'email_templates' => $this->emailtemplates($emailtemplateRepository), 
            'flash'=> $this->flash($session,'',''),
            'stringUtil'=>$stringUtil
        ];    
        return $this->viewRenderer->render('index', $parameters);
    }
    
    public function add(ViewRenderer $tag,SessionInterface $session, Request $request, ValidatorInterface $validator, 
                        SettingRepository $settingRepository, 
                        CustomFieldRepository $customfieldRepository,
                        EmailTemplateRepository $emailtemplateRepository,
                        ): Response
    {
        $this->rbac($session); 
        $parameters = [
            'title' => 'Add Email Template',
            'action' => ['emailtemplate/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$settingRepository,            
            'email_template_tags' => $this->viewRenderer->renderPartialAsString('/invoice/emailtemplate/template-tags', [
                    's'=>$settingRepository,
                    'custom_fields' => [                        
                        'client_custom'=>$customfieldRepository->repoTablequery('client_custom'),
                        'quote_custom'=>$customfieldRepository->repoTablequery('quote_custom'),
                        'invoice_custom'=>$customfieldRepository->repoTablequery('invoice_custom'),
                    ],            
            ]),                        
            'invoice_templates'=>$emailtemplateRepository->get_invoice_templates('pdf'),
            'quote_templates'=>$emailtemplateRepository->get_quote_templates('pdf'),
            'selected_pdf_template'=>'',
            'tag'=>$tag
        ];
        
        if ($request->getMethod() === Method::POST) {
            
            $form = new EmailTemplateForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->emailtemplateService->saveEmailTemplate($this->userService->getUser(),new EmailTemplate(),$form);
                return $this->webService->getRedirectResponse('emailtemplate/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('__form', $parameters, );
    }

    public function edit(ViewRenderer $tag,SessionInterface $session, CurrentRoute $currentRoute, Request $request, 
                         EmailTemplateRepository $emailtemplateRepository, 
                         CustomFieldRepository $customfieldRepository,
                         SettingRepository $settingRepository,
                         ValidatorInterface $validator,
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('edit'),
            'action' => ['emailtemplate/edit', ['email_template_id' => $this->emailtemplate($currentRoute, $emailtemplateRepository)->getEmail_template_id()]],
            'errors' => [],
            'email_template'=>$this->emailtemplate($currentRoute, $emailtemplateRepository),
            'body' => $this->body($this->emailtemplate($currentRoute, $emailtemplateRepository)),
            'aliases'=> new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']),
            's'=>$settingRepository,
            'email_template_tags' => $this->viewRenderer->renderPartialAsString('/invoice/emailtemplate/template-tags', [
                    's'=>$settingRepository,
                    'custom_fields' => [                        
                        'client_custom'=>$customfieldRepository->repoTablequery('client_custom'),
                        'quote_custom'=>$customfieldRepository->repoTablequery('quote_custom'),
                        'invoice_custom'=>$customfieldRepository->repoTablequery('invoice_custom'),
                    ],            
            ]),      
            'invoice_templates'=>$emailtemplateRepository->get_invoice_templates('pdf'),
            'quote_templates'=>$emailtemplateRepository->get_quote_templates('pdf'),
            'selected_pdf_template'=>$this->emailtemplate($currentRoute, $emailtemplateRepository)->getEmail_template_pdf_template(),
            'tag'=>$tag
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new EmailTemplateForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->emailtemplateService->saveEmailTemplate($this->userService->getUser(),$this->emailtemplate($currentRoute, $emailtemplateRepository), $form);
                return $this->webService->getRedirectResponse('emailtemplate/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }
    
    public function delete(SessionInterface $session, CurrentRoute $currentRoute, EmailTemplateRepository $emailtemplateRepository 
    ): Response {
        $this->rbac($session);
       
        $this->emailtemplateService->deleteEmailTemplate($this->emailtemplate($currentRoute, $emailtemplateRepository));               
        return $this->webService->getRedirectResponse('emailtemplate/index');        
    }
    
    public function view(SessionInterface $session, CurrentRoute $currentRoute, EmailTemplateRepository $emailtemplateRepository, ValidatorInterface $validator,SettingRepository $settingRepository   
    ): Response {
        $this->rbac($session);
        $parameters = [
            'title' => $settingRepository->trans('view'),
            'action' => ['emailtemplate/edit', ['email_template_id' => $this->emailtemplate($currentRoute, $emailtemplateRepository)->getEmail_template_id()]],
            'errors' => [],
            'emailtemplate'=>$this->emailtemplate($currentRoute, $emailtemplateRepository),
            'body' => $this->body($this->emailtemplate($currentRoute, $emailtemplateRepository)),
            'aliases'=>new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']),
            's'=>$settingRepository,
        ];
        return $this->viewRenderer->render('__view', $parameters); 
    }
    
    private function rbac(SessionInterface $session) {
        $canEdit = $this->userService->hasPermission('editEmailTemplate');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('emailtemplate/index');
        }
        return $canEdit;
    }
    
    private function emailtemplate(CurrentRoute $currentRoute, EmailTemplateRepository $emailtemplateRepository) {
        $email_template_id = $currentRoute->getArgument('email_template_id');       
        $emailtemplate = $emailtemplateRepository->repoEmailTemplatequery($email_template_id);
        if ($emailtemplate === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $emailtemplate;
    }
    
    private function emailtemplates(EmailTemplateRepository $emailtemplateRepository) {
        $emailtemplates = $emailtemplateRepository->findAllPreloaded();        
        if ($emailtemplates === null) {
            return $this->webService->getNotFoundResponse();
        };
        return $emailtemplates;
    }
    
    private function body($emailtemplate) {
        $body = [
                'email_template_title'=>$emailtemplate->getEmail_template_title(),
                'email_template_type'=>$emailtemplate->getEmail_template_type(),
                'email_template_body'=>$emailtemplate->getEmail_template_body(),
                'email_template_subject'=>$emailtemplate->getEmail_template_subject(),
                'email_template_from_name'=>$emailtemplate->getEmail_template_from_name(),
                'email_template_from_email'=>$emailtemplate->getEmail_template_from_email(),
                'email_template_cc'=>$emailtemplate->getEmail_template_cc(),
                'email_template_bcc'=>$emailtemplate->getEmail_template_bcc(),
                'email_template_pdf_template'=>$emailtemplate->getEmail_template_pdf_template(),
        ];
        return $body;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    } 
}
