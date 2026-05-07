<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $table = 'metas';

    protected $fillable = [
        'id_usuario', 'mes', 'año',
        'ventas_meta', 'clientes_atendidos_meta',
        'clientes_visitados_meta', 'nuevos_clientes_meta',
    ];

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    // Nombres de los meses en español
    public static function nombreMes(int $mes): string
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo',
            4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre',
            10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
        ];
        return $meses[$mes] ?? '—';
    }
}