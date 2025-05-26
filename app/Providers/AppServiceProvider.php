<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View; 
use App\Models\Notification;
use App\Models\ServicesModel;
use Illuminate\Support\ServiceProvider;
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
        Schema::defaultStringLength(191);

        // This composer is for 'admin.partials.__nav'
        // If this also depends on a specific authenticated user type,
        // you might need a similar check inside, or ensure it's only for admins
        // who are always authenticated when this view is rendered.
        View::composer('admin.partials.__nav', function ($view) {
            $notif = Notification::latest()->get();
            $unreadCount = $notif->where('status', 'unread')->count();
            $view->with('notif', $notif)->with('unreadCount', $unreadCount);
        });

        // This is the problematic line. Remove it or comment it out,
        // as auth()->user() will be null here.
        // dd(auth()->user()->isTechnician());

        // Instead of conditionally registering the composer based on auth()->user() here,
        // register it unconditionally and do the checks *inside* the closure.
        View::composer('user.partials.__nav', function ($view) {
            $user = Auth::user(); // Get the authenticated user INSIDE the closure

            // Always good to check if a user is actually logged in
            if ($user) {
                // Now you can safely check the user's role
                if (method_exists($user, 'isTechnician') && $user->isTechnician()) {
                    $technicianId = $user->id; // Use $user->id for clarity
                    $notif = ServicesModel::where('technician_id', $technicianId)->latest()->get();
                    $unreadCount = $notif->where('status', 'Assigned')->count();
                    $view->with('notif', $notif)->with('unreadCount', $unreadCount);
                } elseif (method_exists($user, 'isFarmer') && $user->isFarmer()) {
                    $farmerId = $user->id; // Use $user->id for clarity
                    $notif = ServicesModel::where('farmer_id', $farmerId)->latest()->get();
                    $unreadCount = $notif->where('status', 'Acknowledged')->count();
                    $view->with('notif', $notif)->with('unreadCount', $unreadCount);
                } else {
                    // Optional: Handle cases where the user is logged in but is neither
                    // or if the view should have default empty data.
                    $view->with('notif', collect())->with('unreadCount', 0);
                }
            } else {
                // Optional: Handle cases where no user is logged in for this view,
                // if the view might be rendered for guests.
                // Provide default empty data.
                $view->with('notif', collect())->with('unreadCount', 0);
            }
        });
    }
}



// namespace App\Providers;

// use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Facades\View; // Import the View facade
// // use App\Models\farmer\FarmerReportsModel; 
// // use App\Models\technician\TechiniciansReportsModel;
// use App\Models\Notification;
// // use Illuminate\Support\Facades\Auth;
// use App\Models\ServicesModel;
// use Illuminate\Support\ServiceProvider;

// class AppServiceProvider extends ServiceProvider
// {
//     /**
//      * Register any application services.
//      */
//     public function register(): void
//     {
//         //
//     }

//     /**
//      * Bootstrap any application services.
//      */
      
//     public function boot(): void
//     {

//         Schema::defaultStringLength(191);
//         // View::composer('admin.partials.__nav', function ($view) { 
//         //     $notif = FarmerReportsModel::latest()->get(); 
//         //     $notif = TechiniciansReportsModel::latest()->get(); 
//         //     $unreadCount = $notif->where('status', 'unread')->count();
//         //     $view->with('notif', $notif)->with('unreadCount', $unreadCount);
//         // });
//         View::composer('admin.partials.__nav', function ($view) {
//             // $farmerNotif = FarmerReportsModel::latest()->get(); 
//             // $techNotif = TechiniciansReportsModel::latest()->get();
//             $notif = Notification::latest()->get();
//             // Combine both collections
//             // $allNotif = $farmerNotif->merge($techNotif);
//             // Count unread from both
//             $unreadCount = $notif->where('status', 'unread')->count();
//             $view->with('notif', $notif)->with('unreadCount', $unreadCount);
//         });
//         //     $user = Auth::user();
//         // if (auth()->user() && auth()->user()->isTechnician()) {
//             View::composer('user.partials.__nav', function ($view) {
//                 $technicianId = auth()->id();
//                 $notif = ServicesModel::where('technician_id', $technicianId)->latest()->get();
//                 $unreadCount = $notif->where('status', 'Assigned')->count();
//                 $view->with('notif', $notif)->with('unreadCount', $unreadCount);
//             });
//         // } else if(auth()->user() && auth()->user()->isFarmer()) { 
//             View::composer('user.partials.__nav', function ($view) {
//                 $farmerId = auth()->id();
//                 $notif = ServicesModel::where('farmer_id', $farmerId)->latest()->get();
//                 $unreadCount = $notif->where('status', 'Acknowledged')->count();
//                 $view->with('notif', $notif)->with('unreadCount', $unreadCount);
//             });
//         // }
//     }

// }
