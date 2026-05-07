@extends('layouts.app')
@section('titulo', 'Editar Usuario')
@section('contenido')

<div class="row justify-content-center">
<div class="col-lg-7">

<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h5 class="fw-bold mb-0">Editar Usuario</h5>
</div>

<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}">
            @csrf @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Nombre completo</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $usuario->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Correo electrónico</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $usuario->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Rol</label>
                    <select name="rol" class="form-select @error('rol') is-invalid @enderror" required>
                        <option value="admin"    {{ old('rol',$usuario->rol)=='admin'    ? 'selected':'' }}>Administrador</option>
                        <option value="vendedor" {{ old('rol',$usuario->rol)=='vendedor' ? 'selected':'' }}>Vendedor</option>
                        <option value="auditor"  {{ old('rol',$usuario->rol)=='auditor'  ? 'selected':'' }}>Auditor</option>
                    </select>
                    @error('rol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Estado</label>
                    <select name="activo" class="form-select">
                        <option value="1" {{ old('activo', $usuario->activo) ? 'selected':'' }}>Activo</option>
                        <option value="0" {{ !old('activo', $usuario->activo) ? 'selected':'' }}>Inactivo</option>
                    </select>
                </div>
            </div>

            <hr class="my-3">
            <p class="text-muted small mb-2">
                <i class="bi bi-info-circle me-1"></i>
                Deja en blanco para mantener la contraseña actual.
            </p>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Nueva contraseña</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Mínimo 6 caracteres">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>
            </div>

            <div class="d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> Guardar Cambios
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