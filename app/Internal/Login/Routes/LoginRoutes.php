<?php

use App\Internal\Login\Handler\LoginHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [LoginHandler::class, 'login'])->name('login');
    Route::middleware('auth:api')->group(function () {
        Route::get('/check', function (Request $request) {
            return response()->json(['status' => 'ok', 'data' => $request->user('api')]);
        });
    });
});
