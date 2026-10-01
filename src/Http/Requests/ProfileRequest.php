<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userModel = config('rental-hub.user_model', 'App\\Models\\User');
        $userTable = class_exists($userModel) ? (new $userModel())->getTable() : 'users';
        $userId = $this->user()?->getAuthIdentifier();

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique($userTable, 'email')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:25'],
            'current_password' => ['nullable', 'required_with:password', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
