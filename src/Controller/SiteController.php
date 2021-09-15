<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Cookies\Cookie;
use Yiisoft\Cookies\CookieCollection;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\View\ViewRenderer;
use Yiisoft\Translator\TranslatorInterface;

class SiteController
{
    private ViewRenderer $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer->withController($this);
    }

    public function index(ServerRequestInterface $request,TranslatorInterface $translate): ResponseInterface
    {
        return $this->viewRenderer->render('index');
    }

    public function setLocale(
        ResponseFactoryInterface $responseFactory,
        UrlGeneratorInterface $urlGenerator,
        ServerRequestInterface $request
    ): ResponseInterface {
        $locale = $request->getParsedBody()['locale'];

        $response = $responseFactory
            ->createResponse(302);

        $cookies = CookieCollection::fromArray($request->getCookieParams());
        $cookies->add(new Cookie('locale', $locale));
        
        $response = $cookies->setToResponse($response);

        return $response
            ->withHeader(
                'Location',
                $urlGenerator->generate('site/index')
            );
    }
}
