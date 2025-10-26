<?php

namespace App\Shared\Base;

trait BaseEnum
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function toArray(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->name => $case->value])
            ->toArray();
    }

    public static function labels(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => __(self::labelKey($case))])
            ->toArray();
    }

    public static function fromValue(string|int $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value){
                 return $case;
            }
        }
        return null;
    }


}
