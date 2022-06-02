<?php

declare(strict_types=1);

namespace App\Invoice\EmailTemplate;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class EmailTemplateForm extends FormModel
{
    private ?string $email_template_title = null;
    private ?string $email_template_type = null;
    private ?string $email_template_body = null;
    private ?string $email_template_subject = null;
    private ?string $email_template_from_name = null;
    private ?string $email_template_from_email = null;
    private ?string $email_template_cc = null;
    private ?string $email_template_bcc = null;
    private ?string $email_template_pdf_template = null;
                   
    public function getEmail_template_title(): string
    {
        return $this->email_template_title;
    }

    public function getEmail_template_type(): string
    {
        return $this->email_template_type;
    }
    
    public function getEmail_template_body(): string
    {
        return $this->email_template_body;
    }
    
    public function getEmail_template_subject(): string
    {
        return $this->email_template_subject;
    }
    
    public function getEmail_template_from_name(): string
    {
        return $this->email_template_from_name;
    }
    
    public function getEmail_template_from_email(): string
    {
        return $this->email_template_from_email;
    }
    
    public function getEmail_template_cc(): string
    {
        return $this->email_template_cc;
    }
    
    public function getEmail_template_bcc(): string
    {
        return $this->email_template_bcc;
    }
    
    public function getEmail_template_pdf_template(): string
    {
        return $this->email_template_pdf_template;
    }
    
    public function getFormName(): string
    {
        return '';
    }
    
    public function getRules(): array
    {
        return [
            'email_template_title' => [new Required()],
            'email_template_from_name' => [new Required()],
            'email_template_from_email' =>[new Required()],
        ];
    }
}
