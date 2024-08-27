<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'auth' => [
                'user' => Auth::user() ? [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->role,
                ] : null,
            ],
            'errors' => function () {
                return session()->get('errors')
                    ? session()->get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
        ]);
    }
}
