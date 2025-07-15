<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Relations\HasMany;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'api';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'user_id');
    }
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

     public function favoriteProperties()
    {
        return $this->belongsToMany(Property::class, 'favorites');
    } 

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'user_id');
    }

     public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    

}
