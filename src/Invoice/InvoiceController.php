<?php

declare(strict_types=1);

namespace App\Invoice;

use App\Invoice\Entity\Setting;
use App\Invoice\Setting\SettingRepository;
use App\Invoice\Setting\SettingForm;
use App\Invoice\Setting\SettingService;
use App\Service\WebControllerService;
use App\User\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Yiisoft\Security\Random;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Session\Flash\Flash;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\View\ViewRenderer;

use Cycle\Database\DatabaseManager;

final class InvoiceController
{
    private ViewRenderer $viewRenderer;
    private WebControllerService $webService;
    private UserService $userService; 
    private TranslatorInterface $translator;
    private SettingService $settingService;
    
        
    public function __construct(ViewRenderer $viewRenderer, 
                                WebControllerService $webService, 
                                UserService $userService, 
                                TranslatorInterface $translator,
                                SettingService $settingService)
    {
        $this->viewRenderer = $viewRenderer->withControllerName('invoice')
                                           ->withLayout(dirname(dirname(__DIR__)).'/src/Invoice/Layout/main.php');                                           
        $this->webService = $webService;
        $this->userService = $userService;
        $this->translator = $translator;
        $this->settingService = $settingService;
    }

    public function index(SessionInterface $session, CurrentUser $currentUser, DatabaseManager $dbal, SettingRepository $sR): Response {
        $canEdit = $this->rbac($session);
        $flash = $this->flash($session, 'info' , $this->viewRenderer->renderPartialAsString('/invoice/info/invoice'));
        $sR->repoCount('default_settings_exist') === 0 ? $this->install_default_settings_on_first_run($session) : '';
        $data = [
            'isGuest' => $currentUser->isGuest(),
            'canEdit' => $canEdit,
            'tables'=> $dbal->database('default')->getTables(),
            'flash'=> $flash,           
        ];
        return $this->viewRenderer->render('index', $data);
    }
        
    private function rbac(SessionInterface $session) {
        $canEdit = $this->userService->hasPermission('editGenerator');
        if (!$canEdit){
            $this->flash($session,'warning', $this->translator->translate('invoice.permission'));
            return $this->webService->getRedirectResponse('invoice/index');
        }
        return $canEdit;
    }
    
    private function flash(SessionInterface $session, $level, $message){
        $flash = new Flash($session);
        $flash->set($level, $message); 
        return $flash;
    }
    
    private function install_default_settings_on_first_run(SessionInterface $session) : void {
        $default_settings = [
            'default_language' => $session->get('_language') ?? 'English',
            //paginator list limit
            'default_list_limit'=>10,
            'date_format' => 'd/m/Y',
            'currency_symbol' => '£',
            'currency_symbol_placement' => 'before',
            'currency_code' => 'GBP',
            'invoices_due_after' => 30,
            'quotes_expire_after' => 15,
            'default_invoice_group' => 3,
            'default_quote_group' => 4,
            'thousands_separator' => ',',
            'decimal_point' => '.',
            'cron_key' => Random::string(32),
            'tax_rate_decimal_places' => 2,
            'pdf_invoice_template' => 'Invoice',
            'pdf_invoice_template_paid' => 'Invoice - paid',
            'pdf_invoice_template_overdue' => 'Invoice - overdue',
            'pdf_quote_template' => 'Invoice',
            'public_invoice_template' => 'Invoice_Web',
            'public_quote_template' => 'Invoice_Web',
            'disable_sidebar' => 1,
            'default_settings_exist'=>1
        ]; 
        $this->install_default_settings($default_settings);
    }
    
    private function install_default_settings($default_settings) : void
    {
        foreach ($default_settings as $key => $value) {
            $form = new SettingForm();
            $array = [
                'setting_key'=>$key,
                'setting_value'=>$value,
                'setting_trans'=>'',
                'setting_section'=>'',
                'setting_subsection'=>''
            ];
            if ($form->load($array)) {
                $this->settingService->saveSetting(new Setting(), $form);
            }
        }    
    }
}


