<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Consultorio;
use App\Models\Doctore;

class ConsultorioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Consultorio::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'codigo' => fake()->word(),
            'ubicacion' => fake()->word(),
            'piso' => fake()->numberBetween(-10000, 10000),
            'doctor_id' => Doctore::factory(),
        ];
    }
}
