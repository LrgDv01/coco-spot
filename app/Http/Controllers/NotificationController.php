<?php

namespace App\Http\Controllers;

use App\Models\ServicesModel;
use App\Models\farmer\FarmerReportsModel;
use App\Models\technician\TechiniciansReportsModel;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View; 
use Illuminate\Support\Facades\Auth; 


class NotificationController extends Controller
{
    public function __construct()
    {
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

        // view()->composer('user.partials.__nav', function ($view) {
        //     $technicianId = auth()->id();
        //     // $notifications = ServicesModel::where('technician_id', $technicianId);
        //     $notif = ServicesModel::where('technician_id', $technicianId)->latest()->get(); 
        //     $unreadCount = $notif->where('status', 'Assigned')->count();
        //     $view->with('notif', $notif)->with('unreadCount', $unreadCount);
        // });

        // view()->composer('user.partials.__nav', function ($view) {
        //     $farmerId = auth()->id();
        //     // $notifications = ServicesModel::where('technician_id', $technicianId);
        //     $notif = ServicesModel::where('farmer_id', $farmerId)->latest()->get(); 
        //     $unreadCount = $notif->where('status', 'Acknowledged')->count();
        //     $view->with('notif', $notif)->with('unreadCount', $unreadCount);
        // });
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

    // Technicians notifications
    public function technicianNotif() {
        $technicianId = auth()->id();
        $notifications = ServicesModel::where('technician_id', $technicianId)
            ->whereIn('status', ['Assigned', 'Viewed'])
            ->select('id', 'request_id', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
            // $technicianId = auth()->id();
            // $notifications = ServicesModel::where('technician_id', $technicianId);
        $notif = ServicesModel::where('technician_id', $technicianId)->latest()->get(); 
        $unreadCount = $notif->where('status', 'Assigned')->count();
        // return view('admin.dashboard', compact('notif', 'unreadCount'));
        // dd($unreadCount);
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

    public function techViewDetails($id)
    {
        $notification = ServicesModel::findOrFail($id);
        $notification->update(['status' => 'Viewed']);
        $requestDetails = ServicesModel::with('farmers', 'user' ,'farmerReports')->findOrFail($id);
        return view('user.technician.pages.request_details', compact('requestDetails'));
    }

    // Farmer notifications
    public function farmerNotif() {
        $farmerId = auth()->id();
        $notifications = ServicesModel::where('farmer_id', $farmerId)
            ->where('status', 'Acknowledged')
            ->select('id', 'request_id', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
            // $technicianId = auth()->id();
            // $notifications = ServicesModel::where('technician_id', $technicianId);
        $notif = ServicesModel::where('farmer_id', $farmerId)->latest()->get(); 
        $unreadCount = $notif->where('status', 'Acknowledged')->count();
        // return view('admin.dashboard', compact('notif', 'unreadCount'));
        return view('user.pages.notifications', compact('notifications', 'notif', 'unreadCount'));
    }

    public function getFarmerNotifications()
    {
        $notifications = ServicesModel::latest()->get(); 
        return response()->json($notifications);
    }

     public function markAsReadFarmerNotif($id)
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

    public function farmerViewDetails($id)
    {
        $notification = ServicesModel::findOrFail($id);
        $notification->update(['status' => 'Acknowledged']);
        $requestDetails = ServicesModel::with('farmers', 'user' ,'farmerReports')->findOrFail($id);
        return view('user.pages.request_details', compact('requestDetails'));
    }
}
