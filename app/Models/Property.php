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
        'rental_price',
        'm_built',
        'm_usefull',
        'bathrooms',
        'transaction',
        'transaction',
        'transaction',
        'ubication',
        'characteristics',
        'antique',
        'address',
        'latitude',
        'longitude',
        'hide_address',
        'top_floor',
        'door',
        'description',
        'optionals',
        'energy_certificate',
        'energy_certificate_yes',
        'publish_phone',
        'phone',
        'phone_characteristics',
        'status',
        'optionals'
    ];
}
