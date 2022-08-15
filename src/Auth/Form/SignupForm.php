<?php

declare(strict_types=1);

namespace App\Auth\Form;

use App\User\SignupService;
use App\User\UserLoginException;
use App\User\UserPasswordException;
use Yiisoft\Form\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\ValidatorInterface;

final class SignupForm extends FormModel
{
    private string $login = '';
    private string $password = '';
    private string $passwordVerify = '';

    public function __construct(
        private SignupService $signupService,
        private ValidatorInterface $validator,
        private TranslatorInterface $translator
    ) {
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

    public function signup(): bool
    {
        if ($this->validator->validate($this)->isValid()) {
            $this->signupService->signup();
            return true;
        }
        return false;
    }

    public function getRules(): array
    {
        return [
            'login' => fn() => $this->fillLogin(),
            'password' => fn() => $this->fillPassword(),
            'passwordVerify' => [
                new Required(),
                new Equal(
                    targetValue: $this->password,
                    message: $this->translator->translate('validator.password.not.match')
                ),
            ],
        ];
    }

    private function fillLogin(): Result
    {
        $result = new Result();
        try {
            $this->signupService->setLogin($this->login);
        } catch (UserLoginException $exception) {
            $result->addError($exception->getMessage());
        }
        return $result;
    }

    private function fillPassword(): Result
    {
        $result = new Result();
        try {
            $this->signupService->setPassword($this->password);
        } catch (UserPasswordException $exception) {
            $result->addError($exception->getMessage());
        }
        return $result;
    }
}
