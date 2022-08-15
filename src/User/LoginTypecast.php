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

    public function cast(array $values): array
    {
        foreach ($this->rules as $column => $rule) {
            if (!isset($values[$column])) {
                continue;
            }

            $class = new \ReflectionClass(UserLogin::class);
            $constructor = $class->getConstructor();
            $constructor->setAccessible(true);
            $object = $class->newInstanceWithoutConstructor();
            $constructor->invoke($object, (string)$values[$column]);
            $values[$column] = $object;
        }

        return $values;
    }

    public function uncast(array $values): array
    {
        foreach ($this->rules as $column => $rule) {
            if (!isset($values[$column]) || !$values[$column] instanceof UserLogin) {
                continue;
            }

            $values[$column] = $values[$column]->value();
        }

        return $values;
    }
}
