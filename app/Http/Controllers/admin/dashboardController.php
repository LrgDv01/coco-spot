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

        // $count_total_cost = PCA::all()->count();
        $count_total_cost = PCA::sum('total_cost');
         // count total farmers
        $count_farmers = User::where('user_type', '=', '1')->count();
         // count total farms
        $count_farms = FarmModel::all()->count();
        $count_doa = DOA::all()->count();

        
        $year = $request->input('year');

        $coconut_variety = [
            "laguna_tall" => "Laguna Tall",
            "dwarf_coconut" => "Dwarf Coconut", 
            "hybrid" => "Hybrid"
        ];
        $count_variety = [];
        foreach ($coconut_variety as $key => $variety) {
            $count_variety[$key] = ReportsModel::where('coconut_variety', $variety)
                ->count();
        }
       
        $disease_types = [
            "coconut_wilt" => "Coconut Wilt",
            "bud_rot" => "Bud Rot",
            "leaf_spot" => "Leaf Spot",
            "stem_bleeding" => "Stem Bleeding"
        ];
    
        $yearData = ReportsModel::whereYear('created_at', $year)->get();
        $count_disease = [];
    
        foreach ($yearData as $item) {
            $month = Carbon::parse($item->created_at)->format('n'); // Numeric month (1-12)
            $year = Carbon::parse($item->created_at)->format('Y'); // Year
    
            if (!isset($count_disease[$year][$month])) {
                $count_disease[$year][$month] = array_fill_keys(array_keys($disease_types), 0);
            }
    
            $reported_diseases = json_decode($item->disease_type, true) ?? [];
    
            foreach ($disease_types as $key => $disease) {
                if (in_array($disease, $reported_diseases)) {
                    $count_disease[$year][$month][$key]++;
                }
            }
        }

        $res = [
            'count_total_cost' => $count_total_cost,
            'count_farmers' => $count_farmers,
            'count_farms' => $count_farms,
            'count_doa' => $count_doa,
            'count_variety' => $count_variety,
            'count_disease' => $count_disease        
        ];
        return response()->json($res);
    }
}
