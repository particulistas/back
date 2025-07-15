<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Profile\UserController;
use App\Http\Controllers\Utilities\CategoryController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\Properties\PropertiesController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ConversationController;

Route::group(['prefix' => 'v1'], function () {
    
    /*** Authentications ***/
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/signup', [AuthController::class, 'store']);
    Route::post('/resend/email', [AuthController::class, 'resendMailVerified']);
    Route::post('/verified', [AuthController::class, 'verifiedMail']);
    Route::post('/recovery/password/', [AuthController::class, 'recoveryPassword']);
    Route::post('/new/password/{token}', [AuthController::class, 'newPassword']);

    /*** Utilities ***/
    Route::get('/categories/main', [CategoryController::class, 'getMainCategories']);
    Route::get('/categories/children/{parentId}', [CategoryController::class, 'getChildCategories']);

    /*** Properties ***/
    Route::post('/properties/first-step', [PropertiesController::class, 'storeFirstStep']);
    Route::post('/properties/update-first-step', [PropertiesController::class, 'updateFirstStep']);
    Route::post('/properties/second-step', [PropertiesController::class, 'storeSecondStep']);
    Route::post('/properties/third-step', [PropertiesController::class, 'storeThirdStep']);
    Route::post('/properties/fourth-step', [PropertiesController::class, 'storeFourthStep']);
    Route::post('/properties/media', [PropertiesController::class, 'storeMedia']);
    Route::post('/properties/update-status', [PropertiesController::class, 'updateStatusProperties']);
    Route::get('/properties/{id}', [PropertiesController::class, 'show']);
    Route::get('/properties/user/{user_id}', [PropertiesController::class, 'showByUserId']);
    Route::get('/properties/all/all', [PropertiesController::class, 'showAll']);

    /*** Users ***/
    Route::prefix('users')->group(function () {
        Route::get('{id}', [UserController::class, 'show']);
        Route::put('{id}', [UserController::class, 'update']);
    });

    /*** Avatar ***/
    Route::post('/upload-avatar', [AvatarController::class, 'upload']);
    Route::post('/update-avatar', [AvatarController::class, 'updateAvatar']);

    /*** Rutas protegidas ***/
    /* Route::middleware(['auth:sanctum'])->group(function () {
        // Asegúrate de usar 'auth:sanctum' consistentemente
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/{property}', [FavoriteController::class, 'store']);
        Route::delete('/favorites/{property}', [FavoriteController::class, 'destroy']);
        
        // Agrega aquí otras rutas que requieran autenticación
    }); */

    // routes/api.php
   // Route::middleware('auth:api')->group(function () {
        Route::get('/favorites/{id}', [FavoriteController::class, 'userFavorites']);
        Route::post('/favorites/{id}', [FavoriteController::class, 'store']);
        Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy']);
        Route::get('/is-favorite/{id}', [FavoriteController::class, 'checkFavorite']);
   // });

   // Obtener tenant por user_id
    Route::get('/tenants', [TenantController::class, 'getByUserId']);
    // Operaciones CRUD para tenants
    Route::apiResource('tenants', TenantController::class)->except(['index']);

  /*   Route::middleware('auth:api')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    }); */

    Route::middleware('auth:api')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications', [NotificationController::class, 'store']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);
        Route::post('/notifications/delete-all', [NotificationController::class, 'destroyAll']);

    });

   // Route::middleware('auth:api')->group(function () {
        // Conversations
        Route::get('/conversations', [ConversationController::class, 'index']);
        Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);
        Route::get('/conversations/{conversation}/messages', [ConversationController::class, 'getMessages']);
        Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage']);
        Route::post('/conversations/{conversation}/read', [ConversationController::class, 'markAsRead']);
        Route::post('/conversations/{conversation}/favorite', [ConversationController::class, 'toggleFavorite']);
        Route::post('/conversations/{conversation}/typing', [ConversationController::class, 'typing']);
        
        // Users
        Route::get('/blocked-users', [UserController::class, 'getBlockedUsers']);
        Route::post('/users/{user}/block', [UserController::class, 'blockUser']);
        Route::post('/users/{user}/unblock', [UserController::class, 'unblockUser']);
  //  });

        Route::post('/pusher/auth', function (Request $request) {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $pusher = new Pusher\Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                [
                    'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                    'encrypted' => true
                ]
            );

            return $pusher->socket_auth($request->channel_name, $request->socket_id);
        });//->middleware('auth:sanctum'); 
        // // Ajusta el middleware según tu sistema de autenticación

});