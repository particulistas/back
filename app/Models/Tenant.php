<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'room',
        'pets',
        'accept_no_smoking',
        'can_provide_documentation',
        'can_provide_references',
        'no_credit_issues',
        'not_real_estate_professional',
        'additional_info',
        'income_percentage',
        'minimum_stay',
    ];

    protected $casts = [
        'pets' => 'array',
        'room' => 'boolean',
        'accept_no_smoking' => 'boolean',
        'can_provide_documentation' => 'boolean',
        'can_provide_references' => 'boolean',
        'no_credit_issues' => 'boolean',
        'not_real_estate_professional' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function peoples()
    {
        return $this->hasMany(TenantPeople::class);
    }
}
