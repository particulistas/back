<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversation;

    public function __construct(Message $message, Conversation $conversation)
    //public function __construct( $message,  $conversation)
    {
        $this->message = $message;
        $this->conversation = $conversation;
    }

    public function broadcastOn()
    {
        $channels = [];
        
        // Broadcast to both participants
        if ($this->conversation->user_id !== $this->message->sender_id) {
            $channels[] = new PrivateChannel('user.' . $this->conversation->user_id);
        }
        
        if ($this->conversation->participant_id !== $this->message->sender_id) {
            $channels[] = new PrivateChannel('user.' . $this->conversation->participant_id);
        }

        return $channels;
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message->load('sender:id,name'),
            'conversation_id' => $this->conversation->id
        ];
    }

    public function broadcastAs()
    {
        return 'message.sent';
    }
}