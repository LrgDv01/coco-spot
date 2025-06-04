<?php

namespace App\Http\Controllers\admin;
use App\Models\admin\MapModel;
use App\Models\farmer\FarmModel;
use App\Models\technician\TechiniciansReportsModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MapController extends Controller
{

    public function get_map_locations() {
        $user = Auth::user();
        $farmerID = auth()->user()->id; 
        $map_loc = MapModel::all();
        $locations = [];
        foreach ($map_loc as $location) {
            $coordinates = json_decode($location->coordinates, true);
            $locations[] = [
                'name' => $location->name,
                'coordinates' => $coordinates, 
                'color' => $location->color,
                'lot_area' => $location->lot_area,
                'trees' => $location->trees,
                'meters' => $location->meters,
            ];
        }
        $farm_loc = FarmModel::all();
        if ($user->isAdmin() || $user->isTechnician()) {
            $diseases = FarmModel::with('farmerReports', 'technicianReports')->get();
        } else {
            $diseases = FarmModel::with('farmerReports', 'technicianReports')
                ->where('farmer_id', $farmerID)
                ->get();
        }
        $res = [
            'locations' => $locations,
            'farm_loc' => $farm_loc,
            'diseases' => $diseases
        ];
        return response()->json($res);
    }
    
}
     