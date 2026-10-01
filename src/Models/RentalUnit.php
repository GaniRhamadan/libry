<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use RentalHub\StarterKit\Enums\UnitStatus;

class RentalUnit extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [
        'category_id',
        'name',
        'code',
        'specifications',
        'price_per_hour',
        'price_per_day',
        'status',
        'late_fee_per_hour',
        'photo_path',
    ];

    protected $casts = [
        'specifications' => 'array',
        'status' => UnitStatus::class,
        'price_per_hour' => 'integer',
        'price_per_day' => 'integer',
        'late_fee_per_hour' => 'integer',
    ];

    public function getTable(): string
    {
        return config('rental-hub.table_prefix', 'rental_') . 'units';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RentalCategory::class, 'category_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(RentalBooking::class, 'unit_id');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', UnitStatus::AVAILABLE->value);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($search): void {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%");
        });
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $query->search((string) $filters['search']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    public function getFormattedPricePerHourAttribute(): string
    {
        $symbol = (string) config('rental-hub.currency_symbol', 'Rp ');
        return $symbol . number_format($this->price_per_hour, 0, ',', '.');
    }

    public function getFormattedPricePerDayAttribute(): string
    {
        $symbol = (string) config('rental-hub.currency_symbol', 'Rp ');
        return $symbol . number_format($this->price_per_day, 0, ',', '.');
    }

    public function getEffectiveLateFeePerHourAttribute(): int
    {
        return $this->late_fee_per_hour ?? (int) config('rental-hub.late_fee_per_hour', 50000);
    }

    public function getFormattedLateFeePerHourAttribute(): string
    {
        $symbol = (string) config('rental-hub.currency_symbol', 'Rp ');
        return $symbol . number_format($this->effective_late_fee_per_hour, 0, ',', '.');
    }

    public function getPhotoUrlAttribute(): string
    {
        $disk = (string) config('rental-hub.disk', 'public');

        if (!empty($this->photo_path) && Storage::disk($disk)->exists($this->photo_path)) {
            return Storage::disk($disk)->url($this->photo_path);
        }

        // Return a crisp, SVG vehicle silhouette data URI when no photo is uploaded
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="260" viewBox="0 0 400 260" fill="none">'
            . '<rect width="400" height="260" fill="#F1F5F9"/>'
            . '<path d="M90 160L125 105C130 97 138 92 148 92H252C262 92 270 97 275 105L310 160H325C330.5 160 335 164.5 335 170V195C335 197.8 332.8 200 330 200H315C315 211 306 220 295 220C284 220 275 211 275 200H125C125 211 116 220 105 220C94 220 85 211 85 200H70C67.2 200 65 197.8 65 195V170C65 164.5 69.5 160 75 160H90Z" fill="#CBD5E1"/>'
            . '<circle cx="105" cy="200" r="14" fill="#64748B"/>'
            . '<circle cx="295" cy="200" r="14" fill="#64748B"/>'
            . '<path d="M135 150L155 108H245L265 150H135Z" fill="#E2E8F0"/>'
            . '</svg>';

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }
}
