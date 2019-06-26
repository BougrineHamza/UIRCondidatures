<?php

namespace App\Http\Middleware;

use Closure;
use App\Admin;

class Auth_Admin
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
        if ($request->api_token && Admin::where('api_token', $request->api_token)->first()) {
            return $next($request);
        } else {
            return redirect('/gestion/connexion');
        }
    }
}
