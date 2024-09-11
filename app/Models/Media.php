<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'properties_id',
        'name',
        'path',
        'type',
        'object', //imagen de la propiedad o del plano de la propiedad
        'postition'
    ];

    public function properties(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'properties_id');
    }
}
