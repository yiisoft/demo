<?php

declare(strict_types=1);

namespace App\Contact;

use Yiisoft\Form\FormModel;
use Yiisoft\Form\HtmlOptions\EmailHtmlOptions;
use Yiisoft\Form\HtmlOptions\RequiredHtmlOptions;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Required;

final class ContactForm extends FormModel
{
    private string $name = '';
    private string $email = '';
    private string $subject = '';
    private string $body = '';
    private ?array $attachFiles = null;

    public function getAttributeLabels(): array
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
            'name' => [new RequiredHtmlOptions(Required::rule())],
            'email' => [new RequiredHtmlOptions(Required::rule()), new EmailHtmlOptions(Email::rule())],
            'subject' => [new RequiredHtmlOptions(Required::rule())],
            'body' => [new RequiredHtmlOptions(Required::rule())],
        ];
    }
}
