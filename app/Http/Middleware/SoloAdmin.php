<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SoloAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->rol !== 'admin') {
            abort(403, 'Acceso denegado. Se requiere rol de Administrador.');
        }
        return $next($request);
    }
}