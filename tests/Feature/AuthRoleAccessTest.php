<?php

use App\Models\Actividad;
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
