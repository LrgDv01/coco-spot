<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\farmer\FarmModel;
use App\Models\admin\PCA;
use App\Models\technician\TechiniciansReportsModel;
use App\Models\admin\DOA;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class dashboardController extends Controller
{
    public function display_dashboard() {
        return view('admin.dashboard');
    }

    // private function varietyYearFilter($varietyYearData, $coconut_variety, $count_variety, $district) {

    //     foreach ($varietyYearData as $key => $item) {
    //         $month = Carbon::parse($item->created_at)->format('n'); 
    //         $year = Carbon::parse($item->created_at)->format('Y'); 

    //         if (!isset($count_variety[$year][$month])) {
    //             $count_variety[$year][$month] = array_fill_keys(array_keys($coconut_variety), 0);
    //             $district[$year][$month] = $item->district; // ✅ Add district info
    //         }
    //         $reported_coco = $item->variety ?? [];

    //         foreach ($coconut_variety as $key => $coco) {
    //             if ($coco === $reported_coco) {
    //                 $count_variety[$year][$month][$key]++;
    //             }
    //         }
    //     }
    //     return [$varietyYearData, $coconut_variety, $count_variety, $district];
    // }

    public function get_dashboard_info(Request $request)
    { 

        // $count_total_cost = PCA::sum('total_cost');
        $count_techicians = User::where('user_type', '=', '2')->count();
        $count_farmers = User::where('user_type', '=', '1')->count();
        $count_farms = FarmModel::all()->count();
        $count_doa = DOA::all()->count();

        $year = $request->input('year');
        $districtFilter = $request->input('district'); 

        // Filter for Disease
        // $diseaseYearData = ReportsModel::whereYear('created_at', $year)->get();
        $reportQuery = TechiniciansReportsModel::whereYear('created_at', $year);
        if ($districtFilter !== 'all') {
            $reportQuery->where('district', $districtFilter);
        }
        $diseaseYearData = $reportQuery->get();
        $disease_type = [
            "bud_rot" => "Bud Rot",
            "leaf_spot" => "Leaf Spot",
            "stem_bleeding" => "Stem Bleeding"
        ];

        $count_disease = [];
        $diseaseDistrict = [];
        foreach ($diseaseYearData as $item) {
            $month = Carbon::parse($item->created_at)->format('n'); 
            $year = Carbon::parse($item->created_at)->format('Y'); 
            if (!isset($count_disease[$year][$month])) {
                $count_disease[$year][$month] = array_fill_keys(array_keys($disease_type), 0);
                $diseaseDistrict[$year][$month] = $item->district; // ✅ Add district info
            }
            $reported_diseases = json_decode($item->disease_type, true) ?? [];
            foreach ($disease_type as $key => $disease) {
                if (in_array($disease, $reported_diseases)) {
                    $count_disease[$year][$month][$key]++;
                }
            }
        }

        // Filter for Coconut Variety
        $farmQuery = FarmModel::whereYear('created_at', $year);
        if ($districtFilter !== 'all') {
            $farmQuery->where('district', $districtFilter);
        }

        $varietyYearData = $farmQuery->get();
        $coconut_variety = [
            "laguna_tall" => "Laguna Tall",
            "dwarf_coconut" => "Dwarf Coconut", 
            "hybrid" => "Hybrid"
        ];
        $count_variety = [];
        $varietyDistrict = []; 
        foreach ($varietyYearData as $key => $item) {
            $month = Carbon::parse($item->created_at)->format('n'); 
            $year = Carbon::parse($item->created_at)->format('Y'); 

            if (!isset($count_variety[$year][$month])) {
                $count_variety[$year][$month] = array_fill_keys(array_keys($coconut_variety), 0);
                $varietyDistrict[$year][$month] = $item->district; 
            }
            $reported_coco = $item->variety ?? [];
            foreach ($coconut_variety as $key => $coco) {
                if ($coco === $reported_coco) {
                    $count_variety[$year][$month][$key]++;
                }
            }
        }

        $res = [
            'count_techicians' => $count_techicians,
            'count_farmers' => $count_farmers,
            'count_farms' => $count_farms,
            'count_doa' => $count_doa,
            'count_disease' => $count_disease,
            'count_variety' => $count_variety,
            'varietyDistrict' => $varietyDistrict, 
            'diseaseDistrict' => $diseaseDistrict
        ];
        return response()->json($res);
    }
}
