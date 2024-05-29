<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Obtener el idioma de la solicitud o usar 'es' por defecto
        $locale = $request->get('lang', 'es');
        // Configurar el idioma de la aplicación
        App::setLocale($locale);

        return $next($request);
    }
}
