<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && ($this->user()->rental_role ?? 'customer') === 'admin';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $prefix = (string) config('rental-hub.table_prefix', 'rental_');
        $categoriesTable = $prefix . 'categories';
        $categoryId = $this->route('category')?->id ?? $this->input('category_id');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique($categoriesTable, 'slug')->ignore($categoryId)],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
