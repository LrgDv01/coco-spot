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
    public function adminNotif()
    { 
        $notif = Notification::latest()->get();
        $unreadCount = $notif->where('status', 'unread')->count();
        return view('admin.dashboard', compact('notif', 'unreadCount'));
    }
    public function showNotif() {
        return view('admin.pages.notification');
    }
    
    public function getNotifications()
    {
        $notif = Notification::latest()->get();
        return response()->json($notif);
    }

    public function markAsRead($id)
    {
        $notification = Notification::with(['user', 'report'])->findOrFail($id);
        $notification->update(['status' => 'read']);
        $services = ServicesModel::pluck('farm_id')->toArray();
        $exist = ($notification->farm_id && in_array($notification->farm_id, $services));
        if ($notification->report instanceof \App\Models\farmer\FarmerReportsModel) { 
            $notification->report->load('technician');
        } elseif ($notification->report instanceof \App\Models\technician\TechiniciansReportsModel) { 
            $notification->report->load('technician');
        }
        return view('admin.pages.notification', [
            'notif' => $notification,
            'exist' => $exist
        ]);
    }

    // Technicians notification
    public function technicianNotif() {
        $technicianId = auth()->id();
        // $notifications = ServicesModel::where('technician_id', $technicianId)
        //     ->whereIn('remarks', ['Assigned by Admin', 'Viewed by Technician'])
        //     ->orWhereIn('status', ['Confirmed', 'Rescheduled'])
        //     // ->whereIn('status', ['Confirmed', 'Rescheduled'])
        //     // ->orWhereIn('status', ['Confirmed', 'Rescheduled', 'Unverified'])
        //     ->select('id', 'request_id', 'status', 'created_at', 'updated_at')
        //     ->orderBy('created_at', 'desc') 
        //     ->get();

        $notifications = ServicesModel::where(function($query) use ($technicianId) {
            $query->where('technician_id', $technicianId)
                      ->whereIn('remarks', ['Assigned by Admin', 'Viewed by Technician']);
            })
            ->orWhere(function($query) use ($technicianId) {
                $query->where('technician_id', $technicianId)
                      ->whereIn('status', ['Confirmed', 'Rescheduled', 'Unverified']);
            })
            ->select('id', 'request_id', 'status', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc') 
            ->get();
        $notif = ServicesModel::where('technician_id', $technicianId)->latest()->get(); 
        $unreadCount = $notif->where('remarks', 'Assigned by Admin')->count();
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
        $exist = in_array($notification->farm_id, $services);
        $notification->update(['status' => 'read']);
        return view('admin.pages.notification', compact('notification', 'exist'));
    } 

    public function techViewDetails($id)
    {
        $notification = ServicesModel::findOrFail($id);
        // Check and update remarks if needed
        if ($notification->status === 'Assigned' || $notification->remarks === 'Assigned by Admin') {
            // $notification->update(['remarks' => 'Acknowledged and Scheduled by Technician']);
            $notification->update(['status' => 'Viewed']);
            $notification->update(['remarks' => 'Viewed by Technician']);
        } else {
            $notification->update(['status' => 'Unverified']);
            $notification->update(['remarks' => 'Unverified by Technician']);
        }
        $requestDetails = ServicesModel::with('farmers', 'user' ,'farmerReports')->findOrFail($id);
        return view('user.technician.pages.request_details', compact('requestDetails'));
    }

    // Farmer notifications
    public function farmerNotif() {
        $farmerId = auth()->id();
        $notifications = ServicesModel::where('farmer_id', $farmerId)
            ->whereIn('remarks', ['Acknowledged and Scheduled by Technician', 'Viewed by Farmer'])
            ->select('id', 'request_id', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
        $notif = ServicesModel::where('farmer_id', $farmerId)->latest()->get(); 
        $unreadCount = $notif->where('remarks', 'Acknowledged and Scheduled by Technician')->count();
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
        $exist = in_array($notification->farm_id, $services);
        $notification->update(['status' => 'read']);
        return view('admin.pages.notification', compact('notification', 'exist'));
    } 
    public function farmerViewDetails($id)
    {
        $notification = ServicesModel::findOrFail($id);
        $notification->update(['status' => 'Viewed']);
        $notification->update(['remarks' => 'Viewed by Farmer']);
        $requestDetails = ServicesModel::with('farmers', 'user' ,'farmerReports')->findOrFail($id);
        return view('user.pages.request_details', compact('requestDetails'));
    }
}
