<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RentalHub\StarterKit\Enums\BookingStatus;

class RentalBooking extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'booking_code',
        'user_id',
        'unit_id',
        'start_time',
        'end_time',
        'actual_return_time',
        'total_hours',
        'base_price',
        'late_fee',
        'total_price',
        'status',
        'notes',
        'cancellation_reason',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'actual_return_time' => 'datetime',
        'status' => BookingStatus::class,
        'total_hours' => 'integer',
        'base_price' => 'integer',
        'late_fee' => 'integer',
        'total_price' => 'integer',
    ];

    public function getTable(): string
    {
        return config('rental-hub.table_prefix', 'rental_') . 'bookings';
    }

    public function user(): BelongsTo
    {
        /** @var class-string<Model> $userModel */
        $userModel = config('rental-hub.user_model', 'App\\Models\\User');
        return $this->belongsTo($userModel, 'user_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(RentalUnit::class, 'unit_id')->withTrashed();
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::ACTIVE->value);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::PENDING->value);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', BookingStatus::ACTIVE->value)
            ->where('end_time', '<', Carbon::now());
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === BookingStatus::ACTIVE && $this->end_time->isPast();
    }

    public function getOverdueHoursAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }

        $now = Carbon::now();
        $diffSeconds = max(0, $now->getTimestamp() - $this->end_time->getTimestamp());
        return (int) ceil($diffSeconds / 3600);
    }

    public function getFormattedBasePriceAttribute(): string
    {
        $symbol = (string) config('rental-hub.currency_symbol', 'Rp ');
        return $symbol . number_format($this->base_price, 0, ',', '.');
    }

    public function getFormattedLateFeeAttribute(): string
    {
        $symbol = (string) config('rental-hub.currency_symbol', 'Rp ');
        return $symbol . number_format($this->late_fee, 0, ',', '.');
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        $symbol = (string) config('rental-hub.currency_symbol', 'Rp ');
        return $symbol . number_format($this->total_price, 0, ',', '.');
    }

    public function getFormattedStartTimeAttribute(): string
    {
        $format = (string) config('rental-hub.date_format', 'd M Y H:i');
        return $this->start_time->translatedFormat($format);
    }

    public function getFormattedEndTimeAttribute(): string
    {
        $format = (string) config('rental-hub.date_format', 'd M Y H:i');
        return $this->end_time->translatedFormat($format);
    }

    public function getFormattedActualReturnTimeAttribute(): ?string
    {
        if ($this->actual_return_time === null) {
            return null;
        }

        $format = (string) config('rental-hub.date_format', 'd M Y H:i');
        return $this->actual_return_time->translatedFormat($format);
    }
}
