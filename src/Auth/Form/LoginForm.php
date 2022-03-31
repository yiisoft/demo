<?php

declare(strict_types=1);

namespace App\Auth\Form;

use App\Auth\AuthService;
use Yiisoft\Form\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Required;

final class LoginForm extends FormModel
{
    private string $login = '';
    private string $password = '';
    private bool $rememberMe = false;
    private AuthService $authService;
    private TranslatorInterface $translator;

    public function __construct(AuthService $authService, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->authService = $authService;
        $this->translator = $translator;
    }

    public function getAttributeLabels(): array
    {
        return [
            'login' => $this->translator->translate('layout.login'),
            'password' => $this->translator->translate('layout.password'),
            'rememberMe' => $this->translator->translate('layout.remember'),
        ];
    }

    public function getFormName(): string
    {
        return 'Login';
    }

    public function getRules(): array
    {
        return [
            'login' => [new Required()],
            'password' => $this->passwordRules(),
        ];
    }

    private function passwordRules(): array
    {
        return [
            new Required(),
            function (): Result {
                $result = new Result();

                if (!$this->authService->login($this->login, $this->password)) {
                    $this->getFormErrors()->addError('login', '');
                    $result->addError($this->translator->translate('validator.invalid.login.password'));
                }

                return $result;
            },
        ];
    }
}
