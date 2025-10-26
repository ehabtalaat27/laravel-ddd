<?php

namespace App\User\Infrastructure\Models;

use App\User\Domain\Enums\FitnessLevelEnum;
use App\User\Domain\Enums\GenderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'height',
        'weight',
        'birthday',
        'gender',
        'fitness_level'
    ];
       protected $casts = [
        'fitness_level' => FitnessLevelEnum::class,
        'gender' => GenderEnum::class

    ];
}