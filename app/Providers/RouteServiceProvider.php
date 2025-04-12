<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\RateLimiter;
class RouteServiceProvider extends ServiceProvider
{
    
    public const HOME = '/home'; // Define the HOME constant

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public static function redirectTo()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return '/admin';
        } elseif ($user->isFarmer()) {
            return '/user/farm';
        } elseif ($user->isTechnician()) {
            return '/user/notifications';
        }

        return '/welcome'; // Fallback for authenticated users without roles
    }

    // public static function redirectTo()
    // {
    //     if (Auth::check()) {
    //         if (Auth::user()->isAdmin()) {
    //             return '/admin'; // Redirect admin to admin dashboard
    //         } elseif (Auth::user()->isFarmer()) {
    //             return '/user/farm'; // Redirect farmer to farm page
    //         } elseif(Auth::user()->isTechnician()) {
    //             return '/user/notifications'; // Redirect technicins notifications page
    //         }
    //     } else {
    //         return '/welcome';
    //     }
    // }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
