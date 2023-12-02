<?php

declare(strict_types=1);

namespace App\Auth\Form;

use App\User\User;
use App\User\UserRepository;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class SignupForm extends FormModel implements RulesProviderInterface
{
    private string $login = '';
    private string $password = '';
    private string $passwordVerify = '';

    public function __construct(
        private TranslatorInterface $translator,
        private UserRepository $userRepository,
    ) {
    }

    public function getAttributeLabels(): array
    {
        return [
            'login' => $this->translator->translate('layout.login'),
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

    public function signup(): false|User
    {
        if ($this->isValid()) {
            $user = new User($this->getLogin(), $this->getPassword());
            $this->userRepository->save($user);

            return $user;
        }

        return false;
    }

    public function getRules(): array
    {
        return [
            'login' => [
                new Required(),
                new Length(min: 1, max: 48, skipOnError: true),
                function ($value): Result {
                    $result = new Result();
                    if ($this->userRepository->findByLogin($value) !== null) {
                        $result->addError('User with this login already exists.');
                    }

                    return $result;
                },
            ],
            'password' => [
                new Required(),
                new Length(min: 8),
            ],
            'passwordVerify' => [
                new Required(),
                new Equal(
                    targetValue: $this->password,
                    message: $this->translator->translate('validator.password.not.match')
                ),
            ],
        ];
    }
}
