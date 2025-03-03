<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\ServicesModel;
use App\Events\NewFarmerReport;
use App\Models\farmer\FarmerReportsModel;
use App\Models\farmer\FarmModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;



class ServicesController extends Controller
{
    public function index()
    {   
        $userId = Auth::id(); 
        $farms = FarmModel::where('farmer_id', $userId)
        // ->orderBy('created_at', 'desc')->limit(4) // Order by created_at, newest first
        ->orderBy('created_at', 'desc') // Order by created_at, newest first
        ->get();
      
        return view('user.pages.reports', compact('farms'));
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
                'image_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $imagePath = null;
            if ($request->hasFile('image_proof')) {
                $imagePath = $request->file('image_proof')->store('images', 'public');
            }

            $report = FarmerReportsModel::create([
                'farmer_id' => auth()->id() ?? null,
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
