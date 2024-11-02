<?php

declare(strict_types=1);

namespace App\Contact;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Input\Http\Attribute\Parameter\UploadedFiles;
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class ContactForm extends FormModel implements RulesProviderInterface, PropertyTranslatorProviderInterface
{
    private string $name = '';
    private string $email = '';
    private string $subject = '';
    private string $body = '';

    #[UploadedFiles('ContactForm.attachFiles')]
    private array $attachFiles = [];

    public function getPropertyLabels(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'subject' => 'Subject',
            'body' => 'Body',
        ];
    }

    public function getFormName(): string
    {
        return 'ContactForm';
    }

    public function getRules(): array
    {
        return [
            'name' => [new Required()],
            'email' => [new Required(), new Email()],
            'subject' => [new Required()],
            'body' => [new Required()],
        ];
    }

    public function getPropertyTranslator(): ?PropertyTranslatorInterface
    {
        return new ArrayPropertyTranslator($this->getPropertyLabels());
    }
}
