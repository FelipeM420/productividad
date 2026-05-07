<?php

namespace App\Http\Controllers;

use App\Models\Meta;
use App\Models\User;
use Illuminate\Http\Request;

class MetaController extends Controller
{
    public function index()
    {
        $metas = Meta::with('vendedor')
                     ->orderBy('año', 'desc')
                     ->orderBy('mes', 'desc')
                     ->get();
        return view('admin.metas.index', compact('metas'));
    }

    public function create()
    {
        $vendedores = User::where('rol', 'vendedor')->where('activo', true)->get();
        return view('admin.metas.create', compact('vendedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_usuario'              => 'required|exists:users,id',
            'mes'                     => 'required|integer|between:1,12',
            'año'                     => 'required|integer|min:2020|max:2099',
            'ventas_meta'             => 'required|numeric|min:0',
            'clientes_atendidos_meta' => 'required|integer|min:0',
            'clientes_visitados_meta' => 'required|integer|min:0',
            'nuevos_clientes_meta'    => 'required|integer|min:0',
        ], [
            'id_usuario.required' => 'Selecciona un vendedor.',
            'id_usuario.exists'   => 'Vendedor no válido.',
            'mes.required'        => 'El mes es obligatorio.',
            'año.required'        => 'El año es obligatorio.',
        ]);

        // Verificar que no exista ya una meta para ese vendedor/mes/año
        $existe = Meta::where('id_usuario', $request->id_usuario)
                      ->where('mes', $request->mes)
                      ->where('año', $request->año)
                      ->exists();

        if ($existe) {
            return back()->withInput()
                         ->with('error', 'Ya existe una meta para ese vendedor en ese mes y año.');
        }

        Meta::create($request->only([
            'id_usuario', 'mes', 'año',
            'ventas_meta', 'clientes_atendidos_meta',
            'clientes_visitados_meta', 'nuevos_clientes_meta',
        ]));

        return redirect()->route('admin.metas.index')
                         ->with('success', 'Meta asignada correctamente.');
    }

    public function edit(Meta $meta)
    {
        $vendedores = User::where('rol', 'vendedor')->where('activo', true)->get();
        return view('admin.metas.edit', compact('meta', 'vendedores'));
    }

    public function update(Request $request, Meta $meta)
    {
        $request->validate([
            'ventas_meta'             => 'required|numeric|min:0',
            'clientes_atendidos_meta' => 'required|integer|min:0',
            'clientes_visitados_meta' => 'required|integer|min:0',
            'nuevos_clientes_meta'    => 'required|integer|min:0',
        ]);

        $meta->update($request->only([
            'ventas_meta', 'clientes_atendidos_meta',
            'clientes_visitados_meta', 'nuevos_clientes_meta',
        ]));

        return redirect()->route('admin.metas.index')
                         ->with('success', 'Meta actualizada correctamente.');
    }

    public function destroy(Meta $meta)
    {
        $meta->delete();
        return redirect()->route('admin.metas.index')
                         ->with('success', 'Meta eliminada.');
    }
}