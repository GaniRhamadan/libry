<?php

declare(strict_types=1);

namespace RentalHub\StarterKit\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'rental_role',
        'phone',
    ];
}
