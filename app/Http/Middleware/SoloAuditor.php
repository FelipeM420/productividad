<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SoloAuditor
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || Auth::user()->rol !== 'auditor') {
            abort(403, 'Acceso denegado. Se requiere rol de Auditor.');
        }
        return $next($request);
    }
}