<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerificarToken
{
    public function handle($request, Closure $next)
    {

        // VERIFICAR QUE TOKEN SEA VALIDO

        try {
            // Intenta verificar el token
            JWTAuth::parseToken()->authenticate();

            // Token válido, continúa con la lógica de la solicitud
            return $next($request);

        } catch (TokenInvalidException $e) {
            // Captura la excepción cuando el token no es válido
            return response()->json([
                'success' => -1, // PUEDE SER CUAL QUIER NUMERO, ESTE CASO ES NEGATIVO
                'error' => 'Token no válido'],
                401);
        }
    }
}
