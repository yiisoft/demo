<?php

declare(strict_types=1);

namespace App\Contact;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Header;
use Yiisoft\Http\Status;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final class ContactController
{
    public function __construct(
        private ContactMailer $mailer,
        private ResponseFactoryInterface $responseFactory,
        private UrlGeneratorInterface $url,
        private ViewRenderer $viewRenderer
    ) {
        $this->viewRenderer = $viewRenderer
            ->withControllerName('contact')
            ->withViewPath(__DIR__ . '/views');
    }

    public function contact(
        FormHydrator $formHydrator,
        ServerRequestInterface $request,
    ): ResponseInterface {
        $form = new ContactForm();
        if (!$formHydrator->populateFromPostAndValidate($form, $request)) {
            return $this->viewRenderer->render('form', ['form' => $form]);
        }

        $this->mailer->send($form);

        return $this->responseFactory
            ->createResponse(Status::FOUND)
            ->withHeader(Header::LOCATION, $this->url->generate('site/contact'));
    }
}
