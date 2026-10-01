<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userModel = config('rental-hub.user_model', 'App\\Models\\User');
        $userTable = class_exists($userModel) ? (new $userModel())->getTable() : 'users';

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . $userTable . ',email'],
            'phone' => ['nullable', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
