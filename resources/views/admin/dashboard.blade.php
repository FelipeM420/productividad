@extends('layouts.app')
@section('titulo', 'Dashboard')
@section('contenido')

{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#4f46e5,#818cf8)">
            <div class="icon"><i class="bi bi-people-fill"></i></div>
            <div class="label">Total Usuarios</div>
            <div class="value">{{ $totalUsuarios }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#059669,#34d399)">
            <div class="icon"><i class="bi bi-person-badge"></i></div>
            <div class="label">Vendedores Activos</div>
            <div class="value">{{ $totalVendedores }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#d97706,#fbbf24)">
            <div class="icon"><i class="bi bi-bullseye"></i></div>
            <div class="label">Metas Este Mes</div>
            <div class="value">{{ $totalMetas }}</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#db2777,#f472b6)">
            <div class="icon"><i class="bi bi-calendar-check"></i></div>
            <div class="label">Actividades Este Mes</div>
            <div class="value">{{ $totalActiv }}</div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Top Vendedores --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header py-3">
                <i class="bi bi-trophy-fill text-warning me-2"></i>
                Top Vendedores — {{ now()->translatedFormat('F Y') }}
            </div>
            <div class="card-body">
                @forelse($topVendedores as $v)
                    @php $color = $v['pct']>=100 ? 'success' : ($v['pct']>=60 ? 'warning' : 'danger') @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fw-semibold" style="font-size:.85rem">{{ $v['nombre'] }}</span>
                            <span class="text-muted" style="font-size:.8rem">
                                ${{ number_format($v['ventas'],0,',','.') }} /
                                ${{ number_format($v['meta'],0,',','.') }}
                            </span>
                        </div>
                        <div class="progress" style="height:8px">
                            <div class="progress-bar bg-{{ $color }}" style="width:{{ $v['pct'] }}%"></div>
                        </div>
                        <small class="text-{{ $color }}">{{ $v['pct'] }}% cumplimiento</small>
                    </div>
                @empty
                    <p class="text-muted text-center py-3">Sin datos este mes.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Últimas Actividades --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2 text-primary"></i>Últimas Actividades</span>
                <a href="{{ route('admin.reportes.index') }}" class="btn btn-sm btn-outline-primary">
                    Ver reportes
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Vendedor</th>
                            <th>Fecha</th>
                            <th class="text-end">Ventas</th>
                            <th class="text-end">Cl. Atendidos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ultimasActividades as $a)
                        <tr>
                            <td>{{ $a->vendedor->name ?? '—' }}</td>
                            <td>{{ $a->fecha->format('d/m/Y') }}</td>
                            <td class="text-end fw-semibold">
                                ${{ number_format($a->ventas,0,',','.') }}
                            </td>
                            <td class="text-end">{{ $a->clientes_atendidos }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Sin actividades registradas.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Accesos rápidos --}}
<div class="card mt-3">
    <div class="card-body d-flex flex-wrap gap-2">
        <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Nuevo Usuario
        </a>
        <a href="{{ route('admin.metas.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> Asignar Meta
        </a>
        <a href="{{ route('admin.reportes.index') }}" class="btn btn-warning text-dark">
            <i class="bi bi-file-earmark-bar-graph me-1"></i> Ver Reportes
        </a>
    </div>
</div>

@endsection