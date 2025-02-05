<?php

namespace Database\Seeders;
use App\Models\technician\ReportsModel;
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
                'technician_id' => 1, // Example technician ID
                'farm_id' => 101, // Example farm ID
                'farmer_name' => 'John Doe', // Example farmer name
                'contact_no' => '1234567890', // Example contact number
                'recipient' => 'Jane Doe', // Example recipient
                'farm_name' => 'Green Valley Farm', // Example farm name
                'farm_location' => '123 Farm Road, Countryside', // Example farm location
                'farm_size' => 10.5, // Example farm size in hectares
                'coconut_trees' => 500, // Example number of coconut trees
                'coconut_variety' => 'Tall Variety', // Example coconut variety
                'soil_type' => 'Loamy Soil', // Example soil type
                'disease_type' => 'Bud Rot', // Example disease type
                'note' => 'Initial inspection completed.', // Example note
            ],
            [
                'technician_id' => 2,
                'farm_id' => 102,
                'farmer_name' => 'Alice Smith',
                'contact_no' => '9876543210',
                'recipient' => 'Bob Johnson',
                'farm_name' => 'Sunny Acres Farm',
                'farm_location' => '456 Orchard Lane, Countryside',
                'farm_size' => 15.0,
                'coconut_trees' => 700,
                'coconut_variety' => 'Dwarf Variety',
                'soil_type' => 'Sandy Soil',
                'disease_type' => 'Leaf Spot',
                'note' => 'Follow-up required in 2 weeks.',
            ],
            [
                'technician_id' => 3,
                'farm_id' => 103,
                'farmer_name' => 'Rose Gomez',
                'contact_no' => '9876543210',
                'recipient' => 'Bob Johnson',
                'farm_name' => 'Sunny Acres Farm',
                'farm_location' => '456 Orchard Lane, Countryside',
                'farm_size' => 15.0,
                'coconut_trees' => 700,
                'coconut_variety' => 'Dwarf Variety',
                'soil_type' => 'Sandy Soil',
                'disease_type' => 'Leaf Spot',
                'note' => 'Follow-up required in 2 weeks.',
            ],
            

        ];

        foreach ($reports as $report) {
            ReportsModel::create([
                'technician_id' => $report['technician_id'],
                'farm_id' => $report['farm_id'],
                'farmer_name' => $report['farmer_name'],
                'contact_no' => $report['contact_no'],
                'recipient' => $report['recipient'],
                'farm_name' => $report['farm_name'],
                'farm_location' => $report['farm_location'],
                'farm_size' => $report['farm_size'],
                'coconut_trees' => $report['coconut_trees'],
                'coconut_variety' => $report['coconut_variety'],
                'soil_type' => $report['soil_type'],
                'disease_type' => $report['disease_type'],
                'note' => $report['note'],
            ]);
        }
    }
}
