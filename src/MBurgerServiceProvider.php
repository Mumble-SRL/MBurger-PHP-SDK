<?php

namespace Mumble\MBurger;

use Illuminate\Support\ServiceProvider;

class MBurgerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/mburger.php' => config_path('mburger.php'),
        ]);
    }
}
