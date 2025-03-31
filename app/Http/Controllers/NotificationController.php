<?php

namespace App\Http\Controllers;

use App\Models\ServicesModel;
use App\Models\farmer\FarmerReportsModel;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        view()->composer('admin.partials.__nav', function ($view) {
            $notif = FarmerReportsModel::latest()->get(); 
            $unreadCount = $notif->where('status', 'unread')->count();
            $view->with('notif', $notif)->with('unreadCount', $unreadCount);
        });
        view()->composer('user.partials.__nav', function ($view) {
            $notif = ServicesModel::latest()->get(); 
            $unreadCount = $notif->where('status', 'Assigned')->count();
            $view->with('notif', $notif)->with('unreadCount', $unreadCount);
        });
    }
    public function adminNotif()
    { 
        $notif = FarmerReportsModel::latest()->get(); 
        $unreadCount = $notif->where('status', 'unread')->count();
        return view('admin.dashboard', compact('notif', 'unreadCount'));
    }
    public function showNotif() {
        return view('admin.pages.notification');
    }
    
    public function getNotifications()
    {
        $notifications = FarmerReportsModel::latest()->get(); 
        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $notification = FarmerReportsModel::with('technician')->findOrFail($id);
        $services = ServicesModel::pluck('farm_id')->toArray();
        // $exist = in_array($services, $notification->farm_id);
        $exist = in_array($notification->farm_id, $services);
        // dd($exist);
        $notification->update(['status' => 'read']);
        return view('admin.pages.notification', compact('notification', 'exist'));
        // return response()->json(['success' => true]);
    } 

    public function technicianNotif() {
        $technicianId = auth()->id();
        $notifications = ServicesModel::where('technician_id', $technicianId)
            ->select('id', 'request_id', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $notif = ServicesModel::latest()->get(); 
        $unreadCount = $notif->where('status', 'Assigned')->count();
        // return view('admin.dashboard', compact('notif', 'unreadCount'));

        return view('user.home', compact('notifications', 'notif', 'unreadCount'));
    }
     
    public function getTechNotifications()
    {
        $notifications = ServicesModel::latest()->get(); 
        return response()->json($notifications);
    }
    
    public function markAsReadTechNotif($id)
    {
        $notification = FarmerReportsModel::with('technician')->findOrFail($id);
        $services = ServicesModel::pluck('farm_id')->toArray();
        // $exist = in_array($services, $notification->farm_id);
        $exist = in_array($notification->farm_id, $services);
        // dd($exist);
        $notification->update(['status' => 'read']);
        return view('admin.pages.notification', compact('notification', 'exist'));
        // return response()->json(['success' => true]);
    } 


    public function viewDetails($id)
    {
        $notification = ServicesModel::findOrFail($id);
        $notification->update(['status' => 'Viewed']);

        $requestDetails = ServicesModel::with('farmers', 'user' ,'farmerReports')->findOrFail($id);
        return view('user.technician.pages.request_details', compact('requestDetails'));
    }
}
