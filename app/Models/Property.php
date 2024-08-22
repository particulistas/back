<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'transaction',
        'sale_price',
        'rental_price',
        'm_built',
        'm_usefull',
        'bathrooms',
        'transaction',
        'transaction',
        'transaction',
        'rental_price'
    ];
}
