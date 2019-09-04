<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

//@todo how to move service class & interface away from this file?
interface HelloWorldInterface {
    public function hello();
}

class HelloWorldService implements HelloWorldInterface {
    public function hello() {
        return ['title' => 'Hello World'];
    }
}

class HelloWorldProvider extends ServiceProvider
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
        $this->app->singleton('HelloWorldService', function ($app) {
            return new HelloWorldService();
        });
    }
}
