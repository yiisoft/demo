<?php

declare(strict_types=1);

namespace App\Form;

use Yiisoft\Form\FormModel;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Required;

final class LoginForm extends FormModel
{
    private string $login = '';
    private string $password = '';
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct();

        $this->translator = $translator;
    }

    public function getAttributeLabels(): array
    {
        return [
            'login' => $this->translator->translate('layout.login'),
            'password' => $this->translator->translate('layout.password'),
        ];
    }

    public function getFormName(): string
    {
        return 'Login';
    }

    public function getRules(): array
    {
        return [
            'login' => [Required::rule()],
            'password' => [Required::rule()],
        ];
    }
}
