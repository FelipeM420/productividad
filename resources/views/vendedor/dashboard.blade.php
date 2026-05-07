@extends('layouts.app')
@section('titulo', 'Mi Dashboard')
@section('contenido')

@php
    $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
              'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $color = fn($p) => $p >= 100 ? 'success' : ($p >= 60 ? 'warning' : 'danger');
@endphp

{{-- Alerta registro hoy --}}
@if(!$registroHoy)
    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>
            <strong>¡Aún no registraste tu actividad de hoy!</strong>
            <a href="{{ route('vendedor.actividades.create') }}" class="btn btn-warning btn-sm ms-2">
                <i class="bi bi-plus-circle me-1"></i> Registrar ahora
            </a>
        </div>
    </div>
@else
    <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>✅ Ya registraste tu actividad de hoy.
            <a href="{{ route('vendedor.actividades.index') }}" class="ms-2 small">Ver mis actividades →</a>
        </div>
    </div>
@endif

{{-- Encabezado período --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="fw-bold text-muted mb-0">
        <i class="bi bi-calendar3 me-1"></i>
        Cumplimiento — {{ $meses[$mes] }} {{ $año }}
    </h6>
    @if(!$meta)
        <span class="badge bg-warning text-dark">Sin meta asignada este mes</span>
    @endif
</div>

{{-- Cards de cumplimiento --}}
<div class="row g-3 mb-4">
    @php
        $cards = [
            ['label'=>'Ventas','icon'=>'currency-dollar','real'=>'$'.number_format($totales->total_ventas??0,0,',','.'),'meta'=>'$'.number_format($meta?->ventas_meta??0,0,',','.'),'pct'=>$pcts['ventas'],'grad'=>'#4f46e5,#818cf8'],
            ['label'=>'Cl. Atendidos','icon'=>'person-check','real'=>$totales->total_ca??0,'meta'=>$meta?->clientes_atendidos_meta??0,'pct'=>$pcts['ca'],'grad'=>'#059669,#34d399'],
            ['label'=>'Cl. Visitados','icon'=>'geo-alt','real'=>$totales->total_cv??0,'meta'=>$meta?->clientes_visitados_meta??0,'pct'=>$pcts['cv'],'grad'=>'#d97706,#fbbf24'],
            ['label'=>'Nuevos Clientes','icon'=>'person-plus','real'=>$totales->total_nc??0,'meta'=>$meta?->nuevos_clientes_meta??0,'pct'=>$pcts['nc'],'grad'=>'#db2777,#f472b6'],
        ];
    @endphp

    @foreach($cards as $c)
    <div class="col-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,{{ $c['grad'] }})">
            <div class="icon"><i class="bi bi-{{ $c['icon'] }}"></i></div>
            <div class="label">{{ $c['label'] }}</div>
            <div class="value">{{ $c['real'] }}</div>
            <div class="mt-2" style="font-size:.78rem;opacity:.85">
                Meta: {{ $c['meta'] }}
            </div>
            <div class="progress mt-1" style="height:5px;background:rgba(255,255,255,.3)">
                <div class="progress-bar bg-white" style="width:{{ $c['pct'] }}%"></div>
            </div>
            <small>{{ $c['pct'] }}% cumplido</small>
        </div>
    </div>
    @endforeach
</div>

{{-- Últimas actividades --}}
<div class="card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-clock-history me-2 text-primary"></i>
            Últimas Actividades
        </span>
        <a href="{{ route('vendedor.actividades.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Nueva
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
                    <th class="text-end">Cl. Visitados</th>
                    <th class="text-end">Nuevos Cl.</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse($actividades as $a)
                <tr>
                    <td>
                        {{-- Resalta si es el propio usuario --}}
                        @if((int)$a->id_usuario === (int)Auth::id())
                            <span class="fw-bold text-primary">
                                <i class="bi bi-person-fill me-1"></i>{{ $a->vendedor->name ?? '—' }}
                            </span>
                        @else
                            <span class="text-muted">{{ $a->vendedor->name ?? '—' }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $a->fecha->format('d/m/Y') }}
                        @if($a->fecha->isToday())
                            <span class="badge bg-primary ms-1" style="font-size:.65rem">Hoy</span>
                        @endif
                    </td>
                    <td class="text-end fw-semibold">
                        ${{ number_format($a->ventas, 0, ',', '.') }}
                    </td>
                    <td class="text-end">{{ $a->clientes_atendidos }}</td>
                    <td class="text-end">{{ $a->clientes_visitados }}</td>
                    <td class="text-end">{{ $a->nuevos_clientes }}</td>
                    <td class="text-center">
                        {{-- Solo muestra editar si es actividad propia --}}
                        @if((int)$a->id_usuario === (int)Auth::id())
                            <a href="{{ route('vendedor.actividades.edit', $a) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        No hay actividades registradas aún.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection