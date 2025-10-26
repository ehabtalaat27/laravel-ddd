<?php

namespace App\User\Infrastructure\Database\Factories;

use App\User\Infrastructure\Models\UserInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserInfo>
 */
class UserInfoFactory extends Factory
{
    protected $model = UserInfo::class;

    public function definition(): array
    {
        return [
            'height' => $this->faker->numberBetween(150, 200) . ' cm',
            'weight' => $this->faker->numberBetween(50, 120) . ' kg',
            'birthday' => $this->faker->date('Y-m-d', '-18 years'),
            'gender' => $this->faker->randomElement([1, 2]), // 1=male, 2=female
            'fitness_level' => $this->faker->randomElement([1, 2, 3]), // 1=beginner, 2=intermediate, 3=advanced
        ];
    }
}
