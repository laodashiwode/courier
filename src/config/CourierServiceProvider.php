<?php

namespace src\config;

use Illuminate\Support\ServiceProvider;

class CourierServiceProvider extends ServiceProvider
{
    use Illuminate\Support\ServiceProvider;


    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/test.php' => config_path('courier.php'),
        ]);
    }
}