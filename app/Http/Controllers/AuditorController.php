<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Meta;
use App\Models\Actividad;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditorController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────
    public function dashboard()
    {
        $mes = (int) date('n');
        $año = (int) date('Y');

        $totalVendedores = User::where('rol','vendedor')->where('activo',true)->count();
        $totalActividades = Actividad::whereMonth('fecha',$mes)->whereYear('fecha',$año)->count();
        $totalMetas = Meta::where('mes',$mes)->where('año',$año)->count();

        $ventasTotales = Actividad::whereMonth('fecha',$mes)
            ->whereYear('fecha',$año)
            ->sum('ventas');

        $resumen = $this->construirResumen($mes, $año);

        return view('auditor.dashboard', compact(
            'totalVendedores','totalActividades','totalMetas',
            'ventasTotales','resumen','mes','año'
        ));
    }

    // ── Reportes ──────────────────────────────────────────
    public function reportes(Request $request)
    {
        $mes        = $request->input('mes', date('n'));
        $año        = $request->input('año', date('Y'));
        $vendedorId = $request->input('vendedor');

        $vendedores = User::where('rol','vendedor')->where('activo',true)->get();
        $reporte    = $this->construirResumen($mes, $año, $vendedorId);

        $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

        return view('auditor.reportes.index',
            compact('reporte','vendedores','mes','año','vendedorId','meses'));
    }

    // ── Exportar PDF ──────────────────────────────────────
    public function pdf(Request $request)
    {
        $mes        = $request->input('mes', date('n'));
        $año        = $request->input('año', date('Y'));
        $vendedorId = $request->input('vendedor');

        $reporte = $this->construirResumen($mes, $año, $vendedorId);

        $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

        $pdf = Pdf::loadView('auditor.reportes.pdf', compact('reporte','mes','año','meses'))
                  ->setPaper('a4','landscape');

        return $pdf->download("reporte_{$meses[$mes]}_{$año}.pdf");
    }

    // ── Estadísticas ──────────────────────────────────────
    public function estadisticas(Request $request)
    {
        $año = $request->input('año', date('Y'));

        // Ventas mensuales de todos los vendedores agrupadas por mes
        $ventasMensuales = Actividad::whereYear('fecha', $año)
            ->selectRaw('MONTH(fecha) as mes, SUM(ventas) as total')
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total','mes');

        // Preparar array de 12 meses (rellenar vacíos con 0)
        $ventasPorMes = [];
        for ($i = 1; $i <= 12; $i++) {
            $ventasPorMes[] = (float)($ventasMensuales[$i] ?? 0);
        }

        // Comparación meta vs real por vendedor (mes actual)
        $mes = (int)date('n');
        $comparacion = $this->construirResumen($mes, $año);

        $vendedores = User::where('rol','vendedor')->where('activo',true)->get();

        return view('auditor.estadisticas.index',
            compact('ventasPorMes','comparacion','año','mes','vendedores'));
    }

    // ── Helper privado ────────────────────────────────────
    private function construirResumen(int $mes, int $año, ?int $vendedorId = null): \Illuminate\Support\Collection
    {
        $query = User::where('rol','vendedor')->where('activo',true)
            ->with([
                'metas'       => fn($q) => $q->where('mes',$mes)->where('año',$año),
                'actividades' => fn($q) => $q->whereMonth('fecha',$mes)->whereYear('fecha',$año),
            ]);

        if ($vendedorId) $query->where('id', $vendedorId);

        return $query->get()->map(function ($v) {
            $meta = $v->metas->first();
            $acts = $v->actividades;

            $rv = (float)$acts->sum('ventas');
            $mv = (float)($meta?->ventas_meta ?? 0);
            $rca = (int)$acts->sum('clientes_atendidos');
            $mca = (int)($meta?->clientes_atendidos_meta ?? 0);
            $rcv = (int)$acts->sum('clientes_visitados');
            $mcv = (int)($meta?->clientes_visitados_meta ?? 0);
            $rnc = (int)$acts->sum('nuevos_clientes');
            $mnc = (int)($meta?->nuevos_clientes_meta ?? 0);

            $pct = fn($r,$m) => $m > 0 ? min(100, round($r/$m*100)) : 0;

            $pv  = $pct($rv, $mv);
            $pca = $pct($rca, $mca);
            $pcv = $pct($rcv, $mcv);
            $pnc = $pct($rnc, $mnc);
            $global = (int)round(($pv+$pca+$pcv+$pnc)/4);

            return compact('v','rv','mv','rca','mca','rcv','mcv','rnc','mnc',
                           'pv','pca','pcv','pnc','global');
        });
    }
}