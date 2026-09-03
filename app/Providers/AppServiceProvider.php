<?php

namespace App\Providers;

use App\Domain\Login\Interface\LoginDomainInterface;
use App\Domain\Slider\Interface\SliderDomainInterface;
use App\Internal\Login\Repository\LoginRepository;
use App\Internal\Slider\Repository\SliderRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LoginDomainInterface::class, LoginRepository::class);
        $this->app->bind(SliderDomainInterface::class, SliderRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
