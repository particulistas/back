<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Properties\PropertiesController;

Route::group(['prefix' => 'v1'], function () {
    Route::middleware('auth:api')->prefix('properties')->group(function () {
        Route::post('/store', [PropertiesController::class, 'store']);
    });
});