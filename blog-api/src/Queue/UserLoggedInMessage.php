<?php

declare(strict_types=1);

namespace App\Queue;

use Yiisoft\Queue\Message\MessageInterface;

final class UserLoggedInMessage implements MessageInterface
{
    private ?string $id = null;

    public function __construct(private string $userId, private int $time)
    {
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getHandlerName(): string
    {
        return LoggingAuthorizationHandler::NAME;
    }

    public function getData(): array
    {
        return [
            'user_id' => $this->userId,
            'time' => $this->time,
        ];
    }

    public function getMetadata(): array
    {
        return [];
    }
}
