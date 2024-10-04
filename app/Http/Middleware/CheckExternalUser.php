<?php

namespace App\Http\Middleware;

use Closure;

class CheckExternalUser
{
    public function handle($request, Closure $next)
    {
        if ($request->user() && $request->user()->hasPermissionTo('GUEST - Login')) {
            return redirect()->route('external.dashboard');
        }

        return $next($request);
    }
}
