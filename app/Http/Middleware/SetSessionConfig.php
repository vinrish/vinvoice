<?php

namespace App\Http\Middleware;

use Closure;
use Config;
use Illuminate\Http\Request;

class SetSessionConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('store') || $request->is('store/*')) {
            Config::set('session.path', '/store');
            Config::set('session.cookie', 'store_session');
        } else {
            Config::set('session.path', '/');
            Config::set('session.cookie', 'web_session');
        }

        return $next($request);
    }
}
