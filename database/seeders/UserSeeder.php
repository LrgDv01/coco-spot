<?php

namespace Database\Seeders;
use App\Models\User;
// use App\Models\TechnicianModel;
use App\Models\Library\PdlModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(3)->create();
        // User::where('user_type', 2)->each(function ($user) {
        //     TechnicianModel::factory()->create(['technician_id' => $user->id]);
        // });
        $technicians = [
            [
                'profile_img' => null, 
                'user_name' => 'Lovely',
                'full_name' => 'Lovely Labor',
                'address' => $this->faker->address(),
                'municipal' => implode(', ', ['Nagcarlan', 'Rizal', 'Liliw']), // Convert array to comma-separated string
                'contact' => '+639985619672',
                'email' =>  $this->faker->unique()->safeEmail(), 
                'password' => Hash::make('1234'),
                'user_type' => 2,
                'remember_token' => Str::random(10), 
            ],
            [
                'profile_img' => null, 
                'user_name' => 'Riza',
                'full_name' => 'Riza Manalo',
                'address' => $this->faker->address(), 
                'municipal' => implode(', ', ['San Pablo', 'Alaminos']),
                'contact' => '+639506165128',
                'email' =>  $this->faker->unique()->safeEmail(), 
                'password' => Hash::make('1234'),
                'user_type' => 2,
                'remember_token' => Str::random(10), 
            ],
            [
                'profile_img' => null, 
                'user_name' => 'Nora',
                'full_name' => 'Nora Acueza',
                'address' => $this->faker->address(),
                'municipal' => implode(', ', ['Siniloan', 'Pangil', 'Pakil', 'Santa Maria', 'Mabitac']),
                'contact' => '+639205578368', 
                'email' =>  $this->faker->unique()->safeEmail(), 
                'password' => Hash::make('1234'),
                'user_type' => 2,
                'remember_token' => Str::random(10), 
            ],
            [
                'profile_img' => null, 
                'user_name' => 'Charlon',
                'full_name' => 'Charlon Pabon',
                'address' => $this->faker->address(),
                'municipal' => implode(', ', ['Luisiana', 'Cavinti', 'Pagsanjan']),
                'contact' => '+639481279274',
                'email' =>  $this->faker->unique()->safeEmail(), 
                'password' => Hash::make('1234'),
                'user_type' => 2,
                'remember_token' => Str::random(10), 
            ],
            [
                'profile_img' => null, 
                'user_name' => 'Rizaldy',
                'full_name' => 'Rizaldy Acuno',
                'address' => $this->faker->address(),
                'municipal' => implode(', ', ['Kalayaan', 'Famy', 'Lumban', 'Paete']),
                'contact' => '+639082915059',
                'email' =>  $this->faker->unique()->safeEmail(), 
                'password' => Hash::make('1234'),
                'user_type' => 2,
                'remember_token' => Str::random(10), 
            ],
            [
                'profile_img' => null, 
                'user_name' => 'Ruel',
                'full_name' => 'Ruel de Villa',
                'address' => $this->faker->address(),
                'municipal' => implode(', ', ['Magdalena', 'Majayjay', 'Santa Cruz']),
                'contact' => '+639618138276',
                'email' =>  $this->faker->unique()->safeEmail(), 
                'password' => Hash::make('1234'),
                'user_type' => 2,
                'remember_token' => Str::random(10), 
            ],
            [
                'profile_img' => null, 
                'user_name' => 'Rose Ann',
                'full_name' => 'Rose Ann Alejandro',
                'address' => $this->faker->address(),
                'municipal' => implode(', ', ['Calauan', 'Victoria', 'Pila']),
                'contact' => '+639092522509',
                'email' =>  $this->faker->unique()->safeEmail(), 
                'password' => Hash::make('1234'),
                'user_type' => 2,
                'remember_token' => Str::random(10), 
            ],
        ];

        foreach ($technicians as $technician) {
            User::create([
            'profile_img' => $technician['profile_img'],
            'user_name' => $technician['user_name'],
            'full_name' => $technician['full_name'],
            'address' => $technician['address'],
            'municipal' => $technician['municipal'],
            'contact' => $technician['contact'],
            'email' => $technician['email'],
            'password' => $technician['password'],
            'user_type' => $technician['user_type'],
            'remember_token' => $technician['remember_token'],
            ]);
        }
    }
}

