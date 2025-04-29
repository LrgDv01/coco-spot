<?php

namespace App\Http\Controllers\admin;
use App\Models\admin\MapModel;
use App\Models\farmer\FarmModel;
use App\Models\technician\TechiniciansReportsModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MapController extends Controller
{

    public function get_map_locations() {
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

        // $diseases = TechiniciansReportsModel::with('farms')->pluck('diagnosis_disease')->toArray();
        // $diseases = TechiniciansReportsModel::with('farms')->get();
        $diseases = FarmModel::with('farmerReports', 'technicianReports')->get();

        $res = [
            'locations' => $locations,
            'farm_loc' => $farm_loc,
            'diseases' => $diseases
        ];
        return response()->json($res);
    }
    
}
     