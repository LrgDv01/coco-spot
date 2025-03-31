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
                    ->whereIn('status', ['Acknowledge', 'end']);
            })->first();
        
        return view('user.technician.pages.reports',
            compact(
                'report',
                'recipient',
        ));
    }
  
    // important > called on "technicianReport" function here
    // private function checkStatus() { 
    //     $existingReportsCount = FarmModel::whereHas('technicianReports')
    //         ->update(['condition' => 'is Healthy']);

    //     $technicianId = auth()->id();
    //     $farmIDs = FarmModel::whereHas('services')
    //         ->where('condition', 'is Infected')
    //         ->pluck('id')
    //         ->toArray();

    //     $existingReportsCount = TechiniciansReportsModel::where('technician_id', $technicianId)
    //         ->whereIn('farm_id', $farmIDs)
    //         ->count();

    //     if ($existingReportsCount === count($farmIDs)) {
    //         ServicesModel::where('technician_id', $technicianId)
    //             ->where('status', '!=', 'end')
    //             ->update(['status' => 'end']);
    //     }
    // }

    private function updateStatus($farm_id, $status)
    {
        // dd(gettype($farm_id), gettype($status));
        try {
            $updateStatus = ServicesModel::where('farm_id', $farm_id)->first();
            $updateStatus->status = $status; 
            $updateStatus->save();
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.'
            ], 500);
        }

        return view('user.technician.pages.reports');
    }

    public function technicianReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'farm_id' => 'required|integer',
                'farmer_name' => 'required|string',
                'contact_info' => 'required|string',
                'recipient' => 'required|string',
                'farm_location' => 'required|string',
                'district' => 'required|string',
                'farm_size' => 'required|numeric',
                'coconut_trees' => 'required|numeric',
                'coconut_variety' => 'required|string',
                'diagnosis_disease' => 'required|string',
                'treatment_recommendation' => 'required|string',
                'image_proof' => 'required|image|mimes:jpeg,png,jpg,gif,svg|min:0', // Fixed min:0 to max:2048
                'status' => 'required|string',
                'no_of_infected' => 'required|integer|min:1',
                'locations' => 'required|array',
                'locations.*' => 'required|string|regex:/^[-]?[0-9]{1,3}\.[0-9]+,[-]?[0-9]{1,3}\.[0-9]+$/',
            ]);

            // Ensure no_of_infected is an integer for comparison
            $noOfInfected = (int) $validated['no_of_infected'];
            $locationsCount = count($validated['locations']);

            if ($locationsCount !== $noOfInfected) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'locations' => ['The number of locations must match the number of infected trees.'],
                ]);
            }
            $imagePath = $request->file('image_proof')->store('images', 'public'); // Simplified since required
            $reportDetail = TechiniciansReportsModel::create([
                'technician_id' => auth()->id(),
                'farm_id' => $validated['farm_id'],
                'farmer_name' => $validated['farmer_name'],
                'contact_no' => $validated['contact_info'],
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
                'no_of_infected' => $noOfInfected,
                'coordinates' => $validated['locations'],
            ]);

            if ($reportDetail) {
                $this->updateStatus($validated['farm_id'], $validated['status']);
            }

            return response()->json(['success' => true, 'message' => 'Report submitted successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return redirect()->route('user.reports')->with('error', 'Validation failed: ' . implode(', ', $e->errors()['locations'] ?? ['Unknown error']));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('user.reports')->with('error', 'The specified report could not be found.');
        } catch (\Exception $e) {
            \Log::error('Error submitting report: ' . $e->getMessage(), [
                'Request Data' => $request->all(),
            ]);
            return redirect()->route('user.reports')->with('error', 'An unexpected error occurred. Please try again.');
        }
    }
}


