<?php

namespace App\Events;

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $conversation;

    //public function __construct(User $user, Conversation $conversation)
    public function __construct( $user,  $conversation)
    {
        $this->user = $user;
        $this->conversation = $conversation;
    }

    public function broadcastOn()
    {
        $channels = [];
        
        // Broadcast to the other participant
        if ($this->conversation->user_id !== $this->user->id) {
            $channels[] = new PrivateChannel('user.' . $this->conversation->user_id);
        }
        
        if ($this->conversation->participant_id !== $this->user->id) {
            $channels[] = new PrivateChannel('user.' . $this->conversation->participant_id);
        }

        return $channels;
    }

    public function broadcastWith()
    {
        return [
            'user' => $this->user->only(['id', 'name']),
            'conversation_id' => $this->conversation->id
        ];
    }

    public function broadcastAs()
    {
        return 'user.typing';
    }
}