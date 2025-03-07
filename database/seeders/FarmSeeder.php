<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\farmer\FarmModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FarmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $farm_coordinates = [
            [
                'farmer_id' => 1,
                'name' => 'farm1', 
                'latitude' => 14.289890827221688, 
                'longitude' => 121.49150968052746, 
                'address' => "Caliraya, Lumban, Laguna",
                'municipal' => "Lumban",
                'district' => "III",
                'variety' => "Laguna Tall",
                'hectares' => 234.34,
                'tree_age' => 3,
                'planted_coconut' => 234,
            ],
            [
                'farmer_id' => 2,
                'name' => 'farm2', 
                'latitude' => 14.153470465988198, 
                'longitude' => 121.34967013453964, 
                'address' => "Lamot 1 Laguna",
                'municipal' => "Calauan",
                'district' => "IV",
                'variety' => "Dwarf Coconut",
                'hectares' => 234.34,
                'tree_age' => 3,
                'planted_coconut' => 234,
            ],
            [
                'farmer_id' => 3,
                'name' => "farm3", 
                'latitude' => 14.39072170217407, 
                'longitude' => 121.51243066404795, 
                'address' => "9GR6+7XM Pakil, Laguna",
                'municipal' => "Pakil",
                'district' => "III",
                'variety' => "Hybrid",
                'hectares' => 234.34,
                'tree_age' => 3,
                'planted_coconut' => 234,
            ],
        ];

        foreach ($farm_coordinates as $coordinates) {
            FarmModel::create([
                'farmer_id' => $coordinates['farmer_id'],
                'name' => $coordinates['name'],
                'latitude' => $coordinates['latitude'],
                'longitude' => $coordinates['longitude'],
                'address' => $coordinates['address'],
                'municipal' => $coordinates['municipal'],
                'district' => $coordinates['district'],
                'variety' => $coordinates['variety'],
                'hectares' => $coordinates['hectares'],
                'tree_age' => $coordinates['tree_age'],
                'planted_coconut' => $coordinates['planted_coconut'],
            ]);
        }

        // User::where('user_type', 1)->each(function ($user) {
        //     FarmModel::factory(3)->create(['farmer_id' => $user->id]); 
        // });
    }
}
