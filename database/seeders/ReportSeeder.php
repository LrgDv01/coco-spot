<?php

namespace Database\Seeders;
use App\Models\technician\ReportsModel;
use App\Models\Farm\FarmModel;
use App\Models\TechnicianModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $reports = [
            [
                'technician_id' => 1, 
                'farm_id' => 1, 
                'farmer_name' => 'John Doe',
                'contact_no' => '1234567890',
                'recipient' => 'Jane Doe',
                'farm_name' => 'Green Valley Farm',
                'farm_location' => '123 Farm Road, Countryside',
                'district' => 'III',
                'farm_size' => 10.5,
                'coconut_trees' => 500,
                'coconut_variety' => 'Tall Variety',
                'soil_type' => 'Loamy Soil',
                'disease_type' => ['Bud Rot', 'Leaf Spot'],
                'note' => 'Initial inspection completed.',
            ],
        ];

        foreach ($reports as $report) {
            $technician = TechnicianModel::find($report['technician_id']);
            if (!$technician) {
                $technician = TechnicianModel::create([
                    'id' => $report['technician_id'], 
                    'years_in_service' => 5, 
                    'services' => 'General Inspection'
                ]);
            }

            $farm = FarmModel::find($report['farm_id']);
            if (!$farm) {
                $farm = FarmModel::create([
                    'id' => $report['farm_id'], 
                    'farmer_id' => 1, 
                    'name' => $report['farm_name'], 
                    'address' => $report['farm_location'], 
                    'district' => $report['district'],
                    'variety' => $report['coconut_variety'],
                    'hectares' => $report['farm_size'],
                    'tree_age' => 5,
                    'planted_coconut' => $report['coconut_trees'],
                    'condition' => 'Healthy',
                ]);
            }

            ReportsModel::create([
                'technician_id' => $technician->id,
                'farm_id' => $farm->id,
                'farmer_name' => $report['farmer_name'],
                'contact_no' => $report['contact_no'],
                'recipient' => $report['recipient'],
                'farm_name' => $report['farm_name'],
                'farm_location' => $report['farm_location'],
                'district' => $report['district'],
                'farm_size' => $report['farm_size'],
                'coconut_trees' => $report['coconut_trees'],
                'coconut_variety' => $report['coconut_variety'],
                'soil_type' => $report['soil_type'],
                'disease_type' => json_encode($report['disease_type']),
                'note' => $report['note'],
            ]);
        }
    }
}
