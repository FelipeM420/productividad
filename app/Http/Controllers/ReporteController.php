<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Meta;
use App\Models\Actividad;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $mes       = $request->input('mes', date('n'));
        $año       = $request->input('año', date('Y'));
        $vendedorId = $request->input('vendedor');

        $vendedores = User::where('rol','vendedor')->where('activo',true)->get();

        $query = User::where('rol','vendedor')->where('activo',true)
            ->with([
                'metas'       => fn($q) => $q->where('mes',$mes)->where('año',$año),
                'actividades' => fn($q) => $q->whereMonth('fecha',$mes)->whereYear('fecha',$año),
            ]);

        if ($vendedorId) $query->where('id', $vendedorId);

        $reporte = $query->get()->map(function ($v) {
            $meta  = $v->metas->first();
            $acts  = $v->actividades;
            return [
                'nombre'          => $v->name,
                'ventas_real'     => $acts->sum('ventas'),
                'ventas_meta'     => $meta?->ventas_meta ?? 0,
                'ca_real'         => $acts->sum('clientes_atendidos'),
                'ca_meta'         => $meta?->clientes_atendidos_meta ?? 0,
                'cv_real'         => $acts->sum('clientes_visitados'),
                'cv_meta'         => $meta?->clientes_visitados_meta ?? 0,
                'nc_real'         => $acts->sum('nuevos_clientes'),
                'nc_meta'         => $meta?->nuevos_clientes_meta ?? 0,
            ];
        });

        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

        return view('admin.reportes.index',
            compact('reporte','vendedores','mes','año','vendedorId','meses'));
    }

    public function pdf(Request $request)
    {
        // Se implementa en el Paso 6
        return back()->with('error', 'PDF disponible en el Paso 6.');
    }
}