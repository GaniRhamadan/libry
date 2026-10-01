<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitRequest extends FormRequest
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
        $unitsTable = $prefix . 'units';
        $unitId = $this->route('unit')?->id ?? $this->input('unit_id');

        return [
            'category_id' => ['required', 'integer', 'exists:' . $categoriesTable . ',id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique($unitsTable, 'code')->ignore($unitId)],
            'price_per_hour' => ['required', 'integer', 'min:0'],
            'price_per_day' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', Rule::in(['available', 'rented', 'maintenance'])],
            'late_fee_per_hour' => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'spec_keys' => ['nullable', 'array'],
            'spec_keys.*' => ['nullable', 'string', 'max:100'],
            'spec_values' => ['nullable', 'array'],
            'spec_values.*' => ['nullable', 'string', 'max:255'],
        ];
    }
}
