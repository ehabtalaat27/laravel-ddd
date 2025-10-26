<?php

namespace App\User\Domain\Entities;

class UserInfoEntity
{
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }

    public static function fromArray(array $attributes): static
    {
        return new static($attributes);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    // 🔧 Magic accessors
    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }
}
