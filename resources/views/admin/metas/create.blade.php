@extends('layouts.app')
@section('titulo', 'Asignar Meta')
@section('contenido')

<div class="row justify-content-center">
<div class="col-lg-7">

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.metas.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="fw-bold mb-0">Asignar Meta a Vendedor</h5>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.metas.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Vendedor</label>
                <select name="id_usuario" class="form-select @error('id_usuario') is-invalid @enderror" required>
                    <option value="">-- Selecciona un vendedor --</option>
                    @foreach($vendedores as $v)
                        <option value="{{ $v->id }}" {{ old('id_usuario')==$v->id ? 'selected':'' }}>
                            {{ $v->name }}
                        </option>
                    @endforeach
                </select>
                @error('id_usuario')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Mes</label>
                    <select name="mes" class="form-select @error('mes') is-invalid @enderror" required>
                        <option value="">-- Mes --</option>
                        @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                                  'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $m)
                            <option value="{{ $i+1 }}" {{ old('mes')==($i+1) ? 'selected':'' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                    @error('mes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Año</label>
                    <select name="año" class="form-select @error('año') is-invalid @enderror" required>
                        @for($y = date('Y'); $y <= date('Y')+2; $y++)
                            <option value="{{ $y }}" {{ old('año', date('Y'))==$y ? 'selected':'' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    @error('año')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <hr class="my-3">
            <p class="fw-semibold mb-3 text-muted"><i class="bi bi-bullseye me-1"></i> Metas del período</p>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Meta de Ventas ($)</label>
                    <input type="number" name="ventas_meta" step="1000" min="0"
                           class="form-control @error('ventas_meta') is-invalid @enderror"
                           value="{{ old('ventas_meta', 0) }}" required>
                    @error('ventas_meta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Clientes Atendidos</label>
                    <input type="number" name="clientes_atendidos_meta" min="0"
                           class="form-control @error('clientes_atendidos_meta') is-invalid @enderror"
                           value="{{ old('clientes_atendidos_meta', 0) }}" required>
                    @error('clientes_atendidos_meta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Clientes Visitados</label>
                    <input type="number" name="clientes_visitados_meta" min="0"
                           class="form-control @error('clientes_visitados_meta') is-invalid @enderror"
                           value="{{ old('clientes_visitados_meta', 0) }}" required>
                    @error('clientes_visitados_meta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nuevos Clientes</label>
                    <input type="number" name="nuevos_clientes_meta" min="0"
                           class="form-control @error('nuevos_clientes_meta') is-invalid @enderror"
                           value="{{ old('nuevos_clientes_meta', 0) }}" required>
                    @error('nuevos_clientes_meta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-success px-4">
                    <i class="bi bi-check-lg me-1"></i> Asignar Meta
                </button>
                <a href="{{ route('admin.metas.index') }}" class="btn btn-outline-secondary px-4">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

</div>
</div>
@endsection