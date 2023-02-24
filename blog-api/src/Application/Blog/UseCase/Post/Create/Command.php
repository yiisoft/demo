<?php

declare(strict_types=1);

namespace App\Application\Blog\UseCase\Post\Create;

use App\Application\Blog\Entity\Post\PostStatus;
use App\Application\User\Entity\User;

final class Command
{
    public function __construct(
        private string $title,
        private string $content,
        private PostStatus $status,
        private User $user,
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getStatus(): PostStatus
    {
        return $this->status;
    }
}
