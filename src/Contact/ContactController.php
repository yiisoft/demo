<?php

declare(strict_types=1);

namespace App\Contact;

use App\Form\ContactForm;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Http\Method;
use Yiisoft\Yii\View\ViewRenderer;

class ContactController
{
    private ContactMailer $mailer;
    private LoggerInterface $logger;
    private ViewRenderer $viewRenderer;
    private ResponseFactoryInterface $responseFactory;

    public function __construct(
        ViewRenderer $viewRenderer,
        ContactMailer $mailer,
        LoggerInterface $logger,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->viewRenderer = $viewRenderer->withControllerName('contact');
        $this->responseFactory = $responseFactory;
    }

    public function contact(ServerRequestInterface $request, ContactForm $form): ResponseInterface
    {
        $parameters = [
            'form' => $form
        ];
        $sent = false;

        if (($request->getMethod() === Method::POST)) {
            $sent = true;

            if ($form->load($request->getParsedBody()) && $form->validate()) {
                $this->mailer->send($form, $request);
            }

            $parameters['sent'] = $sent;
        }

        return $this->viewRenderer->withCsrf()->render('form', $parameters);
    }
}
