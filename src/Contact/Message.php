<?php


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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
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
