<?php

namespace Database\Seeders;
use App\Models\admin\MapModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MapModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    //
        $mapLocation = [
            // District 3 > Municipality 
            [ 'name' => 'San Pablo',
              'coordinates' => [121.444, 14.419],
              // 'coordinates' => [   121.32470843088436,
              // 14.069835537119275],
              'color' => 'red',
              'lot_area' => 24817.52,
              'trees' => 1604310,
              'meters' => 8,],

            [ 'name' => 'Alaminos',
              'coordinates' => [121.409, 14.281],
              // 'coordinates' => [121.24771143310534,
              // 14.061841802756987],
              'color' => 'yellow',
              'lot_area' => 5591.70,
              'trees' => 286862,
              'meters' => 8,],

            [ 'name' => 'Calauan',
              'coordinates' => [121.409, 14.281],
              // 'coordinates' => [121.31442663145629,
              // 14.14568932389669],
              'color' => 'yellow',
              'lot_area' => 6749.77,
              'trees' => 284582,
              'meters' => 8,],

            [ 'name' => 'Liliw',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 3,755.40,
              'trees' => 336905,
              'meters' => 8,],

            [ 'name' => 'Nagcarlan',
              'coordinates' => [121.328, 14.068],
              'color' => 'blue',
              'lot_area' => 7178.04,
              'trees' => 890600,
              'meters' => 8,],

            [ 'name' => 'Rizal',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 2781.56,
              'trees' => 291800,
              'meters' => 8,],

            [ 'name' => 'Victoria',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 2235.00,
              'trees' => 21159,
              'meters' => 8,],

            // District 4 > Municipality 
              [ 'name' => 'Cavinti',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 25743.43,
              'trees' => 512880,
              'meters' => 8,],

              [ 'name' => 'Famy',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 5375.30,
              'trees' => 243112,
              'meters' => 8,],

              [ 'name' => 'Kalayaan',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 5316.00,
              'trees' => 76444,
              'meters' => 8,],

              [ 'name' => 'Luisiana',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 7079.83,
              'trees' => 399890,
              'meters' => 8,],

              [ 'name' => 'Lumban',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 9424.63,
              'trees' => 77964,
              'meters' => 8,],

              [ 'name' => 'Mabitac',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 5526.00,
              'trees' => 102197,
              'meters' => 8,],

              [ 'name' => 'Magdalena',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 3609.50,
              'trees' => 234635,
              'meters' => 8,],

              [ 'name' => 'Majayjay',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 7245.57,
              'trees' => 388179,
              'meters' => 8,],

              [ 'name' => 'Paete',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 43534,
              'trees' => 5798.80,
              'meters' => '8',],

              [ 'name' => 'Pagsanjan',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 4484.35,
              'trees' => 231454,
              'meters' => 8,],

              [ 'name' => 'Pakil',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 4272.20,
              'trees' => 69233,
              'meters' => 8,],

              [ 'name' => 'Pangil',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 5168.38,
              'trees' => 35577,
              'meters' => 8,],

              [ 'name' => 'Pila',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 2638.35,
              'trees' => 67187,
              'meters' => 8,],

              [ 'name' => 'Santa Cruz',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 3479.20,
              'trees' => 138,758,
              'meters' => 8,],

              [ 'name' => 'Santa Maria',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 7365.00,
              'trees' => 339710,
              'meters' => 8,],

              [ 'name' => 'Siniloan',
              'coordinates' => [121.409, 14.281],
              'color' => 'yellow',
              'lot_area' => 4472.27,
              'trees' => 276351,
              'meters' => 8,],
        ];

        foreach ($mapLocation as $location) {
            MapModel::create([
                'name' => $location['name'],
                'coordinates' => json_encode($location['coordinates']), // Convert to JSON
                'color' => $location['color'],
                'lot_area' => $location['lot_area'],
                'trees' => $location['trees'],
                'meters' => $location['meters'],
            ]);
        }
    }
}
