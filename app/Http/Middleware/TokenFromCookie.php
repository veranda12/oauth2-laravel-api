<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Log;
use Symfony\Component\HttpFoundation\Response;

class TokenFromCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('[TokenFromCookie] Middleware triggered. Cookie: ');
        if ($request->cookies->has('access_token')) {
            $token = $request->cookie('access_token');   
            $request->headers->set('Authorization', 'Bearer ' . $token); 
            Log::info('Token from cookie: ' . $token);
        } else { 
            Log::info('Token not found in cookie');
        }

        return $next($request);
    }
}
