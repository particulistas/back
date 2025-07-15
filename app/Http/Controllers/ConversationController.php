<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use App\Events\UserTyping;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function index2(): JsonResponse
    {
       // $userId = Auth::id();
         $userId = auth()->user()->id;
         //$userId = $id;
        //$userId = '53';
       // $conversations = Conversation::with('user')->findOrFail($userId);
       // $tenant = Tenant::with('peoples')->findOrFail($id);

        //$conversations = Conversation::where('user_id','=', $userId)->get();
//$conversations = Conversation::all();

      

   /*   ->with([
                  'media' => fn($q) => $q->orderBy('postition'),
                  'category',
                  'user.profile' // Si necesitas datos del perfil del usuario
              ])
 */

        $conversations = Conversation::with([
           // 'property:id,address,sale_price,user_id',
            'property:id,address,user_id,transaction,category_id',
            'property.user_id:id,name,email',
            'property.category:id,name', // Añade esta línea
            'lastMessage:id,content,created_at,sender_id',
            'lastMessage.sender:id,name'
        ])
        ->where(function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhere('participant_id', $userId);
        })
        ->withCount(['messages as unread_count' => function($query) use ($userId) {
            $query->where('sender_id', '!=', $userId)
                  ->where('is_read', false);
        }])
        ->orderBy('updated_at', 'desc')
        ->get()
        ->map(function($conversation) use ($userId) {
            // Determine the other user
            $conversation->other_user = $conversation->user_id === $userId 
                ? $conversation->participant 
                : $conversation->user;
            
            // Check if conversation is favorited
            $conversation->is_favorite = $conversation->favorites()
                ->where('user_id', $userId)
                ->exists();
                
            return $conversation;
        });

        return response()->json([
            'success' => true,
            'data' => $conversations
        ]);
    }

    public function index(): JsonResponse
{
    //$userId = auth()->user()->id;
    $userId = Auth::id();

    $conversations = Conversation::with([
        'property:id,transaction,address,sale_price,rental_price,user_id,category_id',
        'property.category:id,name', // Cargar la categoría
        'property.owner:id,name,email',
        'lastMessage:id,content,created_at,sender_id',
        'lastMessage.sender:id,name'
    ])
    ->where(function($query) use ($userId) {
        $query->where('user_id', $userId)
              ->orWhere('participant_id', $userId);
    })
    ->withCount(['messages as unread_count' => function($query) use ($userId) {
        $query->where('sender_id', '!=', $userId)
              ->where('is_read', false);
    }])
    ->orderBy('updated_at', 'desc')
    ->get()
    ->map(function($conversation) use ($userId) {
        // Verificar si la propiedad existe antes de acceder a sus relaciones
        if ($conversation->property) {
            // Cargar la categoría si no se cargó automáticamente
            if (!$conversation->property->relationLoaded('category')) {
                $conversation->property->load('category:id,name');
            }
        }
        
        $conversation->other_user = $conversation->user_id === $userId 
            ? $conversation->participant 
            : $conversation->user;
        
        $conversation->is_favorite = $conversation->favorites()
            ->where('user_id', $userId)
            ->exists();
            
        return $conversation;
    });

    return response()->json([
        'success' => true,
        'data' => $conversations
    ]);
}

    public function show(Conversation $conversation): JsonResponse
    {
      //  $this->authorize('view', $conversation);
        
        $conversation->load([
            'property:id,transaction,address,sale_price,rental_price,user_id,category_id',
            'property.category:id,name', 
            'property.owner:id,name,email',
            'user:id,name,email,is_online',
            'participant:id,name,email,is_online'

        ]);

        $userId = Auth::id();
        $conversation->other_user = $conversation->user_id === $userId 
            ? $conversation->participant 
            : $conversation->user;

        return response()->json([
            'success' => true,
            'data' => $conversation
        ]);
    }

    public function getMessages( $conversation): JsonResponse
    {
      //  $this->authorize('view', $conversation);
        
        $messages = Message::where('conversation_id', $conversation)
            ->with('sender:id,name,email')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $messages
        ]);
    }

    public function sendMessage(Request $request,  $conversationId): JsonResponse
    {
      //  $this->authorize('participate', $conversation);
        
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        // Buscar la conversación
        $conversation = Conversation::findOrFail($conversationId);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'content' => $request->content
        ]);

        // Update conversation timestamp
        $conversation->touch();

        $message->load('sender:id,name,email');

        // Broadcast message to other participants
        broadcast(new MessageSent($message, $conversation))->toOthers();

        return response()->json([
            'success' => true,
            'data' => $message
        ], 201);
    }

    public function markAsRead( $conversation): JsonResponse
    {
       // $this->authorize('participate', $conversation);
        
        Message::where('conversation_id', $conversation)
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read'
        ]);
    }

    public function toggleFavorite( $conversation): JsonResponse
    {
       // $this->authorize('participate', $conversation);
        
        $userId = Auth::id();
        $favorite = $conversation->favorites()->where('user_id', $userId)->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorite = false;
        } else {
            $conversation->favorites()->create(['user_id' => $userId]);
            $isFavorite = true;
        }

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite
        ]);
    }

    public function typing(Request $request,  $conversation): JsonResponse
    {
       // $this->authorize('participate', $conversation);
        
        broadcast(new UserTyping(Auth::user(), $conversation))->toOthers();

        return response()->json([
            'success' => true
        ]);
    }
}
