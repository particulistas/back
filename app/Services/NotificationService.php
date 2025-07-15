<?php

namespace App\Services;

use App\Models\Notification;
use App\Events\NewNotification;

use App\Models\User;
use Carbon\Carbon; // Añade esta línea de importación

class NotificationService
{
    public function createNotification($userId, $message, $type, $data = null)
    {
        $notification = Notification::create([
            'user_id' => $userId,
            'message' => $message,
            'type' => $type,
            'data' => $data,
            'is_read' => false
        ]);

        // Dispara evento de Pusher
        //event(new \App\Events\NewNotification($notification));
        //event(new NewNotification($notification));
          event(new NewNotification($notification));

        return $notification;
    }

    public function getUserNotifications($userId, $filters = [])
    {
        $query = Notification::where('user_id', $userId);
        
        // Filtro por tipo
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        // Filtro por fecha
        if (!empty($filters['start_date'])) {
            $startDate = Carbon::parse($filters['start_date']);
            $endDate = !empty($filters['end_date']) 
                ? Carbon::parse($filters['end_date'])
                : Carbon::now();
                
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        return $query->orderBy('created_at', 'desc')
                    ->get();
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->update(['is_read' => true]);
        return $notification;
    }

    public function deleteNotification($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->delete();
        return true;
    }

    public function deleteAllNotifications($userId)
    {
        return Notification::where('user_id', $userId)->delete();
    }

}