<?php

namespace Miladev\Laracart\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'laracart';
    protected $fillable = ['product_id','quantity','price','name'];


}
