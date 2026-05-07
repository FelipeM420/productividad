@extends('layouts.app')
@section('titulo', 'Nuevo Usuario')
@section('contenido')

<div class="row justify-content-center">
<div class="col-lg-7">

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="fw-bold mb-0">Crear Nuevo Usuario</h5>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.usuarios.store') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre completo</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" placeholder="Ej: Carlos Rodríguez" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Correo electrónico</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="correo@empresa.com" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Rol</label>
                <select name="rol" class="form-select @error('rol') is-invalid @enderror" required>
                    <option value="">-- Selecciona un rol --</option>
                    <option value="admin"    {{ old('rol')=='admin'    ? 'selected':'' }}>Administrador</option>
                    <option value="vendedor" {{ old('rol')=='vendedor' ? 'selected':'' }}>Vendedor</option>
                    <option value="auditor"  {{ old('rol')=='auditor'  ? 'selected':'' }}>Auditor</option>
                </select>
                @error('rol')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Contraseña</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Mínimo 6 caracteres" required>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation"
                           class="form-control" placeholder="Repite la contraseña" required>
                </div>
            </div>

            <div class="d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Crear Usuario
                </button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-outline-secondary px-4">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

</div>
</div>
@endsection