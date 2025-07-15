<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantPeople extends Model
{
    use HasFactory;

    // Especifica el nombre de la tabla explícitamente
    protected $table = 'tenant_peoples';

    protected $fillable = [
        'tenant_id',
        'name',
        'age',
        'employment_situation',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
