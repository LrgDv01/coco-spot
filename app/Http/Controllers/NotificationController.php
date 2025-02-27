<?php

namespace App\Http\Controllers;

use App\Models\ServicesModel;
use App\Models\farmer\FarmerReportsModel;
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
    }

    public function index() {
        $technicianId = auth()->id();
        $notifications = ServicesModel::where('technician_id', $technicianId)
            ->select('id', 'request_id', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('user.home', compact('notifications'));
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
        $notification = FarmerReportsModel::findOrFail($id);
        $notification->update(['status' => 'read']);

        return view('admin.pages.notification', compact('notification'));
        // return response()->json(['success' => true]);
    }
 

}
