@extends('layouts.app')
@section('titulo', 'Mis Actividades')
@section('contenido')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">
        <i class="bi bi-calendar-check me-2 text-primary"></i>Mis Actividades
    </h5>
    <a href="{{ route('vendedor.actividades.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Registrar Hoy
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th class="text-end">Ventas ($)</th>
                        <th class="text-end">Cl. Atendidos</th>
                        <th class="text-end">Cl. Visitados</th>
                        <th class="text-end">Nuevos Cl.</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($actividades as $a)
                    <tr>
                        <td class="fw-semibold">
                            {{ $a->fecha->format('d/m/Y') }}
                            @if($a->fecha->isToday())
                                <span class="badge bg-primary ms-1" style="font-size:.65rem">Hoy</span>
                            @endif
                        </td>
                        <td class="text-end">${{ number_format($a->ventas,0,',','.') }}</td>
                        <td class="text-end">{{ $a->clientes_atendidos }}</td>
                        <td class="text-end">{{ $a->clientes_visitados }}</td>
                        <td class="text-end">{{ $a->nuevos_clientes }}</td>
                        <td class="text-center">
                            <a href="{{ route('vendedor.actividades.edit', $a) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No hay actividades registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $actividades->links() }}
        </div>
    </div>
</div>

@endsection