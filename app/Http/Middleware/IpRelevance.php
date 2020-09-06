<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

class IpRelevance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, string $configKey)
    {
        if (config('app.env') == 'testing' || config('app.env') == 'local') {
            return $next($request);
        }

        $whitelist = config($configKey);

        if (!$whitelist) {
            return $next($request);
        }

        if (!is_array($whitelist)) {
            return $next($request);
        }

        if (in_array($request->ip(), $whitelist)) {
            return $next($request);
        }

        throw new AuthenticationException();
    }
}
