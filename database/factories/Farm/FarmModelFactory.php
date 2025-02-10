<?php

namespace Database\Factories\Farm;
use App\Models\Farm\FarmModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FarmModel>
 */
class FarmModelFactory extends Factory
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
            'location' => fake()->address(),
            'district' => fake()->randomElement(['III', 'IV']),
            'variety' => fake()->randomElement(['Laguna Tall', 'Dwarf Coconut', 'Hybrid']),
            'hectares' => fake()->numberBetween(1, 100), 
            'tree_age' => fake()->numberBetween(1, 50), 
            'planted_coconut' => fake()->numberBetween(10, 1000), 
            'condition' => fake()->randomElement(['is Healthy', 'is Infected']),
        ];
    }

}
