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
        View::composer('admin.partials.__nav', function ($view) {
            $notif = Notification::latest()->get();
            $unreadCount = $notif->where('status', 'unread')->count();
            $view->with('notif', $notif)->with('unreadCount', $unreadCount);
        });
        View::composer('user.partials.__nav', function ($view) {
            $user = Auth::user(); 
            if ($user) {
                if (method_exists($user, 'isTechnician') && $user->isTechnician()) {
                    $technicianId = $user->id; 
                    $notif = ServicesModel::where('technician_id', $technicianId)->latest()->get();
                    $unreadCount = $notif->whereIn('status', ['Assigned', 'Confirmed', 'Rescheduled'])->count();
                    $view->with('notif', $notif)->with('unreadCount', $unreadCount);
                } elseif (method_exists($user, 'isFarmer') && $user->isFarmer()) {
                    $farmerId = $user->id; 
                    $notif = ServicesModel::where('farmer_id', $farmerId)->latest()->get();
                    $unreadCount = $notif->where('status', 'Acknowledged')->count();
                    $view->with('notif', $notif)->with('unreadCount', $unreadCount);
                } else {
                    $view->with('notif', collect())->with('unreadCount', 0);
                }
            } else {
                $view->with('notif', collect())->with('unreadCount', 0);
            }
        });
    }
}


