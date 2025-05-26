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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ReportsController extends Controller
{
    public function index()
    {
        $technicianId = auth()->id();
        $recipient = auth()->user()->full_name;
        $report = ServicesModel::with(['user', 'userFarms', 'farmerReports']) 
            ->where(function ($query) use ($technicianId) {
                $query->where('technician_id', $technicianId)
                    ->where('status', 'Acknowledged');
            })->first();
        // dd($report);
        return view('user.technician.pages.reports',
            compact(
                'report',
                'recipient',
        ));
    }

    public function diseaseReports()
    {
        // $technicianId = auth()->id();
        // $recipient = auth()->user()->full_name;
        $reports = ServicesModel::with(['user', 'technicians', 'farmerReports', 'technicianReports'])
            // ->where('technician_id', $technicianId)
            ->get();
        // dd($reports);
        return view('admin.pages.reports',
            compact(
                'reports',
                // 'recipient',
        ));
    }

    private function updateStatus($farm_id, $status)
    {
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
                // 'treatment_recommendation' => 'required|string',
                'image_proof' => 'required|image|mimes:jpeg,png,jpg,gif,svg|min:0', 
                'status' => 'required|string',
                'no_of_infected' => 'required|integer|min:1',
                'locations' => 'required|array',
                'locations.*' => 'required|string|regex:/^[-]?[0-9]{1,3}\.[0-9]+,[-]?[0-9]{1,3}\.[0-9]+$/',
            ]);

            if (count($validated['locations']) !== (int)$validated['no_of_infected']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'locations' => ['The number of locations must match the number of infected trees.'],
                ]);
            }
            $technician_id = auth()->id();
            $imagePath = $request->file('image_proof')->store('images', 'public');
            $filename = basename($imagePath);
            $storagePath = storage_path('app/public/images/' . $filename);
            $publicPath = public_path('storage/images/' . $filename);
            // Ensure the directory exists
            if (!File::exists(dirname($publicPath))) {
                File::makeDirectory(dirname($publicPath), 0755, true);
            }
            File::copy($storagePath, $publicPath);
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
                // 'treatment_recommendation' => $validated['treatment_recommendation'],
                'image_proof' => $imagePath,
                'status' => $validated['status'],
                'no_of_infected' => $validated['no_of_infected'],
                'coordinates' => $validated['locations'],
            ]);
            if (!$report) {
                // Clean up the uploaded files if report creation fails
                Storage::disk('public')->delete($imagePath);
                if (File::exists($publicPath)) {
                    File::delete($publicPath);
                }
                
                throw new \Exception('Failed to create technician report');
            }
            // Create notification
            $notification = $report->notifications()->create([
                'user_id' => $technician_id,
                'farm_id' => $report->farm_id,
                'disease' => $report->diagnosis_disease,
                'status' => 'unread'
            ]);
            // Update farm status
            $this->updateStatus($report->farm_id, $report->status);
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
                'request' => $request->except(['image_proof']),
                'user_id' => auth()->id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the report'
            ], 500);
        }
    }

    public function monitorList() 
    {
        $technicianId = auth()->id();
        $recipient = auth()->user()->full_name;
        $reports = TechiniciansReportsModel::with('farms')->where('technician_id', $technicianId)->get();
        return view('user.technician.pages.monitor',
            compact(
                'reports',
                'recipient',
        )); 
    }


    
    public function viewDetails($id)
    {
        // $notification = ServicesModel::findOrFail($id);
        // $notification->update(['status' => 'Viewed']); 
        $reportDetails = TechiniciansReportsModel::with('farms')->findOrFail($id);
        return view('user.technician.others.monitor_details', compact('reportDetails'));
    }

}


