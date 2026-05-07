<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — SMP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh; display: flex;
            align-items: center; justify-content: center;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4f46e5 100%);
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            width: 100%; max-width: 420px;
            background: #fff; border-radius: 20px;
            padding: 2.5rem; box-shadow: 0 25px 60px rgba(0,0,0,.25);
        }
        .logo { width:64px;height:64px;background:linear-gradient(135deg,#4f46e5,#818cf8);
                border-radius:18px;display:flex;align-items:center;
                justify-content:center;margin:0 auto 1.25rem;font-size:1.75rem;color:#fff; }
        .form-control { border-radius:10px; padding:.7rem 1rem;
                        border:1.5px solid #e2e8f0; font-size:.9rem; }
        .form-control:focus { border-color:#4f46e5; box-shadow:0 0 0 3px rgba(79,70,229,.15); }
        .input-icon { border-radius:10px 0 0 10px; background:#f8fafc;
                      border:1.5px solid #e2e8f0; border-right:0; color:#94a3b8; }
        .input-group .form-control { border-radius:0 10px 10px 0; border-left:0; }
        .btn-login { background:linear-gradient(135deg,#4f46e5,#6366f1);
                     border:none; border-radius:10px; padding:.75rem;
                     font-weight:600; transition:opacity .2s; }
        .btn-login:hover { opacity:.9; }
        .demo-box { background:#f8fafc; border-radius:10px;
                    padding:.85rem 1rem; font-size:.8rem; color:#64748b; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="logo"><i class="bi bi-graph-up-arrow"></i></div>
    <h4 class="text-center fw-bold mb-1">Sistema de Medición</h4>
    <p class="text-center text-muted mb-4" style="font-size:.875rem">de Productividad</p>

    @if($errors->any())
        <div class="alert alert-danger py-2" style="border-radius:10px;font-size:.875rem">
            <i class="bi bi-exclamation-circle me-1"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.875rem">Correo electrónico</label>
            <div class="input-group">
                <span class="input-group-text input-icon"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" class="form-control"
                       placeholder="tu@correo.com"
                       value="{{ old('email') }}" required autofocus>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:.875rem">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text input-icon"><i class="bi bi-lock"></i></span>
                <input type="password" name="password" class="form-control"
                       placeholder="••••••••" required>
            </div>
        </div>
        <button type="submit" class="btn btn-login btn-primary w-100 text-white">
            <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
        </button>
    </form>

    <div class="demo-box mt-4">
        <div class="mb-1"><strong>Cuentas de prueba</strong> — contraseña: <code>Admin1234</code></div>
        <div>🔴 <code>admin@productividad.com</code></div>
        <div>🟢 <code>vendedor@productividad.com</code></div>
        <div>🟡 <code>auditor@productividad.com</code></div>
        <div>🔵 <code>ana@productividad.com</code></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>