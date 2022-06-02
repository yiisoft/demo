<?php

declare(strict_types=1);

namespace App\Blog\Post;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

final class PostForm extends FormModel
{
    private ?string $title = null;
    private ?string $content = null;
    private array $tags = [];

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getFormName(): string
    {
        return '';
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getRules(): array
    {
        return [
            'title' => [new Required()],
            'content' => [new Required()],
        ];
    }
}
