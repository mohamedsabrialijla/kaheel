<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([
            /**** OTHER MIDDLEWARE ALIASES ****/
            'localize'                => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'    => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect'   => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect'    => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'          => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            'QrCode' => SimpleSoftwareIO\QrCode\Facades\QrCode::class,
            'Image' => Intervention\Image\Facades\Image::class,
            


        ]);
        
        $middleware->web(append: [
            \App\Http\Middleware\PreferUserBearerToken::class,
        ]);

        // استثناء auth routes من CSRF verification
        $middleware->validateCsrfTokens(except: [
            '*/user/auth/*',
            '*/web/auth/*',
            'nafath/post-callback',
            'neoleap',
            'neoleap/*',
            'neoleap-callback',
            'api/neoleap',
            'api/neoleap/*',
            'api/neoleap-callback',
            'yakeen/debug',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
