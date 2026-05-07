@extends('layouts.app')
@section('titulo', 'Gestión de Usuarios')
@section('contenido')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>Usuarios del Sistema</h5>
    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Nuevo Usuario
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $usuario)
                    <tr>
                        <td class="text-muted">{{ $usuario->id }}</td>
                        <td class="fw-semibold">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;border-radius:50%;background:#4f46e5;
                                            color:#fff;display:flex;align-items:center;
                                            justify-content:center;font-weight:700;font-size:.8rem;flex-shrink:0">
                                    {{ strtoupper(substr($usuario->name,0,1)) }}
                                </div>
                                {{ $usuario->name }}
                            </div>
                        </td>
                        <td class="text-muted">{{ $usuario->email }}</td>
                        <td>
                            @php
                                $colores = ['admin'=>'danger','vendedor'=>'success','auditor'=>'warning'];
                                $labels  = ['admin'=>'Administrador','vendedor'=>'Vendedor','auditor'=>'Auditor'];
                            @endphp
                            <span class="badge bg-{{ $colores[$usuario->rol] ?? 'secondary' }}">
                                {{ $labels[$usuario->rol] ?? $usuario->rol }}
                            </span>
                        </td>
                        <td>
                            @if($usuario->activo)
                                <span class="badge bg-success-subtle text-success">Activo</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.usuarios.edit', $usuario) }}"
                               class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($usuario->id !== Auth()->id())
                            <form method="POST"
                                  action="{{ route('admin.usuarios.destroy', $usuario) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('¿Eliminar a {{ $usuario->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No hay usuarios registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection