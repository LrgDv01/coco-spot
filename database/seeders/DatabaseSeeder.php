<?php

namespace Database\Seeders;
use App\Models\Farm\FarmModel;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
      $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            MapModelSeeder::class,
            FarmSeeder::class,
            ReportSeeder::class
        ]);
    }
}
