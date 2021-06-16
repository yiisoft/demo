<?php

declare(strict_types=1);

namespace App\Invoice\EmailTemplate;

use App\Invoice\Entity\EmailTemplate;
use App\Invoice\EmailTemplate\EmailTemplateRepository;
use App\User\User;

final class EmailTemplateService
{
    private EmailTemplateRepository $repository;

    public function __construct(EmailTemplateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function saveEmailTemplate(User $user, EmailTemplate $model, EmailTemplateForm $form): void
    {
        $model->setEmail_template_title($form->getEmail_template_title());
        $model->setEmail_template_type($form->getEmail_template_type());
        $model->setEmail_template_body($form->getEmail_template_body());
        $model->setEmail_template_subject($form->getEmail_template_subject());
        $model->setEmail_template_from_name($form->getEmail_template_from_name());
        $model->setEmail_template_from_email($form->getEmail_template_from_email());
        $model->setEmail_template_cc($form->getEmail_template_cc());
        $model->setEmail_template_bcc($form->getEmail_template_bcc());
        $model->setEmail_template_pdf_template($form->getEmail_template_pdf_template());
        
        
        $this->repository->save($model);
    }
    
    public function deleteEmailTemplate(EmailTemplate $model): void
    {
        $this->repository->delete($model);
    }
}
