<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Profile\UserController;



/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');*/

Route::group(['prefix' => 'v1'], function () {
    
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/signup', [AuthController::class, 'store']);
    Route::post('/resend/email', [AuthController::class, 'resendMailVerified']);
    Route::post('/verified/{token}', [AuthController::class, 'verifiedMail'])->name('verified.mail');
    Route::post('/recovery/password/', [AuthController::class, 'recoveryPassword']);
    Route::post('/new/password/{token}', [AuthController::class, 'newPassword']);

    Route::middleware('auth:api')->group(function () {

        Route::patch('/update/user', [UserController::class, 'updateProfile']);
        Route::post('/logout', [AuthController::class, 'destroy']);

        Route::prefix('user')->group(function () {
            Route::get('/', [AuthController::class, 'getUsers']);
        });
    });
});