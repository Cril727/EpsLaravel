<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\CitasMedica;
use App\Models\Doctore;
use App\Models\Paciente;

class CitasMedicaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CitasMedica::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'fechaHora' => fake()->dateTime(),
            'estado' => fake()->randomElement(["Programada","Completada","Cancelada","Rechazada"]),
            'novedad' => fake()->word(),
            'paciente_id' => Paciente::factory(),
            'doctor_id' => Doctore::factory(),
            'consultorio_id' => fake()->numberBetween(-10000, 10000),
        ];
    }
}
