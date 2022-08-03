<?php

declare(strict_types=1);

namespace App\Contact;

use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Required;

final class ContactForm extends FormModel
{
    private string $name = '';
    private string $email = '';
    private string $subject = '';
    private string $body = '';
    private ?array $attachFiles = null;

    public function getAttributeLabels(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'subject' => 'Subject',
            'body' => 'Body',
        ];
    }

    public function handleRequest(ServerRequestInterface $request, ?string $formName = null)
    {
        $body = $request->getParsedBody();
        $files = $request->getUploadedFiles();
        if ($this->load($body, $formName)) {

            $rawFiles = [];
            $scope = $formName ?? $this->getFormName();

            if ($scope === '' && !empty($data)) {
                $rawFiles = $data;
            } elseif (isset($files[$scope])) {
                if (!is_array($files[$scope])) {
                    return false;
                }
                $rawFiles = $files[$scope];
            }

            foreach ($rawFiles as $name => $value) {
                $this->setAttribute((string) $name, $value);
            }
            return true;
        }
        return false;
    }

    public function getFormName(): string
    {
        return 'ContactForm';
    }

    public function getRules(): array
    {
        return [
            'name' => [new Required()],
            'email' => [new Required(), new Email()],
            'subject' => [new Required()],
            'body' => [new Required()],
            'attachFiles' => [new File()],
        ];
    }
}
