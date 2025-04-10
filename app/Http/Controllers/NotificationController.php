<?php

namespace App\Http\Controllers;

use App\Models\ServicesModel;
use App\Models\farmer\FarmerReportsModel;
use App\Models\technician\TechiniciansReportsModel;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        view()->composer('admin.partials.__nav', function ($view) {
            // $farmerNotif = FarmerReportsModel::latest()->get(); 
            // $techNotif = TechiniciansReportsModel::latest()->get();

            $notif = Notification::latest()->get();
            
            // Combine both collections
            // $allNotif = $farmerNotif->merge($techNotif);
            
            // Count unread from both
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
        // $notif = FarmerReportsModel::latest()->get(); 
        // $notif = TechiniciansReportsModel::latest()->get();
        // $unreadCount = $notif->where('status', 'unread')->count();
        // $farmerNotif = FarmerReportsModel::latest()->get(); 
        // $techNotif = TechiniciansReportsModel::latest()->get();
        $notif = Notification::latest()->get();
        // Combine both collections
        // $allNotif = $farmerNotif->merge($techNotif);
        
        // Count unread from both
        $unreadCount = $notif->where('status', 'unread')->count();
        return view('admin.dashboard', compact('notif', 'unreadCount'));
    }
    public function showNotif() {
        return view('admin.pages.notification');
    }
    
    public function getNotifications()
    {
        // $notifications = FarmerReportsModel::latest()->get(); 
        // $notifications = TechiniciansReportsModel::latest()->get();

        // $farmerNotif = FarmerReportsModel::latest()->get(); 
        // $techNotif = TechiniciansReportsModel::latest()->get();
        $notif = Notification::latest()->get();
        
        // Combine both collections
        // $allNotif = $farmerNotif->merge($techNotif);
        return response()->json($notif);
        // return response()->json($notifications);
    }

    // public function markAsRead($id)
    // {

    //     $notif = Notification::with('user')->findOrFail($id);

    //     // $techNotif->update(['status' => 'read']);

    //     $services = ServicesModel::pluck('farm_id')->toArray();
    //     // $exist = in_array($farmerNotif->farm_id, $services) || in_array($techNotif->farm_id, $services);

    //     $exist = ($notif->farm_id && in_array($notif->farm_id, $services));
    //     $notif->update(['status' => 'read']);

    //     // $farmerNotif = FarmerReportsModel::with('technician')->findOrFail($id);

    //     // if (FarmerReportsModel::count() !== 0){
    //     //     $farmerNotif = FarmerReportsModel::with('technician')->findOrFail(1);
    //     //     return view('admin.pages.notification', compact('farmerNotif', 'exist'));
    //     // } 
    //     // if (TechiniciansReportsModel::count() !== 0) {
    //     //     $techNotif = TechiniciansReportsModel::with('technician')->findOrFail(3);
    //     //     return view('admin.pages.notification', compact('techNotif','exist'));
    //     // }
    //     //  if (TechiniciansReportsModel::count() !== 0 && FarmerReportsModel::count() !== 0) {
    //     //     $farmerNotif = FarmerReportsModel::with('technician')->findOrFail($id);
    //     //     $techNotif = TechiniciansReportsModel::findOrFail($id);
    //     //     return view('admin.pages.notification', compact('farmerNotif', 'techNotif', 'exist'));
    //     // }

    //     return view('admin.pages.notification', compact('notif','exist'));

    // } 

    public function markAsRead($id)
    {
        // Eager load both the user and the report (polymorphic)
        $notification = Notification::with(['user', 'report'])->findOrFail($id);
        
        // Update status to 'read'
        $notification->update(['status' => 'read']);
        
        // Check if farm exists in services
        $services = ServicesModel::pluck('farm_id')->toArray();
        $exist = ($notification->farm_id && in_array($notification->farm_id, $services));
        
        // Determine the report type and load appropriate relationships
        if ($notification->report instanceof \App\Models\farmer\FarmerReportsModel) { 
            // If it's a farmer report, load the technician relationship
            $notification->report->load('technician');
        } elseif ($notification->report instanceof \App\Models\technician\TechiniciansReportsModel) { 
            // If it's a technician report, load the technician relationship
            $notification->report->load('technician');
        }
        
        return view('admin.pages.notification', [
            'notif' => $notification,
            'exist' => $exist
        ]);
    }


    public function technicianNotif() {
        $technicianId = auth()->id();
        $notifications = ServicesModel::where('technician_id', $technicianId)
            ->select('id', 'request_id', 'status', 'created_at')
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
