<?php

use App\Internal\Slider\Handler\SliderHandler;
use Illuminate\Support\Facades\Route;

Route::resources(['slider' => SliderHandler::class]);
