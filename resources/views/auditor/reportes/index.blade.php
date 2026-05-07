@extends('layouts.app')
@section('titulo', 'Reportes — Auditor')
@section('contenido')

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('auditor.reportes.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Mes</label>
                <select name="mes" class="form-select">
                    @foreach($meses as $i => $m)
                        <option value="{{ $i+1 }}" {{ $mes==($i+1)?'selected':'' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Año</label>
                <select name="año" class="form-select">
                    @for($y=date('Y')-1; $y<=date('Y')+1; $y++)
                        <option value="{{ $y }}" {{ $año==$y?'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Vendedor</label>
                <select name="vendedor" class="form-select">
                    <option value="">Todos</option>
                    @foreach($vendedores as $v)
                        <option value="{{ $v->id }}" {{ $vendedorId==$v->id?'selected':'' }}>
                            {{ $v->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary flex-fill">
                    <i class="bi bi-search me-1"></i> Filtrar
                </button>
                <a href="{{ route('auditor.reportes.pdf', request()->query()) }}"
                   class="btn btn-danger flex-fill">
                    <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabla --}}
<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>
        Cumplimiento — {{ $meses[$mes-1] }} {{ $año }}
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th rowspan="2" class="align-middle">Vendedor</th>
                        <th colspan="3" class="text-center border-start">Ventas</th>
                        <th colspan="3" class="text-center border-start">Cl. Atendidos</th>
                        <th colspan="3" class="text-center border-start">Cl. Visitados</th>
                        <th colspan="3" class="text-center border-start">Nuevos Cl.</th>
                        <th rowspan="2" class="text-center align-middle">Global</th>
                    </tr>
                    <tr class="table-light" style="font-size:.73rem">
                        <th class="text-end border-start text-muted">Real</th>
                        <th class="text-end text-muted">Meta</th>
                        <th class="text-center text-muted">%</th>
                        <th class="text-end border-start text-muted">Real</th>
                        <th class="text-end text-muted">Meta</th>
                        <th class="text-center text-muted">%</th>
                        <th class="text-end border-start text-muted">Real</th>
                        <th class="text-end text-muted">Meta</th>
                        <th class="text-center text-muted">%</th>
                        <th class="text-end border-start text-muted">Real</th>
                        <th class="text-end text-muted">Meta</th>
                        <th class="text-center text-muted">%</th>
                    </tr>
                </thead>
                <tbody>
                    @php $color = fn($p) => $p>=100?'success':($p>=60?'warning':'danger'); @endphp
                    @forelse($reporte as $r)
                    <tr>
                        <td class="fw-semibold">{{ $r['v']->name }}</td>

                        <td class="text-end border-start">${{ number_format($r['rv'],0,',','.') }}</td>
                        <td class="text-end text-muted">${{ number_format($r['mv'],0,',','.') }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $color($r['pv']) }}">{{ $r['pv'] }}%</span>
                        </td>

                        <td class="text-end border-start">{{ $r['rca'] }}</td>
                        <td class="text-end text-muted">{{ $r['mca'] }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $color($r['pca']) }}">{{ $r['pca'] }}%</span>
                        </td>

                        <td class="text-end border-start">{{ $r['rcv'] }}</td>
                        <td class="text-end text-muted">{{ $r['mcv'] }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $color($r['pcv']) }}">{{ $r['pcv'] }}%</span>
                        </td>

                        <td class="text-end border-start">{{ $r['rnc'] }}</td>
                        <td class="text-end text-muted">{{ $r['mnc'] }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $color($r['pnc']) }}">{{ $r['pnc'] }}%</span>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-{{ $color($r['global']) }} fs-6">{{ $r['global'] }}%</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="15" class="text-center text-muted py-4">
                            Sin datos para el período seleccionado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection