@extends('layouts.app')
@section('titulo', 'Estadisticas del Sistema')
@section('contenido')

@php
    $mesesLabels = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    $color = fn($p) => $p >= 100 ? 'success' : ($p >= 60 ? 'warning' : 'danger');
    $formatMetric = fn($key, $value) => $key === 'ventas'
        ? '$'.number_format($value, 0, ',', '.')
        : number_format($value, 0, ',', '.');
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Estadisticas del sistema</h5>
    <form method="GET" class="d-flex flex-wrap gap-2">
        <select name="mes" class="form-select form-select-sm" style="width:130px">
            @foreach($meses as $i => $m)
                <option value="{{ $i + 1 }}" {{ $mes == ($i + 1) ? 'selected' : '' }}>{{ $m }}</option>
            @endforeach
        </select>
        <select name="ano" class="form-select form-select-sm" style="width:100px">
            @for($y = date('Y') - 3; $y <= date('Y') + 1; $y++)
                <option value="{{ $y }}" {{ $ano == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <select name="vendedor" class="form-select form-select-sm" style="width:190px">
            <option value="">Todos</option>
            @foreach($vendedores as $v)
                <option value="{{ $v->id }}" {{ $vendedorId == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
            @endforeach
        </select>
        <button class="btn btn-sm btn-primary">Aplicar</button>
    </form>
</div>

<div class="card mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-activity me-2 text-primary"></i>Proyeccion operativa</span>
        <span class="text-muted small">{{ $proyeccion['fuente'] }}</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @foreach($proyeccion['metricas'] as $key => $m)
                @php $badge = $color($m['cumplimiento_esperado']); @endphp
                <div class="col-md-6 col-xl-3">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="text-muted small">{{ $m['label'] }}</div>
                        <div class="d-flex justify-content-between align-items-end gap-2">
                            <strong class="fs-5">{{ $formatMetric($key, $m['real']) }}</strong>
                            <span class="badge bg-{{ $badge }}">{{ $m['cumplimiento_esperado'] }}%</span>
                        </div>
                        <div class="small text-muted mt-2">Esperado: {{ $formatMetric($key, $m['esperado']) }}</div>
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
                <i class="bi bi-graph-up me-2 text-success"></i>Ventas mensuales {{ $ano }}
            </div>
            <div class="card-body">
                <canvas id="chartVentasAdminStats" style="max-height:320px"></canvas>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header py-3">
                <i class="bi bi-person-lines-fill me-2 text-warning"></i>Metricas por mes {{ $ano }}
            </div>
            <div class="card-body">
                <canvas id="chartMetricasAdminStats" style="max-height:320px"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-bullseye me-2 text-warning"></i>
        Control de metas - {{ $meses[$mes - 1] }} {{ $ano }}
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Vendedor</th>
                        <th class="text-center">Ventas</th>
                        <th class="text-center">Atendidos</th>
                        <th class="text-center">Visitados</th>
                        <th class="text-center">Nuevos</th>
                        <th class="text-center">Global</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comparacion as $r)
                    <tr>
                        <td class="fw-semibold">{{ $r['v']->name }}</td>
                        <td class="text-center"><span class="badge bg-{{ $color($r['pv']) }}">{{ $r['pv'] }}%</span></td>
                        <td class="text-center"><span class="badge bg-{{ $color($r['pca']) }}">{{ $r['pca'] }}%</span></td>
                        <td class="text-center"><span class="badge bg-{{ $color($r['pcv']) }}">{{ $r['pcv'] }}%</span></td>
                        <td class="text-center"><span class="badge bg-{{ $color($r['pnc']) }}">{{ $r['pnc'] }}%</span></td>
                        <td class="text-center"><span class="badge bg-{{ $color($r['global']) }} fs-6">{{ $r['global'] }}%</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Sin datos.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labelsAdminStats = @json($mesesLabels);
const seriesAdminStats = @json($series);

new Chart(document.getElementById('chartVentasAdminStats'), {
    type: 'bar',
    data: {
        labels: labelsAdminStats,
        datasets: [{
            label: 'Ventas',
            data: seriesAdminStats.ventas,
            backgroundColor: 'rgba(37, 99, 235, .72)',
            borderRadius: 6,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true }, x: { grid: { display: false } } } }
});

new Chart(document.getElementById('chartMetricasAdminStats'), {
    type: 'line',
    data: {
        labels: labelsAdminStats,
        datasets: [
            { label: 'Atendidos', data: seriesAdminStats.clientes_atendidos, borderColor: '#16a34a', tension: .25 },
            { label: 'Visitados', data: seriesAdminStats.clientes_visitados, borderColor: '#f59e0b', tension: .25 },
            { label: 'Nuevos', data: seriesAdminStats.nuevos_clientes, borderColor: '#dc2626', tension: .25 },
        ]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true }, x: { grid: { display: false } } } }
});
</script>
@endsection
