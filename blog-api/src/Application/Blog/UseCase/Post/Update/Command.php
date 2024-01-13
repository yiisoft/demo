<?php

declare(strict_types=1);

namespace App\Application\Blog\UseCase\Post\Update;

use App\Application\Blog\Entity\Post\PostStatus;

final class Command
{
    public function __construct(
        private int $id,
        private string $title,
        private string $content,
        private PostStatus $status,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatus(): PostStatus
    {
        return $this->status;
    }
}
