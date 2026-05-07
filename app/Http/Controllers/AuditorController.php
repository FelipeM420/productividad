<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Meta;
use App\Models\User;
use App\Services\ProductividadAnalytics;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AuditorController extends Controller
{
    public function __construct(private ProductividadAnalytics $analytics)
    {
    }

    public function dashboard()
    {
        $mes = (int) now()->month;
        $ano = (int) now()->year;

        $totalVendedores = User::where('rol', 'vendedor')->where('activo', true)->count();
        $totalActividades = Actividad::whereMonth('fecha', $mes)->whereYear('fecha', $ano)->count();
        $totalMetas = Meta::where('mes', $mes)->where('año', $ano)->count();
        $ventasTotales = Actividad::whereMonth('fecha', $mes)->whereYear('fecha', $ano)->sum('ventas');
        $resumen = $this->analytics->construirResumen($mes, $ano);

        return view('auditor.dashboard', [
            'totalVendedores' => $totalVendedores,
            'totalActividades' => $totalActividades,
            'totalMetas' => $totalMetas,
            'ventasTotales' => $ventasTotales,
            'resumen' => $resumen,
            'mes' => $mes,
            'año' => $ano,
        ]);
    }

    public function reportes(Request $request)
    {
        [$mes, $ano, $vendedorId] = $this->filtros($request);

        $vendedores = User::where('rol', 'vendedor')->where('activo', true)->orderBy('name')->get();
        $reporte = $this->analytics->construirResumen($mes, $ano, $vendedorId);
        $totales = $this->analytics->totalesReporte($reporte);
        $proyeccion = $this->analytics->proyeccionMensual($mes, $ano, $vendedorId);
        $series = $this->analytics->seriesMensuales($ano, $vendedorId);
        $meses = $this->meses();

        return view('auditor.reportes.index', compact(
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

    public function pdf(Request $request)
    {
        [$mes, $ano, $vendedorId] = $this->filtros($request);

        $reporte = $this->analytics->construirResumen($mes, $ano, $vendedorId);
        $totales = $this->analytics->totalesReporte($reporte);
        $proyeccion = $this->analytics->proyeccionMensual($mes, $ano, $vendedorId);
        $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

        $pdf = Pdf::loadView('auditor.reportes.pdf', compact(
            'reporte',
            'totales',
            'proyeccion',
            'mes',
            'ano',
            'meses'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("reporte_{$meses[$mes]}_{$ano}.pdf");
    }

    public function estadisticas(Request $request)
    {
        [$mes, $ano, $vendedorId] = $this->filtros($request);

        $vendedores = User::where('rol', 'vendedor')->where('activo', true)->orderBy('name')->get();
        $series = $this->analytics->seriesMensuales($ano, $vendedorId);
        $comparacion = $this->analytics->construirResumen($mes, $ano, $vendedorId);
        $proyeccion = $this->analytics->proyeccionMensual($mes, $ano, $vendedorId);
        $meses = $this->meses();

        return view('auditor.estadisticas.index', compact(
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
