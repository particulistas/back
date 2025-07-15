<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;

class NewNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        //return new Channel('user.' . $this->notification->user_id);
        return new Channel('user.' . $this->notification->user_id);
       // return new Channel('user.53');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'user_id' => $this->notification->user_id,
            'message' => $this->notification->message,
            'type' => $this->notification->type,
            'created_at' => $this->notification->created_at->toDateTimeString(),
            'is_read' => $this->notification->is_read
        ];
    }
}

