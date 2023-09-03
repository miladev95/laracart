<?php

namespace Miladev\Laracart\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return string
     */
    public function getTable(): string
    {
        return config('cart.table_name') ?? 'laracart';
    }
}