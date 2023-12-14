<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class IsSeller
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->user_type == 'seller'  && !Auth::user()->banned) {
            return $next($request);
        }
        elseif(Auth::check() && Auth::user()->user_type == 'staff'  && !Auth::user()->banned)
        {
            $roles = Auth::user()->roles;
            if(is_null($roles[0]->created_by))
            {
                abort(404);
            }
            else
            {
                return $next($request);
            }
        }
        else
        {
            abort(404);
        }
    }
}
