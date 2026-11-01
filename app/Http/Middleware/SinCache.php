<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SinCache
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1) Primero dejamos que Laravel arme la página
        $respuesta = $next($request);

        // 2) Luego le agregamos cabeceras para que el navegador no guarde copia
        $respuesta->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $respuesta->headers->set('Pragma', 'no-cache');
        $respuesta->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        // 3) Entregamos la página ya con las cabeceras
        return $respuesta;
    }
}
