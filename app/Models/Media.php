<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    public function properties(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
