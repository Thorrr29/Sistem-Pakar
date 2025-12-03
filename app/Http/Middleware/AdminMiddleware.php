<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Hanya izinkan user yang sudah login dan memiliki role admin.
        if (! $user || ! $user->is_admin) {
            abort(403, 'Anda tidak memiliki hak akses sebagai admin.');
        }

        return $next($request);
    }
}
