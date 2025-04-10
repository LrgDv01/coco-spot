<?php
namespace App\Http\Controllers\user\technician;

use App\Models\User;
use App\Models\ServicesModel;
use App\Models\technician\TechiniciansReportsModel;
use App\Models\Notification;
use App\Events\NewFarmerReport;
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

    // public function technicianReport(Request $request)
    // {
    //     try {
    //         $validated = $request->validate([
    //             'farm_id' => 'required|integer',
    //             'farmer_name' => 'required|string',
    //             'contact_info' => 'required|string',
    //             'recipient' => 'required|string',
    //             'farm_location' => 'required|string',
    //             'district' => 'required|string',
    //             'farm_size' => 'required|numeric',
    //             'coconut_trees' => 'required|numeric',
    //             'coconut_variety' => 'required|string',
    //             'diagnosis_disease' => 'required|string',
    //             'treatment_recommendation' => 'required|string',
    //             'image_proof' => 'required|image|mimes:jpeg,png,jpg,gif,svg|min:0', // Fixed min:0 to max:2048
    //             'status' => 'required|string',
    //             'no_of_infected' => 'required|integer|min:1',
    //             'locations' => 'required|array',
    //             'locations.*' => 'required|string|regex:/^[-]?[0-9]{1,3}\.[0-9]+,[-]?[0-9]{1,3}\.[0-9]+$/',
    //         ]);

    //         // Ensure no_of_infected is an integer for comparison
    //         $noOfInfected = (int) $validated['no_of_infected'];
    //         $locationsCount = count($validated['locations']);

    //         if ($locationsCount !== $noOfInfected) {
    //             throw \Illuminate\Validation\ValidationException::withMessages([
    //                 'locations' => ['The number of locations must match the number of infected trees.'],
    //             ]);
    //         }
    //         $technician_id = auth()->id();
    //         $imagePath = $request->file('image_proof')->store('images', 'public'); // Simplified since required
    //         $reportDetail = TechiniciansReportsModel::create([
    //             'technician_id' => $technician_id,
    //             'farm_id' => $validated['farm_id'],
    //             'farmer_name' => $validated['farmer_name'],
    //             'contact_no' => $validated['contact_info'],
    //             'recipient' => $validated['recipient'],
    //             'farm_location' => $validated['farm_location'],
    //             'district' => $validated['district'],
    //             'farm_size' => $validated['farm_size'],
    //             'coconut_trees' => $validated['coconut_trees'],
    //             'coconut_variety' => $validated['coconut_variety'],
    //             'diagnosis_disease' => $validated['diagnosis_disease'],
    //             'treatment_recommendation' => $validated['treatment_recommendation'],
    //             'image_proof' => $imagePath,
    //             'status' => $validated['status'],
    //             'no_of_infected' => $noOfInfected,
    //             'coordinates' => $validated['locations'],
    //         ]);

    //         $techReport = TechiniciansReportsModel::find(1);
    //         $notification = $techReport->notifications()->create([
    //             'user_id' => $technician_id,
    //             'farm_id' => $techReport->farm_id,
    //             'disease' => $techReport->diagnosis_disease,
    //             'status' => 'unread'
    //         ]);

    //         // Notification::create([
    //         //     'user_id' => $technician_id,
    //         //     'farm_id' => $validated['farm_id'],
    //         //     'disease' => $validated['diagnosis_disease'],
    //         // ]);

    //         // broadcast(new NewFarmerReport($report))->toOthers();

    //         if ($reportDetail) {
    //             $this->updateStatus($validated['farm_id'], $validated['status']);
    //         }

    //         return response()->json(['success' => true, 'message' => 'Report submitted successfully']);
    //     } catch (\Illuminate\Validation\ValidationException $e) {
    //         \Log::error('Validation error: ' . $e->getMessage(), ['errors' => $e->errors()]);
    //         return redirect()->route('user.reports')->with('error', 'Validation failed: ' . implode(', ', $e->errors()['locations'] ?? ['Unknown error']));
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         return redirect()->route('user.reports')->with('error', 'The specified report could not be found.');
    //     } catch (\Exception $e) {
    //         \Log::error('Error submitting report: ' . $e->getMessage(), [
    //             'Request Data' => $request->all(),
    //         ]);
    //         return redirect()->route('user.reports')->with('error', 'An unexpected error occurred. Please try again.');
    //     }
    // }

    public function technicianReport(Request $request)
    {
        try {
            $validated = $request->validate([
                'farm_id' => 'required|integer|exists:farms,id',
                'farmer_name' => 'required|string|max:255',
                'contact_info' => 'required|string|max:255',
                'recipient' => 'required|string|max:255',
                'farm_location' => 'required|string|max:255',
                'district' => 'required|string|max:255',
                'farm_size' => 'required|numeric',
                'coconut_trees' => 'required|numeric',
                'coconut_variety' => 'required|string|max:255',
                'diagnosis_disease' => 'required|string|max:255',
                'treatment_recommendation' => 'required|string',
                'image_proof' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'required|string',
                'no_of_infected' => 'required|integer|min:1',
                'locations' => 'required|array',
                'locations.*' => 'required|string|regex:/^[-]?[0-9]{1,3}\.[0-9]+,[-]?[0-9]{1,3}\.[0-9]+$/',
            ]);

            // Validate locations count matches infected trees
            if (count($validated['locations']) !== (int)$validated['no_of_infected']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'locations' => ['The number of locations must match the number of infected trees.'],
                ]);
            }

            $technician_id = auth()->id();
            
            // Create the technician report
            $report = TechiniciansReportsModel::create([
                'technician_id' => $technician_id,
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
                'image_proof' => $request->file('image_proof')->store('images', 'public'),
                'status' => $validated['status'],
                'no_of_infected' => $validated['no_of_infected'],
                'coordinates' => $validated['locations'],
            ]);

            if (!$report) {
                throw new \Exception('Failed to create technician report');
            }

            // Create notification using the newly created report
            $notification = $report->notifications()->create([
                'user_id' => $technician_id,
                'farm_id' => $report->farm_id,
                'disease' => $report->diagnosis_disease,
                'status' => 'unread'
            ]);

            // Update farm status
            $this->updateStatus($report->farm_id, $report->status);

            // Broadcast event if needed
            // broadcast(new NewTechnicianReport($report))->toOthers();

            return response()->json([
                'success' => true, 
                'message' => 'Report submitted successfully',
                'report_id' => $report->id
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ' . $e->getMessage(), ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Error submitting report: ' . $e->getMessage(), [
                'request' => $request->except(['image_proof']), // Don't log file content
                'user_id' => auth()->id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the report'
            ], 500);
        }
    }

}


