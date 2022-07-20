<?php

declare(strict_types=1);

namespace App\User;

use Yiisoft\Security\PasswordHasher;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;

final class UserPassword
{
    private string $passwordHash;
    private PasswordHasher $hasher;

    private function __construct(private string $value)
    {
        $this->hasher = new PasswordHasher();
        $this->passwordHash = $this->hasher->hash($this->value);
    }

    public function getHash(): string
    {
        return $this->passwordHash;
    }

    public function isEqualHash(string $hash): bool
    {
        return $this->hasher->validate($this->value, $hash);
    }

    /**
     * @throw UserPasswordException
     */
    public static function createNew(string $password): UserPassword
    {
        $validator = new Validator(new SimpleRuleHandlerContainer());
        $rules = [
            new Required(),
            new HasLength(min: 8),
        ];

        $result = $validator->validate($password, $rules);
        if (!$result->isValid()) {
            foreach ($result->getErrorMessages() as $errorMessage) {
                throw new UserPasswordException($errorMessage);
            }
        }

        return new UserPassword($password);
    }
}
