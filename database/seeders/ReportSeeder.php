<?php

namespace Database\Seeders;

use App\Models\farmer\FarmerReportsModel;
use App\Models\technician\TechiniciansReportsModel;
use App\Models\User;
use App\Models\farmer\FarmModel;
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
        $technician_reports = [
            [
                'technician_id' => 1, 
                'farm_id' => 1, 
                'farmer_name' => 'John Doe',
                'contact_no' => '1234567890',
                'recipient' => 'Jane Doe',
                'farm_location' => '123 Farm Road, Countryside',
                'district' => 'III',
                'farm_size' => 10.5,
                'coconut_trees' => 500,
                'coconut_variety' => 'Tall Variety',
                'diagnosis_disease' => 'Leaf Spot',
                'treatment_recommendation' => 'Initial inspection completed.',
                'image_proof' => 'images/example-disease.jpg',
                'status' => 'Verified',
            ],
        ];

        foreach ($technician_reports as $report) {

            TechiniciansReportsModel::create([
                'technician_id' => $report['technician_id'],
                'farm_id' => $report['farm_id'],
                'farmer_name' => $report['farmer_name'],
                'contact_no' => $report['contact_no'],
                'recipient' => $report['recipient'],
                'farm_location' => $report['farm_location'],
                'district' => 'IV',
                'farm_size' => $report['farm_size'],
                'coconut_trees' => $report['coconut_trees'],
                'coconut_variety' => $report['coconut_variety'],
                'diagnosis_disease' => $report['diagnosis_disease'],
                'treatment_recommendation' => $report['treatment_recommendation'],
                'image_proof' => $report['image_proof'],
                'status' => $report['status'],
            ]);
        }

        $farmer_reports = [
            [
                'farmer_id' => 1, 
                'farm_id' => 1, 
                'technician_id' => 1,
                'farmer_name' => 'John Doe',
                'contact_no' => '1234567890',
                'farm_location' => '123 Farm Road, Countryside',
                'district' => 'III',
                'farm_size' => 10.5,
                'coconut_trees' => 500,
                'coconut_variety' => 'Tall Variety',
                'symptoms' => ['Leaves turning yellow', 'Leaves are drying up',
                     'Small brown, black or yellow spots on leaves'],
                'disease' => 'Lethal Yellowing',
                'image_path' => 'images/example-disease.jpg',
                'status' => 'unread',
            ]
        ];

        foreach ($farmer_reports as $report) {
            // $farmer_id = User::find($report['farmer_id']);
            FarmerReportsModel::create([
                'farmer_id' => $report['farmer_id'],
                'farm_id' => $report['farm_id'],
                'technician_id' => $report['technician_id'],
                'farmer_name' => $report['farmer_name'],
                'contact_no' => $report['contact_no'],
                'farm_location' => $report['farm_location'],
                'farm_size' => $report['farm_size'],
                'coconut_trees' => $report['coconut_trees'],
                'coconut_variety' => $report['coconut_variety'],
                'symptoms' => json_encode($report['symptoms']),
                'disease' =>  $report['disease'],
                'image_path' => $report['image_path'],
                'status' => $report['status']
            ]);
        }
    }
}
