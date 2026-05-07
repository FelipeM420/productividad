<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ProductividadAnalytics;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function __construct(private ProductividadAnalytics $analytics)
    {
    }

    public function index(Request $request)
    {
        [$mes, $ano, $vendedorId] = $this->filtros($request);

        $vendedores = User::where('rol', 'vendedor')->where('activo', true)->orderBy('name')->get();
        $reporte = $this->analytics->construirResumen($mes, $ano, $vendedorId);
        $totales = $this->analytics->totalesReporte($reporte);
        $proyeccion = $this->analytics->proyeccionMensual($mes, $ano, $vendedorId);
        $series = $this->analytics->seriesMensuales($ano, $vendedorId);
        $meses = $this->meses();

        return view('admin.reportes.index', compact(
            'reporte',
            'totales',
            'proyeccion',
            'series',
            'vendedores',
            'mes',
            'ano',
            'vendedorId',
            'meses'
        ));
    }

    public function estadisticas(Request $request)
    {
        [$mes, $ano, $vendedorId] = $this->filtros($request);

        $vendedores = User::where('rol', 'vendedor')->where('activo', true)->orderBy('name')->get();
        $series = $this->analytics->seriesMensuales($ano, $vendedorId);
        $comparacion = $this->analytics->construirResumen($mes, $ano, $vendedorId);
        $proyeccion = $this->analytics->proyeccionMensual($mes, $ano, $vendedorId);
        $meses = $this->meses();

        return view('admin.reportes.estadisticas', compact(
            'series',
            'comparacion',
            'proyeccion',
            'ano',
            'mes',
            'vendedorId',
            'vendedores',
            'meses'
        ));
    }

    public function pdf(Request $request)
    {
        return back()->with('error', 'La exportacion PDF avanzada esta disponible para el rol auditor.');
    }

    private function filtros(Request $request): array
    {
        $mes = (int) $request->input('mes', now()->month);
        $ano = (int) $request->input('ano', $request->input('año', $request->input('aÃ±o', now()->year)));
        $vendedorId = $request->filled('vendedor') ? (int) $request->input('vendedor') : null;

        return [$mes, $ano, $vendedorId];
    }

    private function meses(): array
    {
        return ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    }
}
