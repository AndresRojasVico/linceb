<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verificamos si el usuario está autenticado y si tiene el rol correcto
        // Usamos la relación 'role' que ya tienes definida en tu modelo User
        if (Auth::check() && Auth::user()->role && Auth::user()->role->name === 'Super Admin') {
            return $next($request);
        }
        // Si no es Super Admin, denegamos el acceso
        abort(403, 'Acceso restringido a Super Administradores.');
    }
}
