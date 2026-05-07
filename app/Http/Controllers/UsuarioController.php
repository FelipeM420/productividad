<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('rol')->orderBy('name')->get();
        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'rol'      => 'required|in:admin,vendedor,auditor',
        ], [
            'name.required'      => 'El nombre es obligatorio.',
            'email.required'     => 'El correo es obligatorio.',
            'email.unique'       => 'Ese correo ya está registrado.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'Mínimo 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'rol.required'       => 'Selecciona un rol.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'rol'      => $request->rol,
            'activo'   => true,
        ]);

        return redirect()->route('admin.usuarios.index')
                         ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('admin.usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => ['required','email', Rule::unique('users','email')->ignore($usuario->id)],
            'password' => 'nullable|min:6|confirmed',
            'rol'      => 'required|in:admin,vendedor,auditor',
            'activo'   => 'boolean',
        ], [
            'email.unique'       => 'Ese correo ya está en uso.',
            'password.min'       => 'Mínimo 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $data = [
            'name'   => $request->name,
            'email'  => $request->email,
            'rol'    => $request->rol,
            'activo' => $request->boolean('activo'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')
                         ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === Auth::id()) {
            return redirect()->route('admin.usuarios.index')
                             ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->delete();

        return redirect()->route('admin.usuarios.index')
                         ->with('success', 'Usuario eliminado.');
    }
}