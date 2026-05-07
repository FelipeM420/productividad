<?php

use App\Models\Actividad;
use App\Models\Meta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function crearUsuario(string $rol, array $attributes = []): User
{
    $nombre = $attributes['name'] ?? ucfirst($rol).' Demo '.uniqid();

    return User::factory()->create(array_merge([
        'name' => $nombre,
        'email' => $rol.'@example.com',
        'password' => Hash::make('Admin1234'),
        'rol' => $rol,
        'activo' => true,
    ], $attributes));
}

it('redirige al dashboard de administrador despues del login', function () {
    $admin = crearUsuario('admin');

    $this->post('/login', [
        'email' => $admin->email,
        'password' => 'Admin1234',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});

it('redirige al dashboard de vendedor despues del login', function () {
    $vendedor = crearUsuario('vendedor');

    $this->post('/login', [
        'email' => strtoupper($vendedor->email),
        'password' => 'Admin1234',
    ])->assertRedirect(route('vendedor.dashboard'));

    $this->assertAuthenticatedAs($vendedor);
    $this->get(route('vendedor.dashboard'))->assertOk();
});

it('redirige al dashboard de auditor despues del login', function () {
    $auditor = crearUsuario('auditor');

    $this->post('/login', [
        'email' => $auditor->email,
        'password' => 'Admin1234',
    ])->assertRedirect(route('auditor.dashboard'));

    $this->assertAuthenticatedAs($auditor);
});

it('bloquea el acceso si el usuario esta inactivo', function () {
    $inactivo = crearUsuario('vendedor', [
        'email' => 'inactivo@example.com',
        'activo' => false,
    ]);

    $this->from(route('login'))
        ->post('/login', [
            'email' => $inactivo->email,
            'password' => 'Admin1234',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('el dashboard del vendedor solo muestra sus propias actividades', function () {
    $vendedor = crearUsuario('vendedor', ['email' => 'vendedor@example.com']);
    $otroVendedor = crearUsuario('vendedor', ['email' => 'otro@example.com']);

    Actividad::create([
        'id_usuario' => $vendedor->id,
        'fecha' => now()->toDateString(),
        'ventas' => 1000,
        'clientes_atendidos' => 5,
        'clientes_visitados' => 4,
        'nuevos_clientes' => 1,
    ]);

    Actividad::create([
        'id_usuario' => $otroVendedor->id,
        'fecha' => now()->subDay()->toDateString(),
        'ventas' => 2000,
        'clientes_atendidos' => 8,
        'clientes_visitados' => 7,
        'nuevos_clientes' => 2,
    ]);

    $response = $this->actingAs($vendedor)->get(route('vendedor.dashboard'));

    $response->assertOk();
    $response->assertSee($vendedor->name);
    $response->assertDontSee($otroVendedor->name);
});

it('muestra analitica avanzada en reportes del auditor', function () {
    $auditor = crearUsuario('auditor', ['email' => 'auditor-reportes@example.com']);
    $vendedor = crearUsuario('vendedor', ['email' => 'vendedor-reportes@example.com']);

    Actividad::create([
        'id_usuario' => $vendedor->id,
        'fecha' => '2026-01-15',
        'ventas' => 1000,
        'clientes_atendidos' => 10,
        'clientes_visitados' => 8,
        'nuevos_clientes' => 2,
    ]);

    Actividad::create([
        'id_usuario' => $vendedor->id,
        'fecha' => '2026-02-15',
        'ventas' => 3000,
        'clientes_atendidos' => 20,
        'clientes_visitados' => 16,
        'nuevos_clientes' => 4,
    ]);

    Actividad::create([
        'id_usuario' => $vendedor->id,
        'fecha' => '2026-03-15',
        'ventas' => 2500,
        'clientes_atendidos' => 15,
        'clientes_visitados' => 13,
        'nuevos_clientes' => 3,
    ]);

    $this->actingAs($auditor)
        ->get(route('auditor.reportes.index', ['mes' => 3, 'ano' => 2026]))
        ->assertOk()
        ->assertSee('Datos esperados')
        ->assertSee('Promedio de los meses anteriores con actividad');
});

it('el administrador accede a reportes y estadisticas avanzadas', function () {
    $admin = crearUsuario('admin', ['email' => 'admin-analitica@example.com']);
    $vendedor = crearUsuario('vendedor', ['email' => 'vendedor-analitica@example.com']);

    Meta::create([
        'id_usuario' => $vendedor->id,
        'mes' => 5,
        'año' => 2026,
        'ventas_meta' => 5000,
        'clientes_atendidos_meta' => 30,
        'clientes_visitados_meta' => 25,
        'nuevos_clientes_meta' => 5,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.reportes.index', ['mes' => 5, 'ano' => 2026]))
        ->assertOk()
        ->assertSee('Datos esperados');

    $this->actingAs($admin)
        ->get(route('admin.estadisticas.index', ['mes' => 5, 'ano' => 2026]))
        ->assertOk()
        ->assertSee('Estadisticas del sistema');
});
