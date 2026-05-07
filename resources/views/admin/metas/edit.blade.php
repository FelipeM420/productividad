@extends('layouts.app')
@section('titulo', 'Editar Meta')
@section('contenido')

<div class="row justify-content-center">
<div class="col-lg-7">

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.metas.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="fw-bold mb-0">Editar Meta</h5>
</div>

<div class="card">
    <div class="card-body p-4">

        <div class="alert alert-info py-2 mb-4" style="font-size:.875rem">
            <i class="bi bi-person me-1"></i>
            <strong>{{ $meta->vendedor->name }}</strong> —
            {{ \App\Models\Meta::nombreMes($meta->mes) }} {{ $meta->año }}
        </div>

        <form method="POST" action="{{ route('admin.metas.update', $meta) }}">
            @csrf @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Meta de Ventas ($)</label>
                    <input type="number" name="ventas_meta" step="1000" min="0"
                           class="form-control @error('ventas_meta') is-invalid @enderror"
                           value="{{ old('ventas_meta', $meta->ventas_meta) }}" required>
                    @error('ventas_meta')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Clientes Atendidos</label>
                    <input type="number" name="clientes_atendidos_meta" min="0"
                           class="form-control"
                           value="{{ old('clientes_atendidos_meta', $meta->clientes_atendidos_meta) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Clientes Visitados</label>
                    <input type="number" name="clientes_visitados_meta" min="0"
                           class="form-control"
                           value="{{ old('clientes_visitados_meta', $meta->clientes_visitados_meta) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nuevos Clientes</label>
                    <input type="number" name="nuevos_clientes_meta" min="0"
                           class="form-control"
                           value="{{ old('nuevos_clientes_meta', $meta->nuevos_clientes_meta) }}" required>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Guardar Cambios
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