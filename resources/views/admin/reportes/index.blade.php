@extends('layouts.app')
@section('titulo', 'Reportes de Cumplimiento')
@section('contenido')

{{-- Filtros --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reportes.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Mes</label>
                <select name="mes" class="form-select">
                    @foreach($meses as $i => $m)
                        <option value="{{ $i+1 }}" {{ $mes==($i+1) ? 'selected':'' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Año</label>
                <select name="año" class="form-select">
                    @for($y=date('Y')-1; $y<=date('Y')+1; $y++)
                        <option value="{{ $y }}" {{ $año==$y ? 'selected':'' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Vendedor</label>
                <select name="vendedor" class="form-select">
                    <option value="">Todos</option>
                    @foreach($vendedores as $v)
                        <option value="{{ $v->id }}" {{ $vendedorId==$v->id ? 'selected':'' }}>
                            {{ $v->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Tabla de cumplimiento --}}
<div class="card">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>
            Cumplimiento — {{ $meses[$mes-1] }} {{ $año }}
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Vendedor</th>
                        <th colspan="2" class="text-center">Ventas</th>
                        <th colspan="2" class="text-center">Cl. Atendidos</th>
                        <th colspan="2" class="text-center">Cl. Visitados</th>
                        <th colspan="2" class="text-center">Nuevos Cl.</th>
                        <th class="text-center">% Global</th>
                    </tr>
                    <tr class="table-light" style="font-size:.75rem">
                        <th></th>
                        <th class="text-end text-muted">Real</th><th class="text-end text-muted">Meta</th>
                        <th class="text-end text-muted">Real</th><th class="text-end text-muted">Meta</th>
                        <th class="text-end text-muted">Real</th><th class="text-end text-muted">Meta</th>
                        <th class="text-end text-muted">Real</th><th class="text-end text-muted">Meta</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reporte as $r)
                    @php
                        $pcts = [];
                        foreach([['ventas_real','ventas_meta'],['ca_real','ca_meta'],
                                 ['cv_real','cv_meta'],['nc_real','nc_meta']] as [$real,$meta])
                            $pcts[] = $r[$meta]>0 ? min(100,round($r[$real]/$r[$meta]*100)) : 0;
                        $global = (int)round(array_sum($pcts)/count($pcts));
                        $color  = $global>=100 ? 'success' : ($global>=60 ? 'warning' : 'danger');
                    @endphp
                    <tr>
                        <td class="fw-semibold">{{ $r['nombre'] }}</td>
                        <td class="text-end">${{ number_format($r['ventas_real'],0,',','.') }}</td>
                        <td class="text-end text-muted">${{ number_format($r['ventas_meta'],0,',','.') }}</td>
                        <td class="text-end">{{ $r['ca_real'] }}</td>
                        <td class="text-end text-muted">{{ $r['ca_meta'] }}</td>
                        <td class="text-end">{{ $r['cv_real'] }}</td>
                        <td class="text-end text-muted">{{ $r['cv_meta'] }}</td>
                        <td class="text-end">{{ $r['nc_real'] }}</td>
                        <td class="text-end text-muted">{{ $r['nc_meta'] }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $color }} fs-6">{{ $global }}%</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">
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