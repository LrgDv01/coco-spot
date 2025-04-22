<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View; // Import the View facade
// use App\Models\farmer\FarmerReportsModel; 
// use App\Models\technician\TechiniciansReportsModel;
use App\Models\Notification;

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
        // View::composer('admin.partials.__nav', function ($view) { 
        //     $notif = FarmerReportsModel::latest()->get(); 
        //     $notif = TechiniciansReportsModel::latest()->get(); 
        //     $unreadCount = $notif->where('status', 'unread')->count();
        //     $view->with('notif', $notif)->with('unreadCount', $unreadCount);
        // });
        View::composer('admin.partials.__nav', function ($view) {
            // $farmerNotif = FarmerReportsModel::latest()->get(); 
            // $techNotif = TechiniciansReportsModel::latest()->get();
            $notif = Notification::latest()->get();
            // Combine both collections
            // $allNotif = $farmerNotif->merge($techNotif);
            // Count unread from both
            $unreadCount = $notif->where('status', 'unread')->count();
            $view->with('notif', $notif)->with('unreadCount', $unreadCount);
        });

        View::composer('user.partials.__nav', function ($view) {
            $technicianId = auth()->id();
            $notif = ServicesModel::where('technician_id', $technicianId)->latest()->get();
            $unreadCount = $notif->where('status', 'Assigned')->count();
            $view->with('notif', $notif)->with('unreadCount', $unreadCount);
        });
    }
 
}
