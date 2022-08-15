<?php

declare(strict_types=1);

namespace App\Auth\Form;

use Yiisoft\Form\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Required;

final class SignupForm extends FormModel
{
    private string $login = '';
    private string $password = '';
    private string $passwordVerify = '';

    public function __construct(private TranslatorInterface $translator)
    {
        parent::__construct();
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
            'login' => [new Required()],
            'password' => [new Required()],
            'passwordVerify' => [
                new Required(),
                new Equal(targetValue: $this->password,
                    message: $this->translator->translate('validator.password.not.match')),
            ],
        ];
    }
}
