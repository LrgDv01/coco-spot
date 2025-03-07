<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\TechnicianModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TechnicianModel>
 */
class TechnicianModelFactory extends Factory
{
    protected $model = TechnicianModel::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'technician_id' => User::factory(),
            'years_in_service' => $this->faker->numberBetween(1, 30),
            'services' => $this->faker->randomElement([
                'Plumbing, Electrical Repair',
                'Electrical Repair, Carpentry',
                'Carpentry, HVAC Maintenance',
                'HVAC Maintenance, Painting Service',
                'Painting Service, Landscaping',
                'Landscaping, Roofing',
            ]),
        ];
    }
}
