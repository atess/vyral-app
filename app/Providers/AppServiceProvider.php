<?php

namespace App\Providers;

use App\Http\Resources\TwitCollection;
use App\Http\Resources\TwitResource;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // notifikasyonlar durduruldu.
        Notification::fake();
    }
}
