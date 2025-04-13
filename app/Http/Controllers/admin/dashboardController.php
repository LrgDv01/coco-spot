<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\farmer\FarmModel;
use App\Models\technician\TechiniciansReportsModel;
use App\Models\farmer\FarmerReportsModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class dashboardController extends Controller
{
    public function display_dashboard() {
        return view('admin.dashboard');
    }

    public function get_dashboard_info(Request $request)
    { 
        $count_farmers = User::where('user_type', '=', '1')->count();
        $count_farms = FarmModel::all()->count();
        $count_techicians = User::where('user_type', '=', '2')->count();
        $count_correct_diagnosis = TechiniciansReportsModel::where('status', '=', 'Verified')->count();
        $count_incorrect_diagnosis = TechiniciansReportsModel::where('status', '=', 'False Detection')->count();
        $count_healthy_farms = FarmModel::whereDoesntHave('farmerReports')->count();
        $count_infected_farms = FarmerReportsModel::all()->count();
        $count_infected_farms = $count_infected_farms - ($count_correct_diagnosis + $count_incorrect_diagnosis);
        // $affected_rate = round(($count_infected_farms / $count_farms) * 100, 2);
        $affected_rate = round(($count_infected_farms / $count_farms) * 100);
        $count_treated_farms = TechiniciansReportsModel::all()->count();

        $year = $request->input('year');
        $districtFilter = $request->input('district'); 

        // Filter for Disease
        $reportQuery = TechiniciansReportsModel::whereYear('created_at', $year);
        if ($districtFilter !== 'all') {
            $reportQuery->where('district', $districtFilter);
        }
        $diseaseYearData = $reportQuery->get();
        $disease_type = [
            "bud_rot" => "Lethal Yellowing",
            "leaf_spot" => "Leaf Spot",
            "stem_bleeding" => "Stem Bleeding"
        ];
        $count_disease = [];
        $diseaseDistrict = []; 
        foreach ($diseaseYearData as $key => $item) {
            $month = Carbon::parse($item->created_at)->format('n'); 
            $year = Carbon::parse($item->created_at)->format('Y'); 

            if (!isset($count_disease[$year][$month])) {
                $count_disease[$year][$month] = array_fill_keys(array_keys($disease_type), 0);
                $diseaseDistrict[$year][$month] = $item->district; 
            }
            $reported_coco = $item->diagnosis_disease ?? [];
            foreach ($disease_type as $key => $coco) {
                if ($coco === $reported_coco) {
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
            'count_farmers' => $count_farmers,
            'count_farms' => $count_farms,
            'count_techicians' => $count_techicians,
            'count_correct_diagnosis' => $count_correct_diagnosis,
            'count_incorrect_diagnosis' => $count_incorrect_diagnosis,
            'count_healthy_farms' => $count_healthy_farms,
            'count_infected_farms' => $count_infected_farms,
            'affected_rate' => $affected_rate,
            'count_treated_farms' => $count_treated_farms,
            'count_disease' => $count_disease,
            'count_variety' => $count_variety,
            'varietyDistrict' => $varietyDistrict, 
            'diseaseDistrict' => $diseaseDistrict
        ];
        return response()->json($res);
    }
}
