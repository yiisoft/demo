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
    private const DEFAULT_LOCALE_NAME = '_language';

    private TranslatorInterface $translator;
    private UrlGeneratorInterface $urlGenerator;
    private SessionInterface $session;
    private ResponseFactoryInterface $responseFactory;
    private LoggerInterface $logger;
    private array $locales;
    private bool $enableSaveLocale = true;
    private bool $enableDetectLocale = false;
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
            $this->translator->setLocale($locale);
            $this->urlGenerator->setDefaultArgument($this->queryParameterName, $locale);

            $response = $handler->handle($request);
            if ($this->isDefaultLocale($locale, $country) && $request->getMethod() === 'GET') {
                $response = $this->responseFactory->createResponse(Status::FOUND)
                    ->withHeader(Header::LOCATION, $newPath);
            }
            if ($this->enableSaveLocale) {
                $response = $this->saveLocale($locale, $response);
            }
            return $response;
        }
        if ($this->enableSaveLocale) {
            [$locale, $country] = $this->getLocaleFromRequest($request);
        }
        if ($locale === null && $this->enableDetectLocale) {
            [$locale, $country] = $this->detectLocale($request);
        }
        if ($locale === null || $this->isDefaultLocale($locale, $country)) {
            $this->urlGenerator->setDefaultArgument($this->queryParameterName, null);
            $request = $request->withUri($uri->withPath('/' . $this->defaultLocale . $path));
            return $handler->handle($request);
        }
        $this->urlGenerator->setDefaultArgument($this->queryParameterName, $locale);

        if ($request->getMethod() === 'GET') {
            return $this->responseFactory->createResponse(Status::FOUND)
                ->withHeader(Header::LOCATION, '/' . $locale . $path);
        }

        return $handler->handle($request);
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
            [$locale, $country] = $this->parseLocale($locale);
            if (isset($this->locales[$locale])) {
                $this->logger->info(sprintf("Locale '%s' found in URL", $locale));
                return [$locale, $country];
            }
        }
        return [null, null];
    }

    private function getLocaleFromRequest(ServerRequestInterface $request): array
    {
        $cookies = $request->getCookieParams();
        $queryParameters = $request->getQueryParams();
        if (isset($cookies[$this->sessionName])) {
            $this->logger->info(sprintf("Locale '%s' found in cookies", $cookies[$this->sessionName]));
            return $this->parseLocale($cookies[$this->sessionName]);
        }
        if (isset($queryParameters[$this->queryParameterName])) {
            $this->logger->info(
                sprintf("Locale '%s' found in query string", $queryParameters[$this->queryParameterName])
            );
            return $this->parseLocale($queryParameters[$this->queryParameterName]);
        }
        return [null, null];
    }

    private function isDefaultLocale(string $locale, ?string $country): bool
    {
        return $locale === $this->defaultLocale || ($country !== null && $this->defaultLocale === "$locale-$country");
    }

    private function detectLocale(ServerRequestInterface $request): array
    {
        foreach ($request->getHeader(Header::ACCEPT_LANGUAGE) as $language) {
            return $this->parseLocale($language);
        }
        return [null, null];
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

    private function parseLocale(string $locale): array
    {
        if (strpos($locale, '-') !== false) {
            return explode('-', $locale, 2);
        }
        if (isset($this->locales[$locale]) && strpos($this->locales[$locale], '-') !== false) {
            return explode('-', $this->locales[$locale], 2);
        }
        return [$locale, null];
    }

    public function withLocales(array $locales): self
    {
        $new = clone $this;
        $new->locales = $locales;
        return $new;
    }

    public function withDefaultLocale(string $defaultLocale): self
    {
        $new = clone $this;
        $new->defaultLocale = $defaultLocale;
        return $new;
    }

    public function withQueryParameterName(string $queryParameterName): self
    {
        $new = clone $this;
        $new->queryParameterName = $queryParameterName;
        return $new;
    }

    public function withSessionName(string $sessionName): self
    {
        $new = clone $this;
        $new->sessionName = $sessionName;
        return $new;
    }

    public function withEnableSaveLocale(bool $enableSaveLocale): self
    {
        $new = clone $this;
        $new->enableDetectLocale = $enableSaveLocale;
        return $new;
    }

    public function withEnableDetectLocale(bool $enableDetectLocale): self
    {
        $new = clone $this;
        $new->enableDetectLocale = $enableDetectLocale;
        return $new;
    }
}
