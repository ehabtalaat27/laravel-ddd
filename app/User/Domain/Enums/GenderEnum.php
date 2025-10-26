<?php

namespace App\User\Domain\Enums;

use App\Shared\Base\BaseEnum;

enum GenderEnum: int
{
    use BaseEnum;

    case Male = 1;
    case Female = 2;

    /**
     * Get the label for a single enum case.
     */
    public function label(): string
    {
        return match ($this) {
            self::Male => __('male'),
            self::Female => __('female')
        };
    }
}
