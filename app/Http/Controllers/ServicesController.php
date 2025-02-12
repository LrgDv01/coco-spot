<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\ServicesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;



class ServicesController extends Controller
{
    public function index()
    {   
        $technicians = User::where('user_type', 2)
            ->select('id', 'full_name', 'address', 'contact')
            ->get();

        $user = auth()->id();
        $hiredTechnicians = ServicesModel::where( 'farmer_id', $user)
            ->select('technician_id','status')
            ->get();
      
        $hirebySomeone = ServicesModel::where([['farmer_id','!=', $user], ['status', 'accepted']])
            ->pluck('technician_id')
            ->toArray();
            
        return view('user.pages.services', [
            'technicians' => $technicians,
            'hiredTechnicians' => $hiredTechnicians,
            'hirebySomeone' => $hirebySomeone
        ]);
    }


    public function hireTechnician(Request $request)
    {
        $request->validate([
            'technician_id' => 'required|integer',
        ]);
        $user_id = auth()->id();
        $farmer_name = auth()->user()->full_name;
        $request_id = rand(10000, 99999);
        $confirmation = new ServicesModel();
        $confirmation->farmer_id = $user_id;
        $confirmation->technician_id = $request->technician_id; 
        $confirmation->farmer_name = $farmer_name;
        $confirmation->request_id = $request_id;
        $confirmation->status = 'pending';
        $confirmation->save();

        Session::flash('success', 'Technician hired successfully.');
        return response()->json(['success' => true,  'message' => 'Technician hired successfully!']);
    }

    public function hired() {
        $user = auth()->id();
        $hired = ServicesModel::select('technician_id')
            ->where('farmer_id', $user)
            ->get();
        if ($hired) {
            return response()->json([
                'success' => true,
                'hired' => $hired,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Hired not found'
            ], 404);
        }
    }
    
    public function updateStatus(Request $request, $id, $status)
    {
        try {
            $requestDetails = ServicesModel::findOrFail($id);
            $requestDetails->status = $status;
            $requestDetails->save();
    
            return response()->json([
                'success' => true,
                'message' => "Request has been successfully {$status}."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.'
            ], 500);
        }
    }
    
    public function show($id) {
        $technician = User::join('technicians_info', 'users.id', '=', 'technicians_info.technician_id')
            ->where('users.id', $id)
            ->select('users.*', 'technicians_info.*')
            ->first();
        if ($technician) {
            return response()->json([
                'success' => true,
                'technician' => $technician
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Technician not found'
            ], 404);
        }
    }
}
