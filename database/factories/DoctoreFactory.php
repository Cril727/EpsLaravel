<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Doctore;
use App\Models\Especialidade;
use App\Models\Role;

class DoctoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Doctore::class;

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
            'estado' => fake()->randomElement(["Activo","Inactivo"]),
            'password' => fake()->password(),
            'rol_id' => Role::factory(),
            'especialidad_id' => Especialidade::factory(),
        ];
    }
}
