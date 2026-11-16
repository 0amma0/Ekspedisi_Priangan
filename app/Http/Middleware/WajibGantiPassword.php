<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WajibGantiPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->must_change_password) {
            return response()->json([
                'message' => 'Anda wajib mengganti password terlebih dahulu.',
                'code' => 'must_change_password',
            ], 403);
        }
        return $next($request);
    }
}
