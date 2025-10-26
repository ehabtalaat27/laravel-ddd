<?php

namespace App\User\Infrastructure\Database\Factories;

use App\User\Infrastructure\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

        protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
    
    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->info()->create(
                UserInfoFactory::new()->make()->toArray()
            );
        });
    }
}
