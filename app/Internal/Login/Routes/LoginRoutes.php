<?php

use App\Internal\Login\Handler\LoginHandler;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [LoginHandler::class, 'login'])->name('login');
    Route::middleware('auth:api')->group(function () {
        Route::get('/check', function () {
            return response()->json(['status' => 'ok']);
        });
    });
});
