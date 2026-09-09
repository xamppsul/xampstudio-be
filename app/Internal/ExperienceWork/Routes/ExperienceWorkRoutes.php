<?php

use App\Internal\ExperienceWork\Handler\ExperienceWorkHandler;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::resources(['experience_work' => ExperienceWorkHandler::class]);
});
