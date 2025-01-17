<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (! $request->user() || ! $request->user()->hasRole($role)) {
            abort(403);
        }

        return $next($request);
    }
}