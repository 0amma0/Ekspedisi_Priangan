<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$peran): Response
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, $peran)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Anda tidak memiliki akses ke fitur ini.'], 403);
            }
            abort(403, 'Anda tidak memiliki akses ke fitur ini.');
        }
        return $next($request);
    }
}
