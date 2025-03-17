<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\ServicesModel;
use App\Events\NewFarmerReport;
use App\Models\farmer\FarmerReportsModel;
use App\Models\technician\TechiniciansReportsModel;
use App\Models\farmer\FarmModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;


class ServicesController extends Controller
{
    public function index()
    {   

    //  $municipal = FarmModel::pluck('municipal')->unique()->toArray();
    // $technician_id = User::where('user_type', 2)->whereIn('municipal', $municipal)->value('id');
    // dd($technician_id);
    // $all_technician_id = User::where('user_type', 2)->value('id');
    // $new_id = $technician_id ?? $all_technician_id;
    


        $userId = Auth::id(); 
        $farms = FarmModel::where('farmer_id', $userId)
        // ->orderBy('created_at', 'desc')->limit(4) // Order by created_at, newest first
            ->orderBy('created_at', 'desc') // Order by created_at, newest first
            ->get();
        $farmReports = FarmerReportsModel::with('farm')->where('farmer_id', $userId)
            ->pluck('farm_id')->toArray();
        $technicianReport = TechiniciansReportsModel::pluck('farm_id')->toArray();
        return view('user.pages.reports', compact('farms', 'farmReports', 'technicianReport'));
    }


    public function viewReport($id)
    {
        // $requestDetails = ServicesModel::with('farms')->findOrFail($id);
        // $farm = FarmModel::where('id', $id)->get(); 
        $farm = FarmModel::findOrFail($id);
        // foreach ($farm as $f) {
            // dd($farm->name);
        // }
        return view('user.others.submit_report', compact('farm'));
    }

    public function farmerReport(Request $request) {
        try {
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'farmer_name' => 'required|string|max:255',
                'contact_no' => 'required|string|max:255',
                'farm_location' => 'required|string|max:255',
                'farm_size' => 'required|numeric',
                'coconut_trees' => 'required|integer',
                'coconut_variety' => 'required|string|max:255',
                'symptoms' => 'nullable|array',
                'disease' => 'required|string|max:255',
                'image_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|min:0', 
            ]);
            $imagePath = null;
            if ($request->hasFile('image_proof')) {
                $imagePath = $request->file('image_proof')->store('images', 'public');
            }
            $farmer_id = auth()->id();
            $municipal = FarmModel::pluck('municipal')->unique()->toArray();
            $technician_id = User::where('user_type', 2)->whereIn('municipal', $municipal)->value('id');
            // dd($technician_id);
            $all_technician_id = User::where('user_type', 2)->value('id');
            $new_id = $technician_id ?? $all_technician_id;
            $report = FarmerReportsModel::with('farm')->create([
                'farmer_id' => $farmer_id,
                'farm_id' => $validated['farm_id'],
                'technician_id' => $new_id,
                'farmer_name' => $validated['farmer_name'],
                'contact_no' => $validated['contact_no'],
                'farm_location' => $validated['farm_location'],
                'farm_size' => $validated['farm_size'],
                'coconut_trees' => $validated['coconut_trees'],
                'coconut_variety' => $validated['coconut_variety'],
                'symptoms' => json_encode($validated['symptoms'] ?? []),
                'disease' => $validated['disease'],
                'image_path' => $imagePath, 
            ]);
            // Broadcast the event
            broadcast(new NewFarmerReport($report))->toOthers();
            // Broadcast the event
            // event(new NewFarmerReport($report));
            return response()->json(['success' => true, 'message' => 'Report saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
        return redirect()->back()->with('success', 'Report saved successfully');
    }
    public function viewSubmmittedReport($id)
    {
        $reported = FarmerReportsModel::with('farms')->where('farm_id', $id)->first();
        // $reported = FarmerReportsModel::with('farms')->findOrFail($id);
        $services = ServicesModel::pluck('farm_id')->toArray();
        $technicianReport = TechiniciansReportsModel::pluck('farm_id')->toArray();
        // dd($verified->diseaseVerified->farm_id);
        $treatmentRecommendation = TechiniciansReportsModel::where('farm_id', $id)->pluck('treatment_recommendation')->first();
        $assigned = in_array($id, $services);
        $verified = in_array($id, $technicianReport);
      
        // $notification->update(['status' => 'read']);
        // return view('admin.pages.notification', compact('notification', 'exist'));
        return view('user.others.submitted_report', compact('reported', 'assigned', 'verified', 'treatmentRecommendation'));
    }

    public function adminVerified(Request $request)
    {
        $validated = $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'farmer_id' => 'required|exists:users,id',
            'technician_id' => 'required|exists:users,id',
            'farmer_name' => 'required|string|max:255',
        ]);

        do {
            $request_id = rand(10000, 99999);
            $exists = ServicesModel::where('request_id', $request_id)->exists();
        } while ($exists);

        $confirmation = new ServicesModel();
        $confirmation->farm_id = $validated['farm_id'];
        $confirmation->farmer_id = $validated['farmer_id'];
        $confirmation->technician_id = $validated['technician_id'];
        $confirmation->farmer_name = $validated['farmer_name'];
        $confirmation->request_id = $request_id;
        $confirmation->status = 'assigned';

        try {
            $confirmation->save();
            return response()->json([
                'success' => true,
                'message' => 'Report verified successfully!'
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the report.'
            ], 500);
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
                'message' => "Report has been successfully {$status}."
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
