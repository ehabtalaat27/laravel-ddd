<?php

namespace App\User\Domain\Enums;

use App\Shared\Base\BaseEnum;

enum FitnessLevelEnum: int
{
    use BaseEnum;

    case Beginner = 1;
    case Intermediate = 2;
    case Advanced = 3;

    /**
     * Get the label for a single enum case.
     */
    public function label(): string
    {
        return match ($this) {
            self::Beginner => __('Beginner'),
            self::Intermediate => __('Intermediate'),
            self::Advanced => __('Advanced'),
        };
    }

    /**
     * Get a Bootstrap color for each level (optional).
     */
    public function color(): string
    {
        return match ($this) {
            self::Beginner => 'secondary',
            self::Intermediate => 'info',
            self::Advanced => 'success',
        };
    }
}
