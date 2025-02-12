<?php
namespace App\Http\Controllers\user\technician;

use App\Models\User;
use App\Models\ServicesModel;
use App\Models\technician\ReportsModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; 
use App\Http\Controllers\ServicesController;
use App\Models\Farm\FarmModel;

class ReportsController extends Controller
{
    public function index()
    {
        $technicianId = auth()->id();
       
        $recipient = auth()->user()->full_name;
        $reports = ServicesModel::with(['user', 'userFarms']) 
            ->where([['technician_id', $technicianId],])
            ->get();

        $farmID = FarmModel::with('services') 
            ->where([['condition', 'is Infected']])
            // ->where([['services.technician_id', $technicianId]])
            ->get()
            ->pluck('id')
            ->toArray();
        
        // dd($farmID);

        // $reportExists = ReportsModel::where([['technician_id', $technicianId], ['farm_id', $farm->id]])->exists();
        // if ($reportExists) {
        //     ServicesModel::where([['technician_id', $technicianId],['status', '!=', 'end']])
        //         ->update(['status' => 'end']);
        // }
   
    
     
        $submitted = ReportsModel::where('technician_id', $technicianId)
            ->pluck('farm_id')
            ->toArray();

        $submittedReports = ReportsModel::select('farm_id', 'soil_type', 'disease_type', 'note')
            ->where('technician_id', $technicianId)
            ->get()
            ->mapWithKeys(function ($report) {
                return [
                    $report->farm_id => [
                        'soil_type' => $report->soil_type,
                        'disease_types' => explode(',', $report->disease_type), 
                        'note' => $report->note,
                    ],
                ];
            });
        
        return view('user.technician.pages.reports',
            compact(
                'reports',
                'recipient',
                'submitted',
                'submittedReports'
        ));
    }

    public function submitReport(Request $request, ServicesController $servicesController)
    {
        try {
            $validated = $request->validate([
                'farm_id' => 'required|integer',
                'farmer_name' => 'required|string',
                'contact_info' => 'required|string',
                'recipient' => 'required|string',
                'farm_name' => 'required|string',
                'farm_location' => 'required|string',
                'farm_district' => 'required|string',
                'farm_size' => 'required|string', 
                'coconut_trees' => 'required|numeric', 
                'coconut_variety' => 'required|string',
                'soil_type' => 'required|array',
                'disease_types' => 'required|array',
                'note' => 'required|array',
            ]);
    
            $soilType = implode(', ', array_values($validated['soil_type'])); 
            $diseaseTypes = array_merge(...$validated['disease_types']); 
            $diseaseTypes = json_encode($diseaseTypes); 

            $note = implode(' ', array_values($validated['note'])); 
            $reportDetail = ReportsModel::create([
                'technician_id' => auth()->id(),
                'farm_id' => $validated['farm_id'],
                'farmer_name' => $validated['farmer_name'],
                'contact_no' => $validated['contact_info'],
                'recipient' => $validated['recipient'],
                'farm_name' => $validated['farm_name'],
                'farm_location' => $validated['farm_location'],
                'district' => $validated['farm_district'],
                'farm_size' => $validated['farm_size'],
                'coconut_trees' => $validated['coconut_trees'],
                'coconut_variety' => $validated['coconut_variety'],
                'soil_type' => $soilType,
                'disease_type' => $diseaseTypes,
                'note' => $note,
            ]);

            return redirect()->route('user.reports')->with('success', 'Report submitted successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('user.reports')->with('error', 'The specified report could not be found.');
        } catch (\Exception $e) {
            Log::error('Error submitting report: ' . $e->getMessage(), [
                'Request Data' => $request->all(),
            ]);
            return redirect()->route('user.reports')->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

}


