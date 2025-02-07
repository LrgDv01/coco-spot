<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Farm\FarmModel;
use App\Models\admin\PCA;
use App\Models\technician\ReportsModel;
use App\Models\admin\DOA;
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

        $count_total_cost = PCA::sum('total_cost');
        $count_farmers = User::where('user_type', '=', '1')->count();
        $count_farms = FarmModel::all()->count();
        $count_doa = DOA::all()->count();

        $year = $request->input('year');
        $yearData = ReportsModel::whereYear('created_at', $year)->get();

        $disease_type = [
            "coconut_wilt" => "Coconut Wilt",
            "bud_rot" => "Bud Rot",
            "leaf_spot" => "Leaf Spot",
            "stem_bleeding" => "Stem Bleeding"
        ];
        $count_disease = [];

        $coconut_variety = [
            "laguna_tall" => "Laguna Tall",
            "dwarf_coconut" => "Dwarf Coconut", 
            "hybrid" => "Hybrid"
        ];
        $count_variety = [];

        foreach ($yearData as $item) {
            $month = Carbon::parse($item->created_at)->format('n'); 
            $year = Carbon::parse($item->created_at)->format('Y'); 

            if (!isset($count_disease[$year][$month])) {
                $count_disease[$year][$month] = array_fill_keys(array_keys($disease_type), 0);
            }
            $reported_diseases = json_decode($item->disease_type, true) ?? [];
            foreach ($disease_type as $key => $disease) {
                if (in_array($disease, $reported_diseases)) {
                    $count_disease[$year][$month][$key]++;
                }
            }

            if (!isset($count_variety[$year][$month])) {
                $count_variety[$year][$month] = array_fill_keys(array_keys($coconut_variety), 0);
            }
            $reported_coco = $item->coconut_variety ?? [];
            foreach ($coconut_variety as $key => $coco) {
                if ($coco === $reported_coco) {
                    $count_variety[$year][$month][$key]++;
                }
            }
        }

        $res = [
            'count_total_cost' => $count_total_cost,
            'count_farmers' => $count_farmers,
            'count_farms' => $count_farms,
            'count_doa' => $count_doa,
            'count_disease' => $count_disease,
            'count_variety' => $count_variety       
        ];
        return response()->json($res);
    }
}
