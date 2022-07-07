<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/auth/login', [App\Http\Controllers\Api\AuthController::class, 'login']);


// protected routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::prefix('users')->group(function () {

        Route::get('/profile', function(Request $request) {
            return auth()->user();
        });

        Route::prefix('preferences')->group(function () {

            Route::get('/', [App\Http\Controllers\Api\PreferenceController::class, 'getPreference']);
            Route::post('/enable-2fa', [App\Http\Controllers\Api\PreferenceController::class, 'enable2fa']);
            Route::post('/disable-2fa', [App\Http\Controllers\Api\PreferenceController::class, 'disable2fa']);
        });

    });

    Route::post('/auth/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
});
