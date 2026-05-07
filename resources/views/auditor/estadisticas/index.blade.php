@extends('layouts.app')
@section('titulo', 'Estadísticas')
@section('contenido')

@php
    $mesesLabels = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    $color = fn($p) => $p>=100?'success':($p>=60?'warning':'danger');
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Estadísticas</h5>
    <form method="GET" class="d-flex gap-2">
        <select name="año" class="form-select form-select-sm" style="width:100px">
            @for($y=date('Y')-1; $y<=date('Y')+1; $y++)
                <option value="{{ $y }}" {{ $año==$y?'selected':'' }}>{{ $y }}</option>
            @endfor
        </select>
        <button class="btn btn-sm btn-primary">Aplicar</button>
    </form>
</div>

{{-- Gráfica de ventas mensuales --}}
<div class="card mb-4">
    <div class="card-header py-3">
        <i class="bi bi-graph-up me-2 text-success"></i>
        Ventas Mensuales {{ $año }}
    </div>
    <div class="card-body">
        <canvas id="chartVentas" style="max-height:320px"></canvas>
    </div>
</div>

{{-- Comparación metas vs real --}}
<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-bullseye me-2 text-warning"></i>
        Metas vs Resultados — {{ now()->format('F Y') }}
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Vendedor</th>
                    <th class="text-center">Ventas</th>
                    <th class="text-center">Cl. Atendidos</th>
                    <th class="text-center">Cl. Visitados</th>
                    <th class="text-center">Nuevos Cl.</th>
                    <th class="text-center">Cumplimiento Global</th>
                </tr>
            </thead>
            <tbody>
                @forelse($comparacion as $r)
                <tr>
                    <td class="fw-semibold">{{ $r['v']->name }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-fill" style="height:8px">
                                <div class="progress-bar bg-{{ $color($r['pv']) }}"
                                     style="width:{{ $r['pv'] }}%"></div>
                            </div>
                            <small class="text-{{ $color($r['pv']) }}" style="width:38px">
                                {{ $r['pv'] }}%
                            </small>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-fill" style="height:8px">
                                <div class="progress-bar bg-{{ $color($r['pca']) }}"
                                     style="width:{{ $r['pca'] }}%"></div>
                            </div>
                            <small class="text-{{ $color($r['pca']) }}" style="width:38px">
                                {{ $r['pca'] }}%
                            </small>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-fill" style="height:8px">
                                <div class="progress-bar bg-{{ $color($r['pcv']) }}"
                                     style="width:{{ $r['pcv'] }}%"></div>
                            </div>
                            <small class="text-{{ $color($r['pcv']) }}" style="width:38px">
                                {{ $r['pcv'] }}%
                            </small>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-fill" style="height:8px">
                                <div class="progress-bar bg-{{ $color($r['pnc']) }}"
                                     style="width:{{ $r['pnc'] }}%"></div>
                            </div>
                            <small class="text-{{ $color($r['pnc']) }}" style="width:38px">
                                {{ $r['pnc'] }}%
                            </small>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $color($r['global']) }} fs-6">
                            {{ $r['global'] }}%
                        </span>
                    </td>
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

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('chartVentas').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($mesesLabels),
        datasets: [{
            label: 'Ventas ($)',
            data: @json($ventasPorMes),
            backgroundColor: 'rgba(79,70,229,0.75)',
            borderColor: '#4f46e5',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => '$' + ctx.parsed.y.toLocaleString('es-CO')
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: v => '$' + Number(v).toLocaleString('es-CO')
                },
                grid: { color: '#f1f5f9' }
            },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endsection