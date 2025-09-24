<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Doctore;
use App\Models\Horario;

class HorarioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Horario::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'horaInicio' => fake()->time(),
            'horaFin' => fake()->time(),
            'estado' => fake()->randomElement(["Activo","Inactivo"]),
            'doctor_id' => Doctore::factory(),
        ];
    }
}
