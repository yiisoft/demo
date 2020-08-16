<?php

declare(strict_types=1);

namespace App\Contact;

use App\ViewRenderer\CsrfInjection;
use App\ViewRenderer\ViewRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Http\Method;

class ContactController
{
    private ContactMailer $mailer;
    private LoggerInterface $logger;
    private ViewRenderer $viewRenderer;
    private CsrfInjection $csrfInjection;

    public function __construct(
        ViewRenderer $viewRenderer,
        ContactMailer $mailer,
        LoggerInterface $logger,
        CsrfInjection $csrfInjection
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->viewRenderer = $viewRenderer->withControllerName('contact');
        $this->csrfInjection = $csrfInjection;
    }

    public function contact(ServerRequestInterface $request): ResponseInterface
    {
        $body = $request->getParsedBody();
        $parameters = [
            'body' => $body,
        ];
        if ($request->getMethod() === Method::POST) {
            $sent = false;
            $error = '';

            try {
                foreach (['subject', 'name', 'email', 'content'] as $name) {
                    if (empty($body[$name])) {
                        throw new \InvalidArgumentException(ucfirst($name) . ' is required');
                    }
                }

                $message = new Message($body['name'], $body['email'], $body['subject'], $body['content']);

                $files = $request->getUploadedFiles();
                if (!empty($files['file']) && $files['file']->getError() === UPLOAD_ERR_OK) {
                    $message->addFile($files['file']);
                }

                $this->mailer->send($message);

                $sent = true;
            } catch (\Throwable $e) {
                $this->logger->error($e);
                $error = $e->getMessage();
            }
            $parameters['sent'] = $sent;
            $parameters['error'] = $error;
        }

        return $this->viewRenderer->addInjection($this->csrfInjection)->render('form', $parameters);
    }
}
