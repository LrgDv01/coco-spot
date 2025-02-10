<?php
namespace App\Http\Controllers\user\technician;

use App\Models\User;
use App\Models\ServicesModel;
use App\Models\technician\ReportsModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Add this line to import the Log facade

class ReportsController extends Controller
{
    public function index()
    {
        $recipient = auth()->user()->full_name;
        $reports = ServicesModel::with(['user', 'userFarms']) 
            ->where([
                ['technician_id', auth()->id()],
                ['status', '=', 'accepted']
            ])
            ->get();
            
        $user = auth()->id();
        $submitted = ReportsModel::where('technician_id', $user)
            ->pluck('farm_id')
            ->toArray();

        $submittedReports = ReportsModel::select('farm_id', 'soil_type', 'disease_type', 'note')
            ->where('technician_id', $user)
            ->get()
            ->mapWithKeys(function ($report) {
                return [
                    $report->farm_id => [
                        'soil_type' => $report->soil_type,
                        'disease_types' => explode(',', $report->disease_type), // Assuming stored as comma-separated values
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

    // public function submitReport(Request $request)
    // {
    //     try {
    //         // Find the report by ID
    //         $report = ServicesModel::findOrFail($request->input('report_id'));

    //         // Handle input values correctly
    //         $soilType = is_array($request->input('soil_type')) 
    //             ? implode(', ', $request->input('soil_type')) 
    //             : ($request->input('soil_type') ?? ''); // Convert array to string if necessary

    //         $diseaseTypes = is_array($request->input('disease_types')) 
    //             ? json_encode($request->input('disease_types')) 
    //             : json_encode([$request->input('disease_types') ?? '']); // Encode as JSON even for single items

    //         $note = is_array($request->input('note')) 
    //             ? implode(' ', $request->input('note')) 
    //             : ($request->input('note') ?? ''); // Convert array to string if necessary

    //         // Debugging: Check the values before saving
    //         Log::info('Processed Inputs:', [
    //             'Soil Type' => $soilType,
    //             'Disease Types' => $diseaseTypes,
    //             'Note' => $note,
    //         ]);

    //         // Create a new ReportDetail record
    //         $reportDetail = ReportsModel::create([
    //             'report_id' => $report->id,
    //             'farmer_name' => $request->input('farmer_name'),
    //             'contact_no' => $request->input('contact_info'),
    //             'recipient' => $request->input('recipient'),
    //             'farm_name' => $request->input('farm_name'),
    //             'farm_location' => $request->input('farm_location'),
    //             'farm_size' => $request->input('farm_size'),
    //             'coconut_trees' => $request->input('coconut_trees'),
    //             'coconut_variety' => $request->input('coconut_variety'),
    //             'soil_type' => $soilType, // Processed soil type
    //             'disease_type' => $diseaseTypes, // Processed disease types as JSON
    //             'note' => $note, // Processed note
    //         ]);

    //         // Save the report detail
    //         $reportDetail->save();

    //         // Redirect or return a response
    //         return redirect()->route('user.reports')->with('success', 'Report submitted successfully');
    //     } catch (\Exception $e) {
    //         // Log error details for debugging
    //         Log::error('Error submitting report: ' . $e->getMessage(), [
    //             'Request Data' => $request->all(),
    //         ]);

    //         // Return an error response
    //         return redirect()->route('user.reports')->with('error', 'An error occurred while submitting the report.');
    //     }
    // }

    public function submitReport(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'farm_id' => 'required|integer',
                'farmer_name' => 'required|string',
                'contact_info' => 'required|string',
                'recipient' => 'required|string',
                'farm_name' => 'required|string',
                'farm_location' => 'required|string',
                'farm_district' => 'required|string',
                'farm_size' => 'required|string', // Includes units like "hectares"
                'coconut_trees' => 'required|numeric', // Flexible for numeric values
                'coconut_variety' => 'required|string',
                'soil_type' => 'required|array',
                'disease_types' => 'required|array',
                'note' => 'required|array',
            ]);
    
            // Process the inputs
            $soilType = implode(', ', array_values($validated['soil_type'])); // Flatten and convert to string
            // $diseaseTypes = [];
            // foreach ($validated['disease_types'] as $types) {
            //     $diseaseTypes = array_merge($diseaseTypes, $types); // Merge nested arrays
            // }
            // $diseaseTypes = json_encode($diseaseTypes); // Encode as 

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

        // Need for ajax request

            //     // Return a JSON response (successful form submission)
            //     return response()->json([
            //         'message' => 'Report submitted successfully',
            //         'redirectUrl' => route('user.reports'), // URL to redirect to
            //     ]);

            // } catch (\Exception $e) {
            //     // Handle exceptions and errors
            //     Log::error('Error submitting report: ' . $e->getMessage(), [
            //         'Request Data' => $request->all(),
            //     ]);

            //     return response()->json([
            //         'error' => 'An unexpected error occurred. Please try again.',
            //     ], 500);
            // }

            // Redirect to reports page with success message
            return redirect()->route(' .reports')->with('success', 'Report submitted successfully');
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


