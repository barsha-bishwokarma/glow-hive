<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->role !== 'customer') {
            return response()->json([
                'success' => false,
                'message' => 'Only customers can access this.'
            ]);
        }
        return $next($request);
    }
}
