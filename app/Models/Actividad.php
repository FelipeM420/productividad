<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'id_usuario', 'fecha',
        'ventas', 'clientes_atendidos',
        'clientes_visitados', 'nuevos_clientes',
    ];

    // ── IMPORTANTE: fuerza id_usuario a integer ──
    protected $casts = [
        'fecha'       => 'date',
        'id_usuario'  => 'integer',
        'ventas'      => 'float',
    ];

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}