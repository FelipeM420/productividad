@extends('layouts.app')
@section('titulo', 'Dashboard Auditor')
@section('contenido')

@php
    $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
              'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $color = fn($p) => $p>=100 ? 'success' : ($p>=60 ? 'warning' : 'danger');
@endphp

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#4f46e5,#818cf8)">
            <div class="icon"><i class="bi bi-people-fill"></i></div>
            <div class="label">Vendedores</div>
            <div class="value">{{ $totalVendedores }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#059669,#34d399)">
            <div class="icon"><i class="bi bi-calendar-check"></i></div>
            <div class="label">Actividades Este Mes</div>
            <div class="value">{{ $totalActividades }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#d97706,#fbbf24)">
            <div class="icon"><i class="bi bi-bullseye"></i></div>
            <div class="label">Metas Asignadas</div>
            <div class="value">{{ $totalMetas }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0891b2,#22d3ee)">
            <div class="icon"><i class="bi bi-currency-dollar"></i></div>
            <div class="label">Ventas Totales Mes</div>
            <div class="value" style="font-size:1.3rem">
                ${{ number_format($ventasTotales,0,',','.') }}
            </div>
        </div>
    </div>
</div>

{{-- Resumen cumplimiento --}}
<div class="card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-bar-chart-line me-2 text-primary"></i>
            Cumplimiento — {{ $meses[$mes] }} {{ $año }}
        </span>
        <a href="{{ route('auditor.reportes.index') }}" class="btn btn-sm btn-outline-primary">
            Ver reporte completo
        </a>
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
                    <th class="text-center">Global</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resumen as $r)
                <tr>
                    <td class="fw-semibold">{{ $r['v']->name }}</td>
                    <td class="text-center">
                        <span class="badge bg-{{ $color($r['pv']) }}-subtle text-{{ $color($r['pv']) }}">
                            {{ $r['pv'] }}%
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $color($r['pca']) }}-subtle text-{{ $color($r['pca']) }}">
                            {{ $r['pca'] }}%
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $color($r['pcv']) }}-subtle text-{{ $color($r['pcv']) }}">
                            {{ $r['pcv'] }}%
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $color($r['pnc']) }}-subtle text-{{ $color($r['pnc']) }}">
                            {{ $r['pnc'] }}%
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-{{ $color($r['global']) }} fs-6">{{ $r['global'] }}%</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Sin datos este mes.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection