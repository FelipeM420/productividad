<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Meta;
use Illuminate\Support\Facades\Auth;

class VendedorController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $mes = (int) now()->month;
        $ano = (int) now()->year;

        $meta = Meta::where('id_usuario', $user->id)
            ->where('mes', $mes)
            ->where('año', $ano)
            ->first();

        $totales = Actividad::where('id_usuario', $user->id)
            ->whereMonth('fecha', $mes)
            ->whereYear('fecha', $ano)
            ->selectRaw('
                COALESCE(SUM(ventas),0)             AS total_ventas,
                COALESCE(SUM(clientes_atendidos),0) AS total_ca,
                COALESCE(SUM(clientes_visitados),0) AS total_cv,
                COALESCE(SUM(nuevos_clientes),0)    AS total_nc
            ')
            ->first();

        $actividades = Actividad::with('vendedor')
            ->where('id_usuario', $user->id)
            ->orderByDesc('fecha')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $registroHoy = Actividad::where('id_usuario', $user->id)
            ->where('fecha', today()->toDateString())
            ->exists();

        $pcts = $this->calcularPorcentajes($totales, $meta);

        return view('vendedor.dashboard', [
            'meta' => $meta,
            'totales' => $totales,
            'actividades' => $actividades,
            'registroHoy' => $registroHoy,
            'pcts' => $pcts,
            'mes' => $mes,
            'año' => $ano,
        ]);
    }

    private function calcularPorcentajes($totales, $meta): array
    {
        $calc = fn ($real, $objetivo) => $objetivo > 0
            ? min(100, round($real / $objetivo * 100))
            : 0;

        return [
            'ventas' => $calc($totales->total_ventas ?? 0, $meta?->ventas_meta ?? 0),
            'ca' => $calc($totales->total_ca ?? 0, $meta?->clientes_atendidos_meta ?? 0),
            'cv' => $calc($totales->total_cv ?? 0, $meta?->clientes_visitados_meta ?? 0),
            'nc' => $calc($totales->total_nc ?? 0, $meta?->nuevos_clientes_meta ?? 0),
        ];
    }
}
