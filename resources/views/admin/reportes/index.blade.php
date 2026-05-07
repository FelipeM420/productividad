@extends('layouts.app')
@section('titulo', 'Reportes de Cumplimiento')
@section('contenido')

@php
    $color = fn($p) => $p >= 100 ? 'success' : ($p >= 60 ? 'warning' : 'danger');
    $mesesLabels = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    $formatMetric = fn($key, $value) => $key === 'ventas'
        ? '$'.number_format($value, 0, ',', '.')
        : number_format($value, 0, ',', '.');
@endphp

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reportes.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Mes</label>
                <select name="mes" class="form-select">
                    @foreach($meses as $i => $m)
                        <option value="{{ $i + 1 }}" {{ $mes == ($i + 1) ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Ano</label>
                <select name="ano" class="form-select">
                    @for($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $ano == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Vendedor</label>
                <select name="vendedor" class="form-select">
                    <option value="">Todos</option>
                    @foreach($vendedores as $v)
                        <option value="{{ $v->id }}" {{ $vendedorId == $v->id ? 'selected' : '' }}>
                            {{ $v->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary flex-fill">
                    <i class="bi bi-search me-1"></i> Filtrar
                </button>
                <a href="{{ route('admin.estadisticas.index', request()->query()) }}" class="btn btn-outline-primary flex-fill">
                    <i class="bi bi-bar-chart-line me-1"></i> Estadisticas
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-speedometer me-2 text-primary"></i>Datos esperados para {{ $meses[$mes - 1] }} {{ $ano }}</span>
        <span class="text-muted small">{{ $proyeccion['fuente'] }}</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($proyeccion['metricas'] as $key => $m)
                @php $badge = $color($m['cumplimiento_esperado']); @endphp
                <div class="col-md-6 col-xl-3">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="text-muted small">{{ $m['label'] }}</div>
                                <div class="fw-bold fs-5">{{ $formatMetric($key, $m['real']) }}</div>
                            </div>
                            <span class="badge bg-{{ $badge }}">{{ $m['cumplimiento_esperado'] }}%</span>
                        </div>
                        <div class="mt-3 small text-muted">Esperado: {{ $formatMetric($key, $m['esperado']) }}</div>
                        <div class="small text-{{ $m['diferencia'] >= 0 ? 'success' : 'danger' }}">
                            Diferencia: {{ $formatMetric($key, $m['diferencia']) }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header py-3">
                <i class="bi bi-graph-up-arrow me-2 text-success"></i>Ventas por mes - {{ $ano }}
            </div>
            <div class="card-body">
                <canvas id="chartVentasAdminReporte" style="max-height:300px"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header py-3">
                <i class="bi bi-people me-2 text-warning"></i>Metricas comerciales - {{ $ano }}
            </div>
            <div class="card-body">
                <canvas id="chartClientesAdminReporte" style="max-height:300px"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>
        Cumplimiento - {{ $meses[$mes - 1] }} {{ $ano }}
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Vendedor</th>
                        <th class="text-end">Ventas</th>
                        <th class="text-end">Meta</th>
                        <th class="text-center">% V</th>
                        <th class="text-end">Atendidos</th>
                        <th class="text-end">Visitados</th>
                        <th class="text-end">Nuevos</th>
                        <th class="text-center">Global</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reporte as $r)
                    <tr>
                        <td class="fw-semibold">{{ $r['v']->name }}</td>
                        <td class="text-end">${{ number_format($r['rv'], 0, ',', '.') }}</td>
                        <td class="text-end text-muted">${{ number_format($r['mv'], 0, ',', '.') }}</td>
                        <td class="text-center"><span class="badge bg-{{ $color($r['pv']) }}">{{ $r['pv'] }}%</span></td>
                        <td class="text-end">{{ $r['rca'] }} / <span class="text-muted">{{ $r['mca'] }}</span></td>
                        <td class="text-end">{{ $r['rcv'] }} / <span class="text-muted">{{ $r['mcv'] }}</span></td>
                        <td class="text-end">{{ $r['rnc'] }} / <span class="text-muted">{{ $r['mnc'] }}</span></td>
                        <td class="text-center"><span class="badge bg-{{ $color($r['global']) }} fs-6">{{ $r['global'] }}%</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Sin datos para el periodo seleccionado.</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>Total</th>
                        <th class="text-end">${{ number_format($totales['ventas_real'], 0, ',', '.') }}</th>
                        <th class="text-end">${{ number_format($totales['ventas_meta'], 0, ',', '.') }}</th>
                        <th class="text-center">{{ $totales['ventas_pct'] }}%</th>
                        <th class="text-end">{{ $totales['ca_real'] }} / {{ $totales['ca_meta'] }}</th>
                        <th class="text-end">{{ $totales['cv_real'] }} / {{ $totales['cv_meta'] }}</th>
                        <th class="text-end">{{ $totales['nc_real'] }} / {{ $totales['nc_meta'] }}</th>
                        <th class="text-center">{{ $totales['global'] }}%</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labelsAdminReporte = @json($mesesLabels);
const seriesAdminReporte = @json($series);

new Chart(document.getElementById('chartVentasAdminReporte'), {
    type: 'bar',
    data: {
        labels: labelsAdminReporte,
        datasets: [{
            label: 'Ventas',
            data: seriesAdminReporte.ventas,
            backgroundColor: 'rgba(37, 99, 235, .72)',
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
    }
});

new Chart(document.getElementById('chartClientesAdminReporte'), {
    type: 'line',
    data: {
        labels: labelsAdminReporte,
        datasets: [
            { label: 'Atendidos', data: seriesAdminReporte.clientes_atendidos, borderColor: '#16a34a', tension: .25 },
            { label: 'Visitados', data: seriesAdminReporte.clientes_visitados, borderColor: '#f59e0b', tension: .25 },
            { label: 'Nuevos', data: seriesAdminReporte.nuevos_clientes, borderColor: '#dc2626', tension: .25 },
        ]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
    }
});
</script>
@endsection
