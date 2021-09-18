<?php

declare(strict_types=1);

namespace App\Middleware;

use DateInterval;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Cookies\Cookie;
use Yiisoft\Http\Header;
use Yiisoft\Http\Status;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Translator\TranslatorInterface;

final class LocaleMiddleware implements MiddlewareInterface
{
    private const DEFAULT_LOCALE = 'en';
    private const DEFAULT_LOCALE_NAME = 'language';

    private TranslatorInterface $translator;
    private UrlGeneratorInterface $urlGenerator;
    private SessionInterface $session;
    private ResponseFactoryInterface $responseFactory;
    private LoggerInterface $logger;
    private array $locales;
    private bool $enableSaveLocale = true;
    private bool $enableDetectLocale = true;
    private string $defaultLocale = self::DEFAULT_LOCALE;
    private string $queryParameterName = self::DEFAULT_LOCALE_NAME;
    private string $sessionName = self::DEFAULT_LOCALE_NAME;
    private ?DateInterval $cookieDuration;

    public function __construct(
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        SessionInterface $session,
        LoggerInterface $logger,
        ResponseFactoryInterface $responseFactory,
        array $locales = []
    ) {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
        $this->logger = $logger;
        $this->responseFactory = $responseFactory;
        $this->locales = $locales;
        $this->cookieDuration = new DateInterval('P30D');
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->locales === []) {
            return $handler->handle($request);
        }

        $uri = $request->getUri();
        $path = $uri->getPath();
        [$locale, $country] = $this->getLocaleFromPath($path);

        if ($locale !== null) {
            $length = strlen($locale);
            $newPath = substr($path, $length + 1);
            if ($newPath === '') {
                $newPath = '/';
            }
            $request = $request->withUri($uri->withPath($newPath));
            $this->translator->setLocale($locale);
            $this->urlGenerator->setUriPrefix('/' . $locale);

            $response = $handler->handle($request);
            if ($this->isDefaultLocale($locale, $country)) {
                $response = $this->responseFactory->createResponse(Status::FOUND)
                    ->withHeader(Header::LOCATION, $newPath);
            }
            if ($this->enableSaveLocale) {
                $response = $this->saveLocale($locale, $response);
            }
            return $response;
        }
        if ($this->enableSaveLocale) {
            $locale = $this->getLocaleFromRequest($request);
        }
        if ($locale === null && $this->enableDetectLocale) {
            // TODO: detect locale from headers
        }
        if ($locale === null || $this->isDefaultLocale($locale, $country)) {
            return $handler->handle($request);
        }
        return $this->responseFactory->createResponse(Status::FOUND)
            ->withHeader(Header::LOCATION, '/' . $locale . rtrim($path, '/'));
    }

    private function getLocaleFromPath(string $path): array
    {
        $parts = [];
        foreach ($this->locales as $code => $locale) {
            $lang = is_string($code) ? $code : $locale;
            $parts[] = $lang;
        }

        $pattern = implode('|', $parts);
        if (preg_match("#^/($pattern)\b(/?)#i", $path, $matches)) {
            $locale = $matches[1];
            $country = null;
            if (strpos($locale, '-') !== false) {
                [$locale, $country] = explode('-', $locale, 2);
            }
            if (isset($this->locales[$locale])) {
                $this->logger->info(sprintf("Locale '%s' found in URL", $locale));
                return [$locale, $country];
            }
        }
        return [null, null];
    }

    private function getLocaleFromRequest(ServerRequestInterface $request)
    {
        $cookies = $request->getCookieParams();
        $queryParameters = $request->getQueryParams();
        if (isset($cookies[$this->sessionName])) {
            $this->logger->info(sprintf("Locale '%s' found in cookies", $cookies[$this->sessionName]));
            return $cookies[$this->sessionName];
        } elseif (isset($queryParameters[$this->queryParameterName])) {
            $this->logger->info(
                sprintf("Locale '%s' found in query string", $queryParameters[$this->queryParameterName])
            );
            return $queryParameters[$this->queryParameterName];
        }
        return null;
    }

    private function isDefaultLocale(string $locale, ?string $country): bool
    {
        return $locale === $this->defaultLocale || ($country !== null && $this->defaultLocale === "$locale-$country");
    }

    private function detectLocale(ServerRequestInterface $request)
    {
        //
    }

    private function saveLocale(string $locale, ResponseInterface $response): ResponseInterface
    {
        $this->logger->info('Saving found locale to cookies');
        $this->session->set($this->sessionName, $locale);
        $cookie = (new Cookie($this->sessionName, $locale));
        if ($this->cookieDuration !== null) {
            $cookie = $cookie->withMaxAge($this->cookieDuration);
        }
        return $cookie->addToResponse($response);
    }
}
