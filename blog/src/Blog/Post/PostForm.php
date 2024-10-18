<?php

declare(strict_types=1);

namespace App\Blog\Post;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\Rule\Required;

final class PostForm extends FormModel
{
    #[Required]
    private ?string $title = null;

    #[Required]
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
}
