<?php

namespace Database\Factories\farmer;
use App\Models\User;
use App\Models\farmer\FarmModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\farmer\FarmModel>
 */
class FarmFactory extends Factory
{
    protected $model = FarmModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'farmer_id' => User::factory(),
            'name' => fake()->company(), 
            'latitude' => fake()->latitude(-90, 90), 
            'longitude' => fake()->longitude(-180, 180), 
            'address' => fake()->address(),
            'municipal' => fake()->randomElement([
                'Alaminos', 'Calauan', 'Cavinti', 'Famy', 'Kalayaan', 'Liliw', 
                'Luisiana', 'Lumban', 'Mabitac', 'Magdalena', 'Majayjay', 
                'Nagcarlan', 'Paete', 'Pagsanjan', 'Pakil', 'Pangil', 'Pila', 
                'Rizal', 'San Pablo', 'Santa Cruz', 'Santa Maria', 'Siniloan', 
                'Victoria'
            ]),
            'district' => fake()->randomElement(['III', 'IV']),
            'variety' => fake()->randomElement(['Laguna Tall', 'Dwarf Coconut', 'Hybrid']),
            'hectares' => fake()->numberBetween(1, 100),
            'tree_age' => fake()->numberBetween(1, 50),
            'planted_coconut' => fake()->numberBetween(10, 1000),
            // 'condition' => fake()->randomElement(['is Healthy', 'is Infected']),
        ];
    }
}
