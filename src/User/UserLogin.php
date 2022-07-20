<?php

declare(strict_types=1);

namespace App\User;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;

final class UserLogin
{
    private function __construct(private string $value)
    {
    }

    public function value(): string
    {
        return $this->value;
    }

    /**
     * @throw UserLoginException
     */
    public static function createNew(string $login, UserRepository $userRepository): UserLogin
    {
        $validator = new Validator(new SimpleRuleHandlerContainer());
        $rules = [
            new Required(),
            new HasLength(min: 1, max: 48, skipOnError: true),
            static function ($value) use ($userRepository): Result {
                $result = new Result();
                if ($userRepository->findByLogin($value) !== null) {
                    $result->addError('User with this login already exists.');
                }
                return $result;
            },
        ];

        $result = $validator->validate($login, $rules);
        if (!$result->isValid()) {
            foreach ($result->getErrorMessages() as $errorMessage) {
                throw new UserLoginException($errorMessage);
            }
        }

        return new UserLogin($login);
    }
}
