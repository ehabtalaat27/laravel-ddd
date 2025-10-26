<?php

namespace App\User\Presentation\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'numeric', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'height' => ['required', 'numeric', 'min:0', 'max:255'],
            'weight' => ['required', 'numeric', 'min:0', 'max:255'],
            'birthday' => ['required', 'date'],
            'gender' => ['required', 'integer', 'in:1,2'],
            'fitness_level' => ['required', 'integer', 'in:1,2,3'],
            'password_confirmation' => ['required', 'string', 'min:8'],

        ];
    }
}
