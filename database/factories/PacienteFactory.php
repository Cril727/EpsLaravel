<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Paciente;
use App\Models\Role;

class PacienteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Paciente::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nombres' => fake()->word(),
            'apellidos' => fake()->word(),
            'email' => fake()->safeEmail(),
            'telefono' => fake()->word(),
            'password' => fake()->password(),
            'rol_id' => Role::factory(),
        ];
    }
}
