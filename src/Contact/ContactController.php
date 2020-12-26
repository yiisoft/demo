<?php

declare(strict_types=1);

namespace App\Contact;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Header;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\ViewRenderer;

class ContactController
{
    private ContactMailer $mailer;
    private ResponseFactoryInterface $response;
    private UrlGeneratorInterface $url;
    private ViewRenderer $viewRenderer;

    public function __construct(
        ContactMailer $mailer,
        ResponseFactoryInterface $response,
        UrlGeneratorInterface $url,
        ViewRenderer $viewRenderer
    ) {
        $this->mailer = $mailer;
        $this->response = $response;
        $this->url = $url;
        $this->viewRenderer = $viewRenderer->withControllerName('contact');
    }

    public function contact(ContactForm $form, ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        if (($request->getMethod() === Method::POST) && $form->load($body) && $form->validate()) {
            $this->mailer->send($form, $request);

            return $this->response
                ->createResponse(Status::FOUND)
                ->withHeader(Header::LOCATION, $this->url->generate('site/contact'));
        }

        return $this->viewRenderer->render('form', ['form' => $form]);
    }
}
