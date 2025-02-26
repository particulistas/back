<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Profile\UserController;
use App\Http\Controllers\Utilities\CategoryController;
use App\Http\Controllers\AvatarController;

Route::group(['prefix' => 'v1'], function () {
    
    /*** Authentications ***/

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/signup', [AuthController::class, 'store']);
    Route::post('/resend/email', [AuthController::class, 'resendMailVerified']);
   // Route::post('/verified/{token}', [AuthController::class, 'verifiedMail'])->name('verified.mail');
    Route::post('/verified', [AuthController::class, 'verifiedMail']);
    Route::post('/recovery/password/', [AuthController::class, 'recoveryPassword']);
    Route::post('/new/password/{token}', [AuthController::class, 'newPassword']);

    /*** Utilities ****/

   // Route::get('/list/categories', [CategoryController::class, 'show']);

    Route::middleware('auth:api')->group(function () {
        /*** Authentications logued***/

        // Route::patch('/update/user', [UserController::class, 'updateProfile']);
        // Route::post('/logout', [AuthController::class, 'destroy']);

        /*** Utilities logued****/

       /*  Route::prefix('user')->group(function () {
            Route::get('/', [AuthController::class, 'getUsers']);
        }); */


    });

    Route::prefix('users')->group(function () {
        //Route::get('/', [UserController::class, 'index']);
       // Route::get('create', [UserController::class, 'create']);
       // Route::post('/', [UserController::class, 'store']);
        Route::get('{id}', [UserController::class, 'show']);
       // Route::get('edit', [UserController::class, 'edit']);
        Route::put('{id}', [UserController::class, 'update']);
       // Route::delete('{id}', [UserController::class, 'destroy']);
        //Route::get('searchUsers/{id}', [UserController::class, 'searchUsers']);
        //Route::get('identify/{email}', [UserController::class, 'identify']);
       
    });

    Route::post('/upload-avatar', [AvatarController::class, 'upload']);
    Route::post('/update-avatar', [AvatarController::class, 'updateAvatar']);

});