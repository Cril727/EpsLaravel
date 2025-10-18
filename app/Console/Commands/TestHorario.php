<?php

namespace App\Console\Commands;

use App\Models\Horarios;
use Illuminate\Console\Command;

class TestHorario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-horario';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test horario creation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $horario = Horarios::create([
                'horaInicio' => '08:00:00',
                'horaFin' => '17:00:00',
                'estado' => 'Activo',
                'doctor_id' => 3
            ]);
            $this->info('Horario created successfully: ' . $horario->id);
        } catch (\Exception $e) {
            $this->error('Error creating horario: ' . $e->getMessage());
        }
    }
}
