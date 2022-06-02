<?php

declare(strict_types=1);

namespace App\Invoice\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

#[Entity(repository: \App\Invoice\EmailTemplate\EmailTemplateRepository::class)] 
class EmailTemplate
{
    #[Column(type: 'primary')]
    private ?int $id = null;
    
    #[Column(type: 'text', nullable: true)]
    private ?string $email_template_title = '';
    
    #[Column(type: 'string(151)', nullable: true)]
    private ?string $email_template_type = '';
    
    #[Column(type: 'longText')]
    private string $email_template_body = '';
    
    #[Column(type: 'text', nullable: true)]
    private ?string $email_template_subject = '';
    
    #[Column(type: 'text', nullable: true)]
    private ?string $email_template_from_name = '';
    
    #[Column(type: 'text', nullable: true)]
    private ?string $email_template_from_email = '';
    
    #[Column(type: 'text', nullable: true)]
    private ?string $email_template_cc = '';
    
    #[Column(type: 'text', nullable: true)]
    private ?string $email_template_bcc = '';
    
    #[Column(type: 'string(151)', nullable: true)]
    private ?string $email_template_pdf_template = '';
       
    public function __construct(
        string $email_template_title='',
        string $email_template_type='',
        string $email_template_body='',
        string $email_template_subject='',
        string $email_template_from_name='',
        string $email_template_from_email='',
        string $email_template_cc='',
        string $email_template_bcc='',
        string $email_template_pdf_template=''            
    ) 
    
    {
        $this->email_template_title = $email_template_title;
        $this->email_template_type = $email_template_type;
        $this->email_template_body = $email_template_body;
        $this->email_template_subject = $email_template_subject;
        $this->email_template_from_name = $email_template_from_name;
        $this->email_template_from_email = $email_template_from_email;
        $this->email_template_cc = $email_template_cc;
        $this->email_template_bcc = $email_template_bcc;
        $this->email_template_pdf_template = $email_template_pdf_template;        
    }
    
    public function getEmail_template_id(): ?int
    {
        return $this->id;
    }

    public function getEmail_template_title(): string
    {
        return $this->email_template_title;
    }

    public function setEmail_template_title(string $email_template_title): void
    {
        $this->email_template_title = $email_template_title;
    }
    
    public function getEmail_template_type(): string
    {
        return $this->email_template_type;
    }

    public function setEmail_template_type(string $email_template_type): void
    {
        $this->email_template_type = $email_template_type;
    }
    
    public function getEmail_template_body(): string
    {
        return $this->email_template_body;
    }

    public function setEmail_template_body(string $email_template_body): void
    {
        $this->email_template_body = $email_template_body;
    }
    
    public function getEmail_template_subject(): string
    {
        return $this->email_template_subject;
    }

    public function setEmail_template_subject(string $email_template_subject): void
    {
        $this->email_template_subject = $email_template_subject;
    }
    
    public function getEmail_template_from_name(): string
    {
        return $this->email_template_from_name;
    }

    public function setEmail_template_from_name(string $email_template_from_name): void
    {
        $this->email_template_from_name = $email_template_from_name;
    }
    
    public function getEmail_template_from_email(): string
    {
        return $this->email_template_from_email;
    }

    public function setEmail_template_from_email(string $email_template_from_email): void
    {
        $this->email_template_from_email = $email_template_from_email;
    }
    
    public function getEmail_template_cc(): string
    {
        return $this->email_template_cc;
    }

    public function setEmail_template_cc(string $email_template_cc): void
    {
        $this->email_template_cc = $email_template_cc;
    }
    
    public function getEmail_template_bcc(): string
    {
        return $this->email_template_bcc;
    }

    public function setEmail_template_bcc(string $email_template_bcc): void
    {
        $this->email_template_bcc = $email_template_bcc;
    }
    
    public function getEmail_template_pdf_template(): string
    {
        return $this->email_template_pdf_template;
    }

    public function setEmail_template_pdf_template(string $email_template_pdf_template): void
    {
        $this->email_template_pdf_template = $email_template_pdf_template;
    }
}
