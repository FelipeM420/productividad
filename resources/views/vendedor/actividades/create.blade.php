@extends('layouts.app')
@section('titulo', 'Registrar Actividad')
@section('contenido')

<div class="row justify-content-center">
<div class="col-lg-6">

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('vendedor.actividades.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h5 class="fw-bold mb-0">Registrar Actividad del Día</h5>
        <small class="text-muted">{{ now()->format('l, d \d\e F \d\e Y') }}</small>
    </div>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('vendedor.actividades.store') }}">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="bi bi-currency-dollar text-success me-1"></i>
                    Ventas realizadas ($)
                </label>
                <input type="number" name="ventas" step="1000" min="0"
                       class="form-control form-control-lg @error('ventas') is-invalid @enderror"
                       value="{{ old('ventas', 0) }}"
                       placeholder="0" required>
                @error('ventas')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-person-check text-primary me-1"></i>
                        Cl. Atendidos
                    </label>
                    <input type="number" name="clientes_atendidos" min="0"
                           class="form-control @error('clientes_atendidos') is-invalid @enderror"
                           value="{{ old('clientes_atendidos', 0) }}" required>
                    @error('clientes_atendidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-geo-alt text-warning me-1"></i>
                        Cl. Visitados
                    </label>
                    <input type="number" name="clientes_visitados" min="0"
                           class="form-control @error('clientes_visitados') is-invalid @enderror"
                           value="{{ old('clientes_visitados', 0) }}" required>
                    @error('clientes_visitados')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="bi bi-person-plus text-danger me-1"></i>
                        Nuevos Cl.
                    </label>
                    <input type="number" name="nuevos_clientes" min="0"
                           class="form-control @error('nuevos_clientes') is-invalid @enderror"
                           value="{{ old('nuevos_clientes', 0) }}" required>
                    @error('nuevos_clientes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                <i class="bi bi-check-circle me-2"></i> Guardar Actividad
            </button>
        </form>
    </div>
</div>

</div>
</div>
@endsection