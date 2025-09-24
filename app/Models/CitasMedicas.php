<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CitasMedicas extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = "citas_medicas";
    protected $fillable = [
        'fechaHora',
        'estado',
        'novedad',
        'paciente_id',
        'doctor_id',
        'consultorio_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'fechaHora' => 'datetime',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Pacientes::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctores::class);
    }
}
