<?php

namespace callphone;

use Illuminate\Support\ServiceProvider;

/**
 * Класс авторизация на сайте с помощью звонка
 * @version 1.0.0
 * @author tioffs <timlab.ru>
 */

class CallServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Call::class, function ($app) {
            return new Call(config('callphone.api_key'));
        });
    }
}
