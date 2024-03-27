<?php

declare(strict_types=1);

namespace App\Debug\CacheCollector;

use Psr\SimpleCache\CacheInterface;

class SimpleCacheInterfaceProxy implements CacheInterface
{
    public function __construct(
        private CacheInterface $decorated,
        private CacheCollector $collector,
    ) {
    }

    public function get(string $key, mixed $default = null)
    {
        $this->collector->collectGet($key);
        return $this->decorated->{__FUNCTION__}(...func_get_args());
    }

    public function set(string $key, mixed $value, \DateInterval|int|null $ttl = null)
    {
        $this->collector->collectSet($key, $value, $ttl);
        return $this->decorated->{__FUNCTION__}(...func_get_args());
    }

    public function delete(string $key)
    {
        return $this->decorated->{__FUNCTION__}(...func_get_args());
    }

    public function clear()
    {
        return $this->decorated->{__FUNCTION__}(...func_get_args());
    }

    public function getMultiple(iterable $keys, mixed $default = null)
    {
        return $this->decorated->{__FUNCTION__}(...func_get_args());
    }

    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null)
    {
        return $this->decorated->{__FUNCTION__}(...func_get_args());
    }

    public function deleteMultiple(iterable $keys)
    {
        return $this->decorated->{__FUNCTION__}(...func_get_args());
    }

    public function has(string $key)
    {
        return $this->decorated->{__FUNCTION__}(...func_get_args());
    }
}
