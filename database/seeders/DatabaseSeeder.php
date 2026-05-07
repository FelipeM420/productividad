<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Meta;
use App\Models\Actividad;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Usuarios ──────────────────────────────────────
        $admin = User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@productividad.com',
            'password' => Hash::make('Admin1234'),
            'rol'      => 'admin',
            'activo'   => true,
        ]);

        $vendedor1 = User::create([
            'name'     => 'Carlos Vendedor',
            'email'    => 'vendedor@productividad.com',
            'password' => Hash::make('Admin1234'),
            'rol'      => 'vendedor',
            'activo'   => true,
        ]);

        $vendedor2 = User::create([
            'name'     => 'Ana Gómez',
            'email'    => 'ana@productividad.com',
            'password' => Hash::make('Admin1234'),
            'rol'      => 'vendedor',
            'activo'   => true,
        ]);

        $auditor = User::create([
            'name'     => 'Laura Auditora',
            'email'    => 'auditor@productividad.com',
            'password' => Hash::make('Admin1234'),
            'rol'      => 'auditor',
            'activo'   => true,
        ]);

        // ── Metas del mes actual ──────────────────────────
        $mes = (int) date('n');
        $año = (int) date('Y');

        Meta::create([
            'id_usuario'              => $vendedor1->id,
            'mes'                     => $mes,
            'año'                     => $año,
            'ventas_meta'             => 5000000,
            'clientes_atendidos_meta' => 80,
            'clientes_visitados_meta' => 60,
            'nuevos_clientes_meta'    => 10,
        ]);

        Meta::create([
            'id_usuario'              => $vendedor2->id,
            'mes'                     => $mes,
            'año'                     => $año,
            'ventas_meta'             => 4000000,
            'clientes_atendidos_meta' => 70,
            'clientes_visitados_meta' => 50,
            'nuevos_clientes_meta'    => 8,
        ]);

        // ── Actividades de ejemplo (últimos 7 días) ───────
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->toDateString();

            Actividad::create([
                'id_usuario'        => $vendedor1->id,
                'fecha'             => $fecha,
                'ventas'            => rand(200000, 800000),
                'clientes_atendidos'=> rand(5, 15),
                'clientes_visitados'=> rand(3, 12),
                'nuevos_clientes'   => rand(0, 3),
            ]);

            Actividad::create([
                'id_usuario'        => $vendedor2->id,
                'fecha'             => $fecha,
                'ventas'            => rand(150000, 600000),
                'clientes_atendidos'=> rand(4, 12),
                'clientes_visitados'=> rand(2, 10),
                'nuevos_clientes'   => rand(0, 2),
            ]);
        }
    }
}