@extends('layouts.app')
@section('titulo', 'Gestión de Metas')
@section('contenido')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-bullseye me-2 text-success"></i>Metas Asignadas</h5>
    <a href="{{ route('admin.metas.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle me-1"></i> Asignar Meta
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Vendedor</th>
                        <th>Período</th>
                        <th class="text-end">Ventas Meta</th>
                        <th class="text-end">Cl. Atendidos</th>
                        <th class="text-end">Cl. Visitados</th>
                        <th class="text-end">Nuevos Cl.</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($metas as $meta)
                    <tr>
                        <td class="fw-semibold">{{ $meta->vendedor->name ?? '—' }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary">
                                {{ \App\Models\Meta::nombreMes($meta->mes) }} {{ $meta->año }}
                            </span>
                        </td>
                        <td class="text-end">${{ number_format($meta->ventas_meta, 0, ',', '.') }}</td>
                        <td class="text-end">{{ $meta->clientes_atendidos_meta }}</td>
                        <td class="text-end">{{ $meta->clientes_visitados_meta }}</td>
                        <td class="text-end">{{ $meta->nuevos_clientes_meta }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.metas.edit', $meta) }}"
                               class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST"
                                  action="{{ route('admin.metas.destroy', $meta) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('¿Eliminar esta meta?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No hay metas asignadas aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection