<?php
declare(strict_types=1);

namespace App\Invoice\Setting;
// App
use App\Invoice\Entity\Setting;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\EmailTemplate\EmailTemplateRepository as ER;
use App\Invoice\Group\GroupRepository as GR;
use App\Invoice\PaymentMethod\PaymentMethodRepository as PM;
use App\Invoice\Helpers\DateHelper;
use App\Invoice\Helpers\CountryHelper;
use App\Invoice\Helpers\CurrencyHelper;
use App\Invoice\TaxRate\TaxRateRepository as TR;
use App\Invoice\Libraries\Sumex;
use App\Service\WebControllerService;
use App\User\UserService;
// Yii
use Yiisoft\Aliases\Aliases;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Data\Paginator\OffsetPaginator;
use Yiisoft\DataResponse\DataResponseFactoryInterface;
use Yiisoft\Files\FileHelper;
use Yiisoft\Http\Method;
use Yiisoft\Json\Json;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Security\Crypt;
use Yiisoft\Security\Random;
use Yiisoft\Session\SessionInterface as Session;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface as Translator;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;
// Psr
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
// Miscellaneous
use \DateTime;

final class SettingController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private SettingService $settingService;
    private Translator $translator;
    private UserService $userService;    
    private DataResponseFactoryInterface $factory;
    private SettingRepository $s;    
    private const DECRYPT_KEY = 'dyhkdicYUiU';
    private string $decrypt_key = self::DECRYPT_KEY;
    
    public function __construct(
        ViewRenderer $viewRenderer,
        WebControllerService $webService,
        SettingService $settingService,
        Translator $translator,
        UserService $userService,
        DataResponseFactoryInterface $factory,
        Session $session,
        SettingRepository $s,    
    ) {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice/setting')
                                           ->withLayout(dirname(dirname(__DIR__)).'/Invoice/Layout/main.php');
        $this->webService = $webService;
        $this->settingService = $settingService;
        $this->translator = $translator;
        $this->userService = $userService;
        $this->factory = $factory;
        $this->session = $session;
        $this->s = $s;
    }
    
    public function debug_index(CurrentRoute $currentRoute): Response
    {  
        $pageNum = (int)$currentRoute->getArgument('page', '1');
        $paginator = (new OffsetPaginator($this->settings($this->s)))
        ->withPageSize((int)$this->s->setting('default_list_limit'))
        ->withCurrentPage($pageNum);
        $canEdit = $this->rbac(); 
        $parameters = [
              'paginator' => $paginator,
              's'=>$this->s,
              'canEdit' => $canEdit,
              'flash'=>$this->flash($this->session, '', ''),
              'settings' => $this->settings($this->s),
              'session'=>$this->session,
              'trans'=>$this->translator->translate('invoice.setting.translator.key'),
              'section'=>$this->translator->translate('invoice.setting.section'),
              'subsection'=>$this->translator->translate('invoice.setting.subsection'),
        ];
        return $this->viewRenderer->render('debug_index', $parameters);
    }
    
    public function tab_index(Request $request, ValidatorInterface $validator, ViewRenderer $head, ER $eR, GR $gR, PM $pm, SettingRepository $sR, TR $tR) : Response {
        $crypt = new Crypt();
        $aliases = new Aliases(['@invoice' => dirname(__DIR__), '@language' => '@invoice/Language']);
        $datehelper = new DateHelper($this->s);
        $countries = new CountryHelper();               
        $parameters = [
            'defat'=> $sR->withKey('default_language'),
            'action'=>['setting/tab_index'],
            'flash' => $this->flash($this->session,'',''),
            's'=> $this->s,
            'head' => $head,
            'body'=> $request->getParsedBody(),
            'general'=>$this->viewRenderer->renderPartialAsString('/invoice/setting/views/partial_settings_general',[
                's'=>$this->s,
                'languages'=> ArrayHelper::map($this->s->expandDirectoriesMatrix($aliases->get('@language'), 0),'name','name'),     
                'first_days_of_weeks'=>['0' => $this->s->lang('sunday'), '1' => $this->s->lang('monday')],
                'date_formats'=>$datehelper->date_formats(),
                'countries'=>$countries->get_country_list($this->session->get('_language')),
                'gateway_currency_codes'=>CurrencyHelper::all(),
                'number_formats'=>$this->s->number_formats(),
                'current_date'=>new DateTime(),
            ]),
            'invoices'=>$this->viewRenderer->renderPartialAsString('/invoice/setting/views/partial_settings_invoices',[
                's'=>$this->s,
                'invoice_groups'=>$gR->findAllPreloaded(),
                'payment_methods'=>$pm->findAllPreloaded(),
                'public_invoice_templates'=>$this->s->get_invoice_templates('public'),
                'pdf_invoice_templates'=>$this->s->get_invoice_templates('pdf'),
                'email_templates_invoice'=>$eR->repoEmailTemplateType('invoice'),
                'roles' => Sumex::ROLES,
                'places' => Sumex::PLACES,
                'cantons' =>Sumex::CANTONS,
            ]),
            'quotes'=>$this->viewRenderer->renderPartialAsString('/invoice/setting/views/partial_settings_quotes',[
                's'=>$this->s,
                'invoice_groups'=>$gR->findAllPreloaded(),
                'public_quote_templates'=>$this->s->get_quote_templates('public'),
                'pdf_quote_templates'=>$this->s->get_quote_templates('pdf'),
                'email_templates_quote'=>$eR->repoEmailTemplateType('quote'),
            ]),
            'taxes'=>$this->viewRenderer->renderPartialAsString('/invoice/setting/views/partial_settings_taxes',[
                's'=>$this->s,
                'tax_rates'=>$tR->findAllPreloaded(),
            ]),
            'email'=>$this->viewRenderer->renderPartialAsString('/invoice/setting/views/partial_settings_email',[
                's'=>$this->s,
                'crypt'=>$crypt,
                'decrypt_key'=>$this->decrypt_key
            ]),
            'online_payment'=>$this->viewRenderer->renderPartialAsString('/invoice/setting/views/partial_settings_online_payment',[
                's'=>$this->s,
                'gateway_drivers'=>$this->s->payment_gateways(),
                'gateway_currency_codes'=>CurrencyHelper::all(),
                'payment_methods'=>$pm->findAllPreloaded(),
            ]),
            'projects_tasks'=>$this->viewRenderer->renderPartialAsString('/invoice/setting/views/partial_settings_projects_tasks',[
                's'=>$this->s,
            ]),
        ];
        if ($request->getMethod() === Method::POST) {
            $body = $parameters['body'];
            foreach ($body['settings'] as $key => $value) {
                $key = ltrim(rtrim($key));
                $value = ltrim(rtrim($value));
                if ($sR->repoCount((string)$key)) {                   
                    $setting = $sR->withKey($key);
                    $setting->setSetting_value((string)$value);
                    $sR->save($setting);
                }
                else {
                   // The key does not exist because the repoCount is not greater than zero
                   $this->tab_index_debug_mode_ensure_all_settings_included(true, $key, $value, $validator);
                }
            }
            $this->flash($this->session, 'info', $this->s->trans('settings_successfully_saved'));
            return $this->webService->getRedirectResponse('setting/tab_index');
        }
        return $this->viewRenderer->render('tab_index', $parameters);        
    }
    
    public function tab_index_debug_mode_ensure_all_settings_included($bool, $key, $value, $validator) {
        // The setting does not exist because repoCount is not greater than 0;
        if ($bool) {
            // Make sure the setting is available to be set in the database if there is no such like setting in the database
            $form = new SettingForm();
            $array = [
                'setting_key'=>$key,
                'setting_value'=>$value,
                'setting_trans'=>'',
                'setting_section'=>'',
                'setting_subsection'=>''
            ];
            if ($form->load($array) && $validator->validate($form)->isValid()) {
                $this->settingService->saveSetting(new Setting(), $form);
            }
        }
    }

    public function add(Request $request, ValidatorInterface $validator): Response
    {
        $this->rbac();
        $parameters = [
            'title' => $this->s->trans('add'),
            'action' => ['setting/add'],
            'errors' => [],
            'body' => $request->getParsedBody(),
            's'=>$this->s
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new SettingForm();
            if ($form->load($parameters['body']) && $validator->validate($form)->isValid()) {
                $this->settingService->saveSetting(new Setting(), $form);
                return $this->webService->getRedirectResponse('setting/index');
            }
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }

    public function edit(Request $request, CurrentRoute $currentRoute, 
              ValidatorInterface $validator): Response 
    {
        $this->rbac();
        $setting = $this->setting($currentRoute, $this->s);
        $parameters = [
            'title' => $this->s->trans('edit'),
            'action' => ['setting/edit', ['setting_id' => $setting->getSetting_id()]],
            'errors' => [],
            'body' => [
                'setting_key' => $this->setting($currentRoute, $this->s)->getSetting_key(),
                'setting_value' => $this->setting($currentRoute, $this->s)->getSetting_value(),
                'setting_trans' => $this->setting($currentRoute, $this->s)->getSetting_trans(),
                'setting_section' => $this->setting($currentRoute, $this->s)->getSetting_section(),
                'setting_subsection' => $this->setting($currentRoute, $this->s)->getSetting_subsection(),
            ],
            's'=>$this->s,
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new SettingForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->settingService->saveSetting($setting, $form);
                return $this->webService->getRedirectResponse('setting/debug_index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }
    
    public function true()  {
       return true; 
    }
    
    public function false() {
        return false;
    }
    
    public function delete(CurrentRoute $currentRoute): Response 
    {
        $this->rbac();
        $setting = $this->setting($currentRoute,$this->s);
        $this->flash($this->session,'info','This record has been deleted.');
        $this->settingService->deleteSetting($setting);               
        return $this->webService->getRedirectResponse('setting/index');        
    }
    
     //$this->flash
    private function flash(Session $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
    
    public function save_form(Request $request, CurrentRoute $currentRoute, 
              ValidatorInterface $validator): Response 
    {
        $this->rbac();
        $setting = $this->setting($currentRoute, $this->s);
        $parameters = [
            'title' => $this->s->trans('edit'),
            'action' => ['setting/edit', ['setting_id' => $setting->getSetting_id()]],
            'errors' => [],
            'body' => [
                'setting_key' => $this->setting($currentRoute, $this->s)->getSetting_key(),
                'setting_value' => $this->setting($currentRoute, $this->s)->getSetting_value(),
                'setting_trans' => $this->setting($currentRoute, $this->s)->getSetting_trans(),
                'setting_section' => $this->setting($currentRoute, $this->s)->getSetting_section(),
                'setting_subsection' => $this->setting($currentRoute, $this->s)->getSetting_subsection(),
            ],
            's'=>$this->s,
        ];
        if ($request->getMethod() === Method::POST) {
            $form = new SettingForm();
            $body = $request->getParsedBody();
            if ($form->load($body) && $validator->validate($form)->isValid()) {
                $this->settingService->saveSetting($setting, $form);
                return $this->webService->getRedirectResponse('setting/index');
            }
            $parameters['body'] = $body;
            $parameters['errors'] = $form->getFormErrors();
        }
        return $this->viewRenderer->render('__form', $parameters);
    }
    
    public function view(CurrentRoute $currentRoute): Response {
        $this->rbac();        
        $setting = $this->setting($currentRoute, $this->s);
        $parameters = [
            'title' => $this->s->trans('view'),
            'action' => ['setting/edit', ['setting_id' => $setting->getSetting_id()]],
            'errors' => [],
            'setting'=>$this->setting($currentRoute, $this->s),
            's'=>$this->s,     
            'body' => [
                'setting_id'=>$setting->getSetting_id(),
                'setting_key'=>$setting->getSetting_key(),
                'setting_value'=>$setting->getSetting_value(),
                'setting_trans'=>$setting->getSetting_trans(),
                'setting_section'=>$setting->getSetting_section(),
                'setting_subsection'=>$setting->getSetting_subsection()
            ],            
        ];
        return $this->viewRenderer->render('__view', $parameters);
    }
    
    private function rbac() {
        $canEdit = $this->userService->hasPermission('editSetting');
        if (!$canEdit){
            $this->flash($this->session, 'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('setting/index');
        }
        return $canEdit;
    }
    
    //$setting = $this->setting();
    private function setting(CurrentRoute $currentRoute, SettingRepository $settingRepository){
        $setting_id = $currentRoute->getArgument('setting_id');
        $setting = $settingRepository->repoSettingquery($setting_id);
        if ($setting === null) {
            return $this->webService->getNotFoundResponse();
        }        
        return $setting; 
    }
    
    //$settings = $this->settings();
    private function settings(SettingRepository $settingRepository){
        $settings = $settingRepository->findAllPreloaded();
        if ($settings === null) {
            return $this->webService->getNotFoundResponse();
        }
        return $settings;
    }
    
    public function clear() : Response
    {
        // In debug_mode alter this absolute path
        $directory = "C:\wamp64\www\yii-inv\public\assets";
        $filehelper = new FileHelper;
        $filehelper->clearDirectory($directory);
        $this->flash($this->session,'info', 'Assets cleared at '.$directory);
        return $this->factory->createResponse($this->viewRenderer->renderPartialAsString('/invoice/setting/successful',
        ['heading'=>'Successful','message'=>'You have cleared the cache.'])); 
    }
    
    public function get_cron_key() : Response
    {
        $this->rbac(); 
        $parameters = [
               'success'=>1,
               'cron_key'=>Random::string(32)
        ];
        return $this->factory->createResponse(Json::encode($parameters));     
    }
}
