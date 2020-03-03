<?php

namespace Wufly\Dingding;

use Illuminate\Support\ServiceProvider;

class DingdingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //publish
        $this->publishes([
            __DIR__ . '/config/dingding.php' => config_path('dingding.php'),
        ]);
    }
}
