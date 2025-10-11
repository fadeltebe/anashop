<?php

namespace App\Providers;

use App\Listeners\MergeCartAfterLogin;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    protected $listen = [
        \Illuminate\Auth\Events\Login::class => [
            MergeCartAfterLogin::class,
        ],
    ];
}
