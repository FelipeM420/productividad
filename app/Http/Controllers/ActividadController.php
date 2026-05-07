<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActividadController extends Controller
{
    public function index()
    {
        $actividades = Actividad::where('id_usuario', Auth::id())
            ->orderByDesc('fecha')
            ->paginate(15);

        return view('vendedor.actividades.index', compact('actividades'));
    }

    public function create()
    {
        $hoy = now()->toDateString();

        $yaRegistro = Actividad::where('id_usuario', Auth::id())
            ->where('fecha', $hoy)
            ->exists();

        if ($yaRegistro) {
            return redirect()->route('vendedor.actividades.index')
                ->with('error', 'Ya registraste tu actividad de hoy. Puedes editarla si necesitas corregirla.');
        }

        return view('vendedor.actividades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ventas'             => 'required|numeric|min:0',
            'clientes_atendidos' => 'required|integer|min:0',
            'clientes_visitados' => 'required|integer|min:0',
            'nuevos_clientes'    => 'required|integer|min:0',
        ]);

        $hoy = now()->toDateString();

        $yaRegistro = Actividad::where('id_usuario', Auth::id())
            ->where('fecha', $hoy)
            ->exists();

        if ($yaRegistro) {
            return redirect()->route('vendedor.actividades.index')
                ->with('error', 'Ya registraste tu actividad de hoy.');
        }

        Actividad::create([
            'id_usuario'         => Auth::id(),
            'fecha'              => $hoy,
            'ventas'             => $request->ventas,
            'clientes_atendidos' => $request->clientes_atendidos,
            'clientes_visitados' => $request->clientes_visitados,
            'nuevos_clientes'    => $request->nuevos_clientes,
        ]);

        return redirect()->route('vendedor.actividades.index')
            ->with('success', '¡Actividad del día registrada correctamente!');
    }

    public function edit($id)
    {
        // Busca la actividad SOLO si pertenece al usuario logueado
        // Si no es suya → 404 automático, nunca llega al 403
        $actividad = Actividad::where('id', $id)
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        return view('vendedor.actividades.edit', compact('actividad'));
    }

    public function update(Request $request, $id)
    {
        // Mismo filtro: solo encuentra la actividad si es del usuario logueado
        $actividad = Actividad::where('id', $id)
            ->where('id_usuario', Auth::id())
            ->firstOrFail();

        $request->validate([
            'ventas'             => 'required|numeric|min:0',
            'clientes_atendidos' => 'required|integer|min:0',
            'clientes_visitados' => 'required|integer|min:0',
            'nuevos_clientes'    => 'required|integer|min:0',
        ]);

        $actividad->update($request->only([
            'ventas',
            'clientes_atendidos',
            'clientes_visitados',
            'nuevos_clientes',
        ]));

        return redirect()->route('vendedor.actividades.index')
            ->with('success', 'Actividad actualizada correctamente.');
    }
}