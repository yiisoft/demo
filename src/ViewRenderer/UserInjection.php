<?php

declare(strict_types=1);

namespace App\ViewRenderer;

use Yiisoft\Yii\Web\User\User;

class UserInjection extends AbstractInjection
{
    public const DEFAULT_PARAMETER = 'user';

    private User $user;

    private string $parameter = self::DEFAULT_PARAMETER;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function withParameter(string $parameter): self
    {
        $clone = clone $this;
        $clone->parameter = $parameter;
        return $clone;
    }

    public function getParams(): array
    {
        return [$this->parameter => $this->user->getIdentity()];
    }
}
