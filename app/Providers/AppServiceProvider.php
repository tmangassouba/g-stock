<?php

namespace App\Providers;

use App\Models\Operation;
use App\Models\Product;
use App\Models\User;
use App\Observers\OperationObserver;
use App\Observers\ProductObserver;
use App\Observers\UserObserver;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        Product::observe(ProductObserver::class);
        User::observe(UserObserver::class);
        Operation::observe(OperationObserver::class);

        Inertia::share([
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
        ]);

        Inertia::share('flash', function () {
            return [
                'message' => Session::get('message'),
            ];
        });
    }
}
