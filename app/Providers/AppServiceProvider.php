<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View; // Import the View facade
use App\Models\farmer\FarmerReportsModel; //
use App\Models\ServicesModel;
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
        Schema::defaultStringLength(191);

        View::composer('admin.partials.__nav', function ($view) {
            $notif = FarmerReportsModel::latest()->get();
            $unreadCount = $notif->where('status', 'unread')->count();
            $view->with('notif', $notif)->with('unreadCount', $unreadCount);
        });

        View::composer('user.partials.__nav', function ($view) {
            $notif = ServicesModel::latest()->get();
            $unreadCount = $notif->where('status', 'Assigned')->count();
            $view->with('notif', $notif)->with('unreadCount', $unreadCount);
        });
    }
 
}
