<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'type',
        'is_read',
        'data'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array'
    ];

    // Relación con el usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Marcar como leída
 /*    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    // Método para transformar a array para la API
    public function toApiArray()
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'type' => $this->type,
            'is_read' => $this->is_read,
            'created_at' => $this->created_at,
            'data' => $this->data
        ];
    } */
}
