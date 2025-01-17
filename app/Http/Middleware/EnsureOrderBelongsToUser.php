<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrderBelongsToUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $order = $request->route('order');

        // Pastikan order milik user yang sedang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order');
        }

        return $next($request);
    }
}