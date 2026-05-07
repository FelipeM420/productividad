<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Meta;
use App\Models\Actividad;

class AdminController extends Controller
{
    public function dashboard()
    {
        $mes = (int) date('n');
        $año = (int) date('Y');

        $totalUsuarios   = User::where('activo', true)->count();
        $totalVendedores = User::where('rol', 'vendedor')->where('activo', true)->count();
        $totalMetas      = Meta::where('mes', $mes)->where('año', $año)->count();
        $totalActiv      = Actividad::whereMonth('fecha', $mes)->whereYear('fecha', $año)->count();

        $ultimasActividades = Actividad::with('vendedor')
            ->orderByDesc('fecha')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $topVendedores = User::where('rol', 'vendedor')
            ->where('activo', true)
            ->with(['actividades' => fn($q) => $q->whereMonth('fecha', $mes)->whereYear('fecha', $año),
                    'metas'       => fn($q) => $q->where('mes', $mes)->where('año', $año)])
            ->get()
            ->map(function ($v) {
                $totalVentas = $v->actividades->sum('ventas');
                $meta        = $v->metas->first()?->ventas_meta ?? 0;
                $pct         = $meta > 0 ? min(100, round($totalVentas / $meta * 100)) : 0;
                return ['nombre' => $v->name, 'ventas' => $totalVentas, 'meta' => $meta, 'pct' => $pct];
            })
            ->sortByDesc('ventas');

        return view('admin.dashboard', compact(
            'totalUsuarios','totalVendedores','totalMetas',
            'totalActiv','ultimasActividades','topVendedores'
        ));
    }
}