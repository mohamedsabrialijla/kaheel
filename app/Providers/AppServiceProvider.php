<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;



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
        Schema::defaultStringLength(191);

        // روابط الويب (url/asset) تتبع الدومين الذي فتحه المستخدم — لا localhost من .env
        if ($this->app->runningInConsole()) {
            $root = rtrim((string) config('app.url'), '/');
            if ($root !== '') {
                URL::forceRootUrl($root);
            }
        }

        View::composer(['kaheel.*', 'kaheel.pages.*', 'kaheel.partials.*', 'kaheel.layouts.*'], function ($view) {
            if (! array_key_exists('home', $view->getData()) || blank($view->getData()['home'])) {
                $view->with('home', url(app()->getLocale()));
            }
        });
    }
}
