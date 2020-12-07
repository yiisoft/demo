<?php

declare(strict_types=1);

namespace App\Contact;

use App\Form\ContactForm;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;

class ContactController
{
    private ContactMailer $mailer;
    private ViewRenderer $viewRenderer;

    public function __construct(
        ViewRenderer $viewRenderer,
        ContactMailer $mailer
    ) {
        $this->mailer = $mailer;
        $this->viewRenderer = $viewRenderer->withControllerName('contact');
    }

    public function contact(ServerRequestInterface $request, ContactForm $form): ResponseInterface
    {
        $parameters = [
            'form' => $form,
        ];

        if (($request->getMethod() === Method::POST)) {
            $sent = true;

            if ($form->load($request->getParsedBody()) && $form->validate()) {
                $this->mailer->send($form, $request);
            }

            $parameters['sent'] = $sent;
        }

        return $this->viewRenderer->render('form', $parameters);
    }
}
