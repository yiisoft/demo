<?php

declare(strict_types=1);

namespace App\Auth\Form;

use App\Auth\AuthService;
use Yiisoft\Form\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Required;

final class SignupForm extends FormModel
{
    private string $login = '';
    private string $password = '';
    private string $passwordVerify = '';
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
            'email' => $this->translator->translate('layout.login'),
            'password' => $this->translator->translate('layout.password'),
            'passwordVerify' => $this->translator->translate('layout.password-verify'),
        ];
    }

    public function getFormName(): string
    {
        return 'Signup';
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRules(): array
    {
        return [
            'login' => [Required::rule()],
            'password' => [Required::rule()],
            'passwordVerify' => $this->passwordVerifyRules(),
        ];
    }

    private function passwordVerifyRules(): array
    {
        return [
            Required::rule(),

            function (): Result {
                $result = new Result();
                if ($this->password !== $this->passwordVerify) {
                    $result->addError($this->translator->translate('validator.password.not.match'));
                }

                if ($result->getErrors() === [] && !$this->authService->signup($this->login, $this->password)) {
                    $result->addError($this->translator->translate('validator.user.exist'));
                }

                return $result;
            },
        ];
    }
}
