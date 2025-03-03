<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
            'id',
            'user_id',
            'category_id',
            'transaction',
            'sale_price',
            'rental_price',
            'bills',
            'm_built',
            'm_usefull',
            'bathrooms',
            'number_plants',
            'number_habs',
            'distibutions',
            'state',
            'equipment',
            'ubication',
            'characteristics',
            'preferences',
            'cohabitation',
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
            'caracteristics_optionals'
    ];

    public function media(): HasMany
    {
        return $this->hasMany(Media::class, 'properties_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
