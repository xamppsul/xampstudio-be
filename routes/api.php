<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    require base_path('app/Internal/Login/Routes/LoginRoutes.php');
    require base_path('app/Internal/Slider/Routes/SliderRoutes.php');
});

#test endpoint
Route::post('/ping', fn() => response()->json(['message' => 'pong']));
Route::post('/pong', fn() => response()->json(['message' => 'ping']));
