<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
        $prefix = (string) config('rental-hub.table_prefix', 'rental_');
        $unitsTable = $prefix . 'units';

        return [
            'unit_id' => ['required', 'integer', 'exists:' . $unitsTable . ',id'],
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date', 'after:start_time'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->filled('start_time')) {
                $start = strtotime((string) $this->input('start_time'));
                // Allow a small grace period of 2 minutes for clock drift between client and server
                if ($start !== false && $start < (time() - 120)) {
                    $validator->errors()->add('start_time', 'Waktu mulai sewa tidak boleh berada di masa lalu.');
                }
            }
        });
    }
}
