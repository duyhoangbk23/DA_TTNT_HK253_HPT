<?php

namespace App\Providers;

use App\Support\MockData;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Chia sẻ dữ liệu dùng chung cho layout (navbar, sidebar) tới mọi view.
        View::composer('layouts.*', function ($view) {
            $view->with('currentUser', MockData::currentUser())
                 ->with('navNotifications', MockData::notifications());
        });
    }
}
