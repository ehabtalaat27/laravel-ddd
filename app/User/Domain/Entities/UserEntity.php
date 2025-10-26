<?php

namespace App\User\Domain\Entities;

class UserEntity
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

    // 🧠 Domain logic
    public function deactivate(): void
    {
        $this->attributes['active'] = false;
    }

    public function activate(): void
    {
        $this->attributes['active'] = true;
    }

    // Optional convenience method
    public function isActive(): bool
    {
        return (bool) ($this->attributes['active'] ?? false);
    }
}
