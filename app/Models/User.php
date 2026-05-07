<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name', 'email', 'password', 'rol', 'activo',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo'   => 'boolean',
        ];
    }

    // Relaciones
    public function metas()
    {
        return $this->hasMany(Meta::class, 'id_usuario');
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class, 'id_usuario');
    }

    // Helpers de rol
    public function esAdmin(): bool    { return $this->rol === 'admin'; }
    public function esVendedor(): bool { return $this->rol === 'vendedor'; }
    public function esAuditor(): bool  { return $this->rol === 'auditor'; }

    public function dashboardRouteName(): ?string
    {
        return match ($this->rol) {
            'admin' => 'admin.dashboard',
            'vendedor' => 'vendedor.dashboard',
            'auditor' => 'auditor.dashboard',
            default => null,
        };
    }
}
