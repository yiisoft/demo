<?php

declare(strict_types=1);

namespace App\Auth\Form;

use App\Auth\AuthService;
use Yiisoft\FormModel\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class LoginForm extends FormModel implements RulesProviderInterface, PropertyTranslatorProviderInterface
{
    private string $login = '';
    private string $password = '';
    private bool $rememberMe = false;

    public function __construct(
        private AuthService $authService,
        private TranslatorInterface $translator,
    ) {
    }

    public function getPropertyLabels(): array
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
            new Callback(
                callback: function (): Result {
                    $result = new Result();

                    if (!$this->authService->login($this->login, $this->password)) {
                        $result->addError($this->translator->translate('validator.invalid.login.password'));
                    }

                    return $result;
                },
                skipOnEmpty: true,
            ),
        ];
    }

    public function getPropertyTranslator(): ?PropertyTranslatorInterface
    {
        return new ArrayPropertyTranslator($this->getPropertyLabels());
    }
}
