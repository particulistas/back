<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Profile\UserController;
use App\Http\Controllers\Utilities\CategoryController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\Properties\PropertiesController;
use App\Http\Controllers\FavoriteController;

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

});