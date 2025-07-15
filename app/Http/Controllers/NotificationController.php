<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;

class NotificationController extends Controller
{
   protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = [
            'type' => $request->query('type'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date')
        ];
        
        $notifications = $this->notificationService->getUserNotifications(
            $request->user()->id, 
            $query
        );
        
        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'type' => 'required|string',
            'data' => 'nullable|json'
        ]);

        $notification = $this->notificationService->createNotification(
            $request->user()->id,
            $validated['message'],
            $validated['type'],
            $validated['data'] ?? null
        );

        return response()->json($notification, 201);
    }


    public function markAsRead($id)
    {
        $notification = $this->notificationService->markAsRead($id);
        return response()->json($notification);
    }

    public function destroy($id)
    {
        $this->notificationService->deleteNotification($id);
        return response()->json(['message' => 'Notificación eliminada correctamente']);
    }

    public function destroyAll(Request $request)
    {
        $this->notificationService->deleteAllNotifications($request->user()->id);
        return response()->json(['message' => 'Todas las notificaciones fueron eliminadas']);
    }

   
}
