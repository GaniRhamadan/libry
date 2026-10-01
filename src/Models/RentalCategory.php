<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalCategory extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function getTable(): string
    {
        return config('rental-hub.table_prefix', 'rental_') . 'categories';
    }

    public function units(): HasMany
    {
        return $this->hasMany(RentalUnit::class, 'category_id');
    }
}
