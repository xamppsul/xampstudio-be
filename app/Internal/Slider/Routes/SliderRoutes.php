<?php

use App\Internal\Slider\Handler\SliderHandler;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::resources(['slider' => SliderHandler::class]);
});
