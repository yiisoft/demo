<?php

declare(strict_types=1);

namespace App\Contact;

use Psr\Http\Message\UploadedFileInterface;

class Message
{
    private string $name;
    private string $email;
    private string $subject;
    private string $content;

    /**
     * @var UploadedFileInterface[]
     */
    private array $files = [];

    public function __construct(string $name, string $email, string $subject, string $content)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;
    }

    public function addFile(UploadedFileInterface $file)
    {
        $this->files[] = $file;
    }


    public function getName(): string
    {
        return $this->name;
    }


    public function getEmail(): string
    {
        return $this->email;
    }


    public function getSubject(): string
    {
        return $this->subject;
    }


    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return UploadedFileInterface[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
