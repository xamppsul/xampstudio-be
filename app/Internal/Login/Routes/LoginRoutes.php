<?php

use App\Internal\Login\Handler\LoginHandler;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [LoginHandler::class, 'login'])->name('login');
});
