<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if(!auth()->check()) {
            return redirect('/login');
        }
          if (auth()->user()->force_password_change === 1) {
            return redirect('/reset-password');
         }
        return collect($roles)->contains(auth()->user()->role) ? $next($request) : redirect('/login');
    }
}
