<?php
namespace App\Http\Controllers\user\technician;

use App\Models\User;
use App\Models\ServicesModel;
use App\Models\technician\TechiniciansReportsModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use App\Http\Controllers\ServicesController;
use App\Models\farmer\FarmModel;

class ReportsController extends Controller
{
    public function index()
    {
        $technicianId = auth()->id();
        $recipient = auth()->user()->full_name;
        $report = ServicesModel::with(['user', 'userFarms', 'farmerReports'])
            ->where(function ($query) use ($technicianId) {
                $query->where('technician_id', $technicianId)
                    ->whereIn('status', ['acknowledge', 'end']);
            })->first();
        
        return view('user.technician.pages.reports',
            compact(
                'report',
                'recipient',
        ));
    }

    // important > called on "technicianReport" function here
    private function checkStatus() { 
        $existingReportsCount = FarmModel::whereHas('technicianReports')
            ->update(['condition' => 'is Healthy']);

        $technicianId = auth()->id();
        $farmIDs = FarmModel::whereHas('services')
            ->where('condition', 'is Infected')
            ->pluck('id')
            ->toArray();

        $existingReportsCount = TechiniciansReportsModel::where('technician_id', $technicianId)
            ->whereIn('farm_id', $farmIDs)
            ->count();

        if ($existingReportsCount === count($farmIDs)) {
            ServicesModel::where('technician_id', $technicianId)
                ->where('status', '!=', 'end')
                ->update(['status' => 'end']);
        }
    }

    public function technicianReport(Request $request)
    {
        try {
            // $validated = $request->validate([
            //     'farm_id' => 'required|integer',
            //     'farmer_name' => 'required|string',
            //     'contact_info' => 'required|string',
            //     'recipient' => 'required|string',
            //     'farm_location' => 'required|string',
            //     'district' => 'required|string',
            //     'farm_size' => 'required|string', 
            //     'coconut_trees' => 'required|numeric', 
            //     'coconut_variety' => 'required|string',
            //     'diagnosis_disease' => 'required|string',
            //     'treatment_recommendation' => 'required|string',
            //     'image_proof' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            //     'status' => 'required|string',
            //     'no_of_infected' => 'required|integer',
            //     'locations' => 'required|array'

            // ]);

 
            // $imagePath = null;
            // if ($request->hasFile('image_proof')) {
            //     $imagePath = $request->file('image_proof')->store('images', 'public');
            // }
            // $reportDetail = TechiniciansReportsModel::create([
            //     'technician_id' => auth()->id(),
            //     'farm_id' => $validated['farm_id'],
            //     'farmer_name' => $validated['farmer_name'],
            //     'contact_no' => $validated['contact_info'],
            //     'recipient' => $validated['recipient'],
            //     'farm_location' => $validated['farm_location'],
            //     'district' => $validated['district'],
            //     'farm_size' => $validated['farm_size'],
            //     'coconut_trees' => $validated['coconut_trees'],
            //     'coconut_variety' => $validated['coconut_variety'],
            //     'diagnosis_disease' => $validated['diagnosis_disease'],
            //     'treatment_recommendation' => $validated['treatment_recommendation'],
            //     'image_proof' => $imagePath,
            //     'status' => $validated['status'],
            //     'no_of_infected' => $validated['no_of_infected'],
            //     'coordinates' =>  $validated['locations'],
            // ]);

            $validated = $request->validate([
                'farm_id' => 'required|integer',
                'farmer_name' => 'required|string',
                'contact_info' => 'required|string',
                'recipient' => 'required|string',
                'farm_location' => 'required|string',
                'district' => 'required|string',
                'farm_size' => 'required|numeric', // Changed from string to numeric
                'coconut_trees' => 'required|numeric',
                'coconut_variety' => 'required|string',
                'diagnosis_disease' => 'required|string',
                'treatment_recommendation' => 'required|string',
                'image_proof' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Changed nullable to required
                'status' => 'required|string',
                'no_of_infected' => 'required|integer|min:1', // Added min:1 to ensure positive value
                'locations' => 'required|array', // Basic array validation
                'locations.*' => 'required|string|regex:/^[-]?[0-9]{1,3}\.[0-9]+,[-]?[0-9]{1,3}\.[0-9]+$/' // Validate each coordinate
            ]);
            
            // Check if the number of locations matches no_of_infected
            if (count($validated['locations']) !== $validated['no_of_infected']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'locations' => ['The number of locations must match the number of infected trees.'],
                ]);
            }
            
            $imagePath = null;
            if ($request->hasFile('image_proof')) {
                $imagePath = $request->file('image_proof')->store('images', 'public');
            }
            
            $reportDetail = TechiniciansReportsModel::create([
                'technician_id' => auth()->id(),
                'farm_id' => $validated['farm_id'],
                'farmer_name' => $validated['farmer_name'],
                'contact_no' => $validated['contact_info'], // Assuming model uses contact_no
                'recipient' => $validated['recipient'],
                'farm_location' => $validated['farm_location'],
                'district' => $validated['district'],
                'farm_size' => $validated['farm_size'],
                'coconut_trees' => $validated['coconut_trees'],
                'coconut_variety' => $validated['coconut_variety'],
                'diagnosis_disease' => $validated['diagnosis_disease'],
                'treatment_recommendation' => $validated['treatment_recommendation'],
                'image_proof' => $imagePath,
                'status' => $validated['status'],
                'no_of_infected' => $validated['no_of_infected'],
                'coordinates' => $validated['locations'], // Assuming model uses coordinates
            ]);

            // $this->checkStatus();
            return response()->json(['success' => true, 'message' => 'Report submitted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('user.reports')->with('error', 'The specified report could not be found.');
        } catch (\Exception $e) {
            Log::error('Error submitting report: ' . $e->getMessage(), [
                'Request Data' => $request->all(),
            ]);
            return redirect()->route('user.reports')->with('error', 'An unexpected error occurred. Please try again.');
        }
        return redirect()->route('user.reports')->with('success', 'Report submitted successfully');
    }
}


