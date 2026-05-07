@extends('layouts.app')
@section('titulo', 'Editar Actividad')
@section('contenido')

<div class="row justify-content-center">
<div class="col-lg-6">

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('vendedor.actividades.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0">Editar Actividad</h5>
        <small class="text-muted">{{ $actividad->fecha->format('l, d \d\e F \d\e Y') }}</small>
    </div>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('vendedor.actividades.update', $actividad) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="bi bi-currency-dollar text-success me-1"></i>
                    Ventas realizadas ($)
                </label>
                <input type="number" name="ventas" step="1000" min="0"
                       class="form-control form-control-lg @error('ventas') is-invalid @enderror"
                       value="{{ old('ventas', $actividad->ventas) }}" required>
                @error('ventas')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cl. Atendidos</label>
                    <input type="number" name="clientes_atendidos" min="0"
                           class="form-control"
                           value="{{ old('clientes_atendidos', $actividad->clientes_atendidos) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Cl. Visitados</label>
                    <input type="number" name="clientes_visitados" min="0"
                           class="form-control"
                           value="{{ old('clientes_visitados', $actividad->clientes_visitados) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nuevos Cl.</label>
                    <input type="number" name="nuevos_clientes" min="0"
                           class="form-control"
                           value="{{ old('nuevos_clientes', $actividad->nuevos_clientes) }}" required>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-check-lg me-1"></i> Guardar Cambios
                </button>
                <a href="{{ route('vendedor.actividades.index') }}" class="btn btn-outline-secondary px-4">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

</div>
</div>
@endsection