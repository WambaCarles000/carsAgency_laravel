<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Vérification de l'existence du jeton dans le header Authorization
        if (!$request->header('Authorization')) {
            throw new \App\Exceptions\UnauthenticatedException();
        }

        // Extraction du jeton du header
        $token = str_replace('Bearer ', '', $request->header('Authorization'));

        // Vérification du jeton avec Sanctum
        if (!Auth::guard('sanctum')->checkToken($token)) {
            throw new \App\Exceptions\UnauthenticatedException();
        }

        return $next($request);
    }
}
