<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombres',
        'apellidos',
        'telefono',
        'email',
        'password',
        'rol_id',
    ];

    /**
     * Los atributos que deben estar ocultos para la serialización.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

        public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function rol(){
        return $this->belongsTo(Roles::class,'rol_id');
    }

    /**
     * Obtener los atributos que deben ser convertidos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
