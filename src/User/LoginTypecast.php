<?php

namespace App\User;

use Cycle\ORM\Parser\CastableInterface;
use Cycle\ORM\Parser\UncastableInterface;

/**
 * @internal
 */
class LoginTypecast implements CastableInterface, UncastableInterface
{
    private array $rules = [];

    public function __construct() {
    }

    public function setRules(array $rules): array
    {
        foreach ($rules as $key => $rule) {
            if ($rule === 'login') {
                unset($rules[$key]);
                $this->rules[$key] = $rule;
            }
        }

        return $rules;
    }

    public function cast(array $data): array
    {
        foreach ($this->rules as $column => $rule) {
            if (!isset($data[$column])) {
                continue;
            }

            $class = new \ReflectionClass(UserLogin::class);
            $constructor = $class->getConstructor();
            $constructor->setAccessible(true);
            $object = $class->newInstanceWithoutConstructor();
            $constructor->invoke($object, (string)$data[$column]);
            $data[$column] = $object;
        }

        return $data;
    }

    public function uncast(array $data): array
    {
        foreach ($this->rules as $column => $rule) {
            if (!isset($data[$column]) || !$data[$column] instanceof UserLogin) {
                continue;
            }

            $data[$column] = $data[$column]->value();
        }

        return $data;
    }
}
