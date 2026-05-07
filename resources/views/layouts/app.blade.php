<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'SMP') — Productividad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; }

        /* Sidebar */
        #sidebar {
            width: 250px; min-height: 100vh;
            background: #1e1b4b; position: fixed;
            top: 0; left: 0; z-index: 1040;
            display: flex; flex-direction: column;
        }
        .sidebar-brand {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            color: #fff; font-weight: 700; font-size: 1rem;
        }
        .sidebar-brand small { color: #a5b4fc; font-size: .72rem; display: block; }
        .sidebar-section {
            padding: .6rem 1rem .2rem;
            font-size: .68rem; color: #6366f1;
            text-transform: uppercase; letter-spacing: .08em; font-weight: 600;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: .6rem;
            color: #c7d2fe; border-radius: 8px;
            margin: 2px 10px; padding: .5rem .85rem;
            font-size: .875rem; text-decoration: none;
            transition: background .15s, color .15s;
        }
        .sidebar-link:hover  { background: rgba(255,255,255,.08); color: #fff; }
        .sidebar-link.active { background: rgba(79,70,229,.45);   color: #fff; font-weight: 600; }
        .sidebar-footer {
            margin-top: auto; padding: 1rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: #4f46e5; color: #fff;
            display: flex; align-items: center;
            justify-content: center; font-weight: 700;
            font-size: .9rem; flex-shrink: 0;
        }

        /* Contenido */
        #content { margin-left: 250px; min-height: 100vh; }
        .topbar {
            background: #fff; border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.5rem;
            display: flex; align-items: center;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 100;
        }
        .main-content { padding: 1.75rem; }

        /* Cards de estadística */
        .stat-card {
            border: none; border-radius: 14px;
            padding: 1.25rem 1.5rem; color: #fff;
            box-shadow: 0 4px 18px rgba(0,0,0,.12);
        }
        .stat-card .icon {
            width: 48px; height: 48px; border-radius: 12px;
            background: rgba(255,255,255,.2);
            display: flex; align-items: center;
            justify-content: center; font-size: 1.4rem;
        }
        .stat-card .label { font-size: .8rem; opacity: .85; margin-top: .75rem; }
        .stat-card .value { font-size: 1.8rem; font-weight: 700; }

        /* Tabla */
        .table th { font-size: .78rem; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
        .card { border: none; border-radius: 14px; box-shadow: 0 1px 8px rgba(0,0,0,.06); }
        .card-header { background: transparent; border-bottom: 1px solid #f1f5f9; font-weight: 600; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); transition: transform .3s; }
            #sidebar.show { transform: translateX(0); }
            #content { margin-left: 0; }
        }
        @media print {
            #sidebar, .topbar, .no-print { display: none !important; }
            #content { margin: 0 !important; }
        }
    </style>
</head>
<body>

{{-- ═══ SIDEBAR ═══ --}}
<nav id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2">
            <div style="width:34px;height:34px;background:#4f46e5;border-radius:8px;
                        display:flex;align-items:center;justify-content:center">
                <i class="bi bi-graph-up-arrow text-white"></i>
            </div>
            <div>
                SMP
                <small>Sistema de Productividad</small>
            </div>
        </div>
    </div>

    <div class="pt-2">
        @auth
            @php
                $safeRoute = fn (string $name, string $fallback = '#') => Route::has($name) ? route($name) : $fallback;
            @endphp

            @if(Auth::user()->rol === 'admin')
                <div class="sidebar-section">General</div>
                <a href="{{ $safeRoute('admin.dashboard') }}"
                   class="sidebar-link {{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <div class="sidebar-section">Usuarios</div>
                <a href="{{ $safeRoute('admin.usuarios.index') }}"
                   class="sidebar-link {{ Request::routeIs('admin.usuarios.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Gestión de Usuarios
                </a>

                <div class="sidebar-section">Metas</div>
                <a href="{{ $safeRoute('admin.metas.index') }}"
                   class="sidebar-link {{ Request::routeIs('admin.metas.*') ? 'active' : '' }}">
                    <i class="bi bi-bullseye"></i> Gestión de Metas
                </a>

                <div class="sidebar-section">Reportes</div>
                <a href="{{ $safeRoute('admin.reportes.index') }}"
                   class="sidebar-link {{ Request::routeIs('admin.reportes.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Reportes
                </a>
                <a href="{{ $safeRoute('admin.estadisticas.index') }}"
                   class="sidebar-link {{ Request::routeIs('admin.estadisticas.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i> Estadisticas
                </a>

            @elseif(Auth::user()->rol === 'vendedor')
                <div class="sidebar-section">General</div>
                <a href="{{ $safeRoute('vendedor.dashboard') }}"
                   class="sidebar-link {{ Request::routeIs('vendedor.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <div class="sidebar-section">Actividades</div>
                <a href="{{ $safeRoute('vendedor.actividades.index') }}"
                   class="sidebar-link {{ Request::routeIs('vendedor.actividades.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i> Mis Actividades
                </a>
                <a href="{{ $safeRoute('vendedor.actividades.create') }}"
                   class="sidebar-link">
                    <i class="bi bi-plus-circle"></i> Registrar Actividad
                </a>

            @elseif(Auth::user()->rol === 'auditor')
                <div class="sidebar-section">General</div>
                <a href="{{ $safeRoute('auditor.dashboard') }}"
                   class="sidebar-link {{ Request::routeIs('auditor.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <div class="sidebar-section">Análisis</div>
                <a href="{{ $safeRoute('auditor.reportes.index') }}"
                   class="sidebar-link {{ Request::routeIs('auditor.reportes.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text"></i> Reportes
                </a>
                <a href="{{ $safeRoute('auditor.estadisticas.index') }}"
                   class="sidebar-link {{ Request::routeIs('auditor.estadisticas.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line"></i> Estadísticas
                </a>
            @endif
        @endauth
    </div>

    <div class="sidebar-footer">
        @auth
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <div style="overflow:hidden">
                <div class="text-white fw-semibold" style="font-size:.82rem;line-height:1.2">
                    {{ Auth::user()->name }}
                </div>
                @php
                    $badges = ['admin'=>'danger','vendedor'=>'success','auditor'=>'warning'];
                    $badge  = $badges[Auth::user()->rol] ?? 'secondary';
                    $labels = ['admin'=>'Administrador','vendedor'=>'Vendedor','auditor'=>'Auditor'];
                @endphp
                <span class="badge bg-{{ $badge }}" style="font-size:.65rem">
                    {{ $labels[Auth::user()->rol] ?? Auth::user()->rol }}
                </span>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-sm btn-outline-light w-100">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
            </button>
        </form>
        @endauth
    </div>
</nav>

{{-- ═══ CONTENIDO ═══ --}}
<div id="content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" id="menuToggle">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h6 class="mb-0 fw-bold text-dark">@yield('titulo', 'Dashboard')</h6>
        </div>
        <span class="text-muted small">{{ now()->format('d/m/Y') }}</span>
    </div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('contenido')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('menuToggle')?.addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>
@yield('scripts')
</body>
</html>
