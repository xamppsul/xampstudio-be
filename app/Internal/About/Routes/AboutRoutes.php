<?php

use App\Internal\About\Handler\AboutHandler;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::resources(['about' => AboutHandler::class]);
});
