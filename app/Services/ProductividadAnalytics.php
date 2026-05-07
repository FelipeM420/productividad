<?php

namespace App\Services;

use App\Models\Actividad;
use App\Models\Meta;
use App\Models\User;
use Illuminate\Support\Collection;

class ProductividadAnalytics
{
    public function construirResumen(int $mes, int $ano, ?int $vendedorId = null): Collection
    {
        $query = User::where('rol', 'vendedor')->where('activo', true)
            ->with([
                'metas' => fn ($q) => $q->where('mes', $mes)->where('año', $ano),
                'actividades' => fn ($q) => $q->whereMonth('fecha', $mes)->whereYear('fecha', $ano),
            ]);

        if ($vendedorId) {
            $query->where('id', $vendedorId);
        }

        return $query->get()->map(function ($v) {
            $meta = $v->metas->first();
            $acts = $v->actividades;

            $rv = (float) $acts->sum('ventas');
            $mv = (float) ($meta?->ventas_meta ?? 0);
            $rca = (int) $acts->sum('clientes_atendidos');
            $mca = (int) ($meta?->clientes_atendidos_meta ?? 0);
            $rcv = (int) $acts->sum('clientes_visitados');
            $mcv = (int) ($meta?->clientes_visitados_meta ?? 0);
            $rnc = (int) $acts->sum('nuevos_clientes');
            $mnc = (int) ($meta?->nuevos_clientes_meta ?? 0);

            $pct = fn ($real, $meta) => $meta > 0 ? min(100, round($real / $meta * 100)) : 0;

            $pv = $pct($rv, $mv);
            $pca = $pct($rca, $mca);
            $pcv = $pct($rcv, $mcv);
            $pnc = $pct($rnc, $mnc);
            $global = (int) round(($pv + $pca + $pcv + $pnc) / 4);

            return compact(
                'v',
                'rv',
                'mv',
                'rca',
                'mca',
                'rcv',
                'mcv',
                'rnc',
                'mnc',
                'pv',
                'pca',
                'pcv',
                'pnc',
                'global'
            );
        });
    }

    public function totalesReporte(Collection $reporte): array
    {
        $totales = [
            'ventas_real' => (float) $reporte->sum('rv'),
            'ventas_meta' => (float) $reporte->sum('mv'),
            'ca_real' => (int) $reporte->sum('rca'),
            'ca_meta' => (int) $reporte->sum('mca'),
            'cv_real' => (int) $reporte->sum('rcv'),
            'cv_meta' => (int) $reporte->sum('mcv'),
            'nc_real' => (int) $reporte->sum('rnc'),
            'nc_meta' => (int) $reporte->sum('mnc'),
        ];

        $porcentaje = fn ($real, $meta) => $meta > 0 ? min(100, round($real / $meta * 100)) : 0;

        $totales['ventas_pct'] = $porcentaje($totales['ventas_real'], $totales['ventas_meta']);
        $totales['ca_pct'] = $porcentaje($totales['ca_real'], $totales['ca_meta']);
        $totales['cv_pct'] = $porcentaje($totales['cv_real'], $totales['cv_meta']);
        $totales['nc_pct'] = $porcentaje($totales['nc_real'], $totales['nc_meta']);
        $totales['global'] = (int) round((
            $totales['ventas_pct'] + $totales['ca_pct'] + $totales['cv_pct'] + $totales['nc_pct']
        ) / 4);

        return $totales;
    }

    public function seriesMensuales(int $ano, ?int $vendedorId = null): array
    {
        $series = [
            'ventas' => array_fill(0, 12, 0.0),
            'clientes_atendidos' => array_fill(0, 12, 0),
            'clientes_visitados' => array_fill(0, 12, 0),
            'nuevos_clientes' => array_fill(0, 12, 0),
        ];

        $query = Actividad::whereYear('fecha', $ano);

        if ($vendedorId) {
            $query->where('id_usuario', $vendedorId);
        }

        $query->get()->each(function (Actividad $actividad) use (&$series) {
            $index = ((int) $actividad->fecha->format('n')) - 1;

            $series['ventas'][$index] += (float) $actividad->ventas;
            $series['clientes_atendidos'][$index] += (int) $actividad->clientes_atendidos;
            $series['clientes_visitados'][$index] += (int) $actividad->clientes_visitados;
            $series['nuevos_clientes'][$index] += (int) $actividad->nuevos_clientes;
        });

        return $series;
    }

    public function proyeccionMensual(int $mes, int $ano, ?int $vendedorId = null): array
    {
        $series = $this->seriesMensuales($ano, $vendedorId);
        $mesesHistoricos = collect(range(1, max(1, $mes - 1)))
            ->filter(function (int $historicoMes) use ($mes, $series) {
                if ($historicoMes >= $mes) {
                    return false;
                }

                $index = $historicoMes - 1;

                return collect($series)->sum(fn ($valores) => $valores[$index] ?? 0) > 0;
            })
            ->values();

        $metas = $this->metasDelMes($mes, $ano, $vendedorId);
        $cantidadMeses = $mesesHistoricos->count();

        $metricas = [
            'ventas' => ['label' => 'Ventas', 'real' => (float) $series['ventas'][$mes - 1], 'meta' => $metas['ventas']],
            'clientes_atendidos' => ['label' => 'Clientes atendidos', 'real' => (int) $series['clientes_atendidos'][$mes - 1], 'meta' => $metas['clientes_atendidos']],
            'clientes_visitados' => ['label' => 'Clientes visitados', 'real' => (int) $series['clientes_visitados'][$mes - 1], 'meta' => $metas['clientes_visitados']],
            'nuevos_clientes' => ['label' => 'Nuevos clientes', 'real' => (int) $series['nuevos_clientes'][$mes - 1], 'meta' => $metas['nuevos_clientes']],
        ];

        foreach ($metricas as $clave => $metrica) {
            $esperado = $cantidadMeses > 0
                ? round($mesesHistoricos->sum(fn ($historicoMes) => $series[$clave][$historicoMes - 1]) / $cantidadMeses, 2)
                : $metrica['meta'];

            $metricas[$clave]['esperado'] = $esperado;
            $metricas[$clave]['diferencia'] = round($metrica['real'] - $esperado, 2);
            $metricas[$clave]['cumplimiento_esperado'] = $esperado > 0
                ? min(100, round($metrica['real'] / $esperado * 100))
                : 0;
        }

        return [
            'metricas' => $metricas,
            'meses_historicos' => $cantidadMeses,
            'fuente' => $cantidadMeses > 0
                ? 'Promedio de los meses anteriores con actividad'
                : 'Sin meses anteriores; se usa la meta del periodo como referencia',
        ];
    }

    private function metasDelMes(int $mes, int $ano, ?int $vendedorId = null): array
    {
        $query = Meta::where('mes', $mes)->where('año', $ano);

        if ($vendedorId) {
            $query->where('id_usuario', $vendedorId);
        }

        return [
            'ventas' => (float) (clone $query)->sum('ventas_meta'),
            'clientes_atendidos' => (int) (clone $query)->sum('clientes_atendidos_meta'),
            'clientes_visitados' => (int) (clone $query)->sum('clientes_visitados_meta'),
            'nuevos_clientes' => (int) (clone $query)->sum('nuevos_clientes_meta'),
        ];
    }
}
